<?php

namespace App\Console\Commands;

use Aws\CommandPool;
use Aws\ResultInterface;
use Aws\S3\S3Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Helper\ProgressBar;
use Throwable;

class SyncProductsToR2Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:sync-to-r2 
                            {--concurrency=10 : Số luồng upload đồng thời lên Cloudflare R2}
                            {--delete-local : Xóa file local sau khi ĐÃ KIỂM CHỨNG tồn tại và khớp size trên R2}
                            {--overwrite : Ép buộc ghi đè lên R2 (CHỈ ÁP DỤNG TRÊN PRODUCTION)}
                            {--dry-run : Chỉ kiểm tra và hiển thị danh sách, không copy hoặc xóa thật}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ toàn bộ ảnh sản phẩm (products/) lên Cloudflare R2 với kiến trúc Catalog O(1) và an toàn môi trường';

    /**
     * Danh mục lưu trữ file trên Cloudflare R2 trong RAM [path => size].
     */
    private array $r2Catalog = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        @ini_set('max_execution_time', '0');
        @set_time_limit(0);

        $concurrency = max(1, (int) $this->option('concurrency'));
        $shouldDeleteLocal = (bool) $this->option('delete-local');
        $isOverwriteRequested = (bool) $this->option('overwrite');
        $isDryRun = (bool) $this->option('dry-run');

        $isProduction = app()->isProduction();
        $allowOverwriteNonProd = (bool) config('filesystems.disks.r2_public.allow_overwrite', false)
            || (bool) env('R2_ALLOW_OVERWRITE', false);
        $isOverwrite = $isOverwriteRequested && ($isProduction || $allowOverwriteNonProd);

        if ($isOverwriteRequested && ! $isProduction && ! $allowOverwriteNonProd) {
            $this->warn('⚠ [BẢO VỆ MÔI TRƯỜNG] Bạn đang ở môi trường Non-Prod. Cờ --overwrite bị vô hiệu hóa (Bật R2_ALLOW_OVERWRITE=true trong .env nếu muốn ép ghi đè).');
        }

        $localDisk = Storage::disk('public');
        $r2Disk = Storage::disk('r2_public');

        $this->info('=================================================================');
        $this->info('  ĐỒNG BỘ ẢNH SẢN PHẨM LÊN CLOUDFLARE R2 (PRODUCTS -> R2)');
        $this->info('=================================================================');
        $this->line('Môi trường:           ' . ($isProduction ? '<fg=green;options=bold>PRODUCTION</>' : '<fg=yellow;options=bold>LOCAL / NON-PROD</>'));
        $this->line('Quyền ghi đè R2:      ' . ($isOverwrite ? '<info>BẬT</info>' : '<comment>TẮT (Bỏ qua file đã có)</comment>'));
        $this->line('Chế độ:               ' . ($isDryRun ? '<comment>DRY-RUN (Không thay đổi)</comment>' : '<info>THỰC THI THẬT</info>'));
        $this->line('Đa luồng Concurrency: <info>' . $concurrency . ' luồng đồng thời</info>');
        $this->line('=================================================================');

        // 1. Kiểm tra kết nối R2 chủ động
        $testKey = 'products/_test_connectivity_' . time() . '.txt';
        try {
            $r2Disk->put($testKey, 'ok');
            $r2Disk->delete($testKey);
        } catch (Throwable $e) {
            $this->error('✗ Không thể kết nối tới Cloudflare R2: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // 2. Nạp R2 Catalog O(1)
        $this->info('Đang nạp danh mục Cloudflare R2 (Prefix: products/)...');
        $s3Client = $r2Disk->getClient();
        $bucket = config('filesystems.disks.r2_public.bucket', 'crm-dpceline-public');
        $this->buildR2Catalog($s3Client, $bucket, 'products');
        $this->line(sprintf('Đã tải <info>%s</info> objects từ Cloudflare R2 vào RAM catalog.', number_format(count($this->r2Catalog))));

        // 3. Quét danh sách file local
        $localFiles = $localDisk->allFiles('products');
        $this->line(sprintf('Tìm thấy <info>%s</info> file trên Local Disk [public/products].', number_format(count($localFiles))));

        $uploadQueue = [];
        $skippedCount = 0;
        $deleteQueue = [];

        foreach ($localFiles as $file) {
            $localSize = (int) $localDisk->size($file);
            $hasR2 = isset($this->r2Catalog[$file]);
            $r2Size = $hasR2 ? $this->r2Catalog[$file]['size'] : null;

            if ($hasR2) {
                if ($isOverwrite && $localSize !== $r2Size) {
                    $uploadQueue[] = ['path' => $file, 'size' => $localSize];
                } else {
                    $skippedCount++;
                    if ($shouldDeleteLocal && $localSize === $r2Size) {
                        $deleteQueue[] = $file;
                    }
                }
            } else {
                $uploadQueue[] = ['path' => $file, 'size' => $localSize];
            }
        }

        $this->table(
            ['Phân loại', 'Số lượng'],
            [
                ['Tổng số file local', count($localFiles)],
                ['Đã có trên R2 (Bỏ qua upload)', $skippedCount],
                ['Cần upload lên R2', count($uploadQueue)],
                ['Đủ điều kiện xóa local an toàn', count($deleteQueue)],
            ]
        );

        if ($isDryRun) {
            $this->warn('(*) Chế độ DRY-RUN: Hoàn tất kiểm tra đối soát, không upload hay xóa file.');
            return Command::SUCCESS;
        }

        // 4. Upload bất đồng bộ đa luồng qua CommandPool
        if (! empty($uploadQueue)) {
            $this->info(sprintf('Bắt đầu tải lên %s file lên Cloudflare R2...', number_format(count($uploadQueue))));
            $this->uploadPool($s3Client, $bucket, $localDisk, $uploadQueue, $concurrency);
        }

        // 5. Xóa local nếu được yêu cầu và đã kiểm chứng
        if ($shouldDeleteLocal && ! empty($deleteQueue)) {
            $this->info(sprintf('Đang dọn dẹp %s file local đã lưu trữ an toàn trên R2...', number_format(count($deleteQueue))));
            foreach ($deleteQueue as $delFile) {
                $localDisk->delete($delFile);
            }
            $this->info('✓ Đã dọn dẹp ổ cứng local thành công.');
        }

        $this->info('✓ Hoàn tất đồng bộ toàn bộ ảnh sản phẩm lên Cloudflare R2!');

        return Command::SUCCESS;
    }

    /**
     * Tải R2 catalog bằng ListObjectsV2.
     */
    private function buildR2Catalog(S3Client $s3Client, string $bucket, string $directory): void
    {
        $this->r2Catalog = [];
        try {
            $paginator = $s3Client->getPaginator('ListObjectsV2', [
                'Bucket' => $bucket,
                'Prefix' => rtrim($directory, '/') . '/',
            ]);

            foreach ($paginator as $page) {
                if (! empty($page['Contents'])) {
                    foreach ($page['Contents'] as $object) {
                        $key = ltrim(str_replace('\\', '/', $object['Key']), '/');
                        $this->r2Catalog[$key] = [
                            'size' => (int) ($object['Size'] ?? 0),
                            'etag' => trim((string) ($object['ETag'] ?? ''), '"'),
                        ];
                    }
                }
            }
        } catch (Throwable $e) {
            $this->warn('Không thể nạp toàn bộ danh mục R2: ' . $e->getMessage());
        }
    }

    /**
     * Upload song song bằng CommandPool theo batch 50 file.
     */
    private function uploadPool(S3Client $s3Client, string $bucket, $localDisk, array $items, int $concurrency): void
    {
        $progressBar = new ProgressBar($this->output, count($items));
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');
        $progressBar->setMessage('Đang upload...');
        $progressBar->start();

        $chunks = array_chunk($items, 50);

        foreach ($chunks as $chunk) {
            $commands = [];
            $openStreams = [];

            foreach ($chunk as $index => $item) {
                $path = $item['path'];
                $stream = $localDisk->readStream($path);
                if (! $stream) {
                    continue;
                }

                $openStreams[$index] = $stream;
                $mimeType = $localDisk->mimeType($path) ?: 'image/webp';

                $commands[$index] = $s3Client->getCommand('PutObject', [
                    'Bucket' => $bucket,
                    'Key' => $path,
                    'Body' => $stream,
                    'ContentType' => $mimeType,
                    'CacheControl' => 'public, max-age=31536000, immutable',
                ]);
            }

            if (empty($commands)) {
                continue;
            }

            try {
                $pool = new CommandPool($s3Client, $commands, [
                    'concurrency' => $concurrency,
                    'fulfilled' => function (ResultInterface $result, $index) use (&$openStreams, $progressBar) {
                        if (isset($openStreams[$index]) && is_resource($openStreams[$index])) {
                            fclose($openStreams[$index]);
                        }
                        $progressBar->advance();
                    },
                    'rejected' => function ($reason, $index) use (&$openStreams, $progressBar) {
                        if (isset($openStreams[$index]) && is_resource($openStreams[$index])) {
                            fclose($openStreams[$index]);
                        }
                        $progressBar->advance();
                    },
                ]);

                $promise = $pool->promise();
                $promise->wait();
            } finally {
                foreach ($openStreams as $s) {
                    if (is_resource($s)) {
                        fclose($s);
                    }
                }
            }
        }

        $progressBar->finish();
        $this->newLine();
    }
}
