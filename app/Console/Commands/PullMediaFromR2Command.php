<?php

namespace App\Console\Commands;

use Aws\S3\S3Client;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;

class PullMediaFromR2Command extends Command
{
    protected $signature = 'storage:pull-from-r2
                            {--disk=r2_public : Cloud disk in config/filesystems.php}
                            {--prefix=products/ : Directory prefix on R2}
                            {--target=public : Local disk name to receive files}
                            {--concurrency=10 : Number of parallel download workers}
                            {--force : Force overwrite even if local file exists with matching size}';

    protected $description = 'Kéo hình ảnh/media từ Cloudflare R2 về local đa luồng, hỗ trợ resume, không tràn RAM';

    public function handle(): int
    {
        @ini_set('max_execution_time', '0');
        @set_time_limit(0);

        $diskName = (string) $this->option('disk');
        $prefix = trim((string) $this->option('prefix'), '/\\');
        if (! empty($prefix)) {
            $prefix .= '/';
        }
        $concurrency = max(1, (int) $this->option('concurrency'));
        $force = (bool) $this->option('force');

        $diskConfig = config("filesystems.disks.{$diskName}");
        if (! $diskConfig) {
            $this->error("Không tìm thấy cấu hình disk [{$diskName}] trong config/filesystems.php!");
            return self::FAILURE;
        }

        $bucket = (string) ($diskConfig['bucket'] ?? '');
        if (empty($bucket)) {
            $this->error("Chưa cấu hình bucket cho disk [{$diskName}]!");
            return self::FAILURE;
        }

        $this->info("=================================================");
        $this->info("  PULL MEDIA TỪ CLOUDFLARE R2 VỀ LOCAL STORAGE   ");
        $this->info("=================================================");
        $this->line("Cloud Disk  : {$diskName} ({$bucket})");
        $this->line("Prefix      : " . ($prefix ?: '[Toàn bộ bucket]'));
        $this->line("Concurrency : {$concurrency} luồng");
        $this->line("Chế độ Force: " . ($force ? 'BẬT (Ghi đè)' : 'TẮT (Chỉ tải file mới/thiếu)'));
        $this->newLine();

        $s3 = new S3Client([
            'version' => 'latest',
            'region' => $diskConfig['region'] ?? 'auto',
            'endpoint' => $diskConfig['endpoint'] ?? null,
            'use_path_style_endpoint' => $diskConfig['use_path_style_endpoint'] ?? false,
            'credentials' => [
                'key' => $diskConfig['key'],
                'secret' => $diskConfig['secret'],
            ],
        ]);

        $localRoot = realpath(storage_path('app/public')) ?: storage_path('app/public');

        $this->info("Đang quét danh mục file trên Cloudflare R2...");
        try {
            $paginator = $s3->getPaginator('ListObjectsV2', [
                'Bucket' => $bucket,
                'Prefix' => $prefix,
            ]);
        } catch (\Throwable $e) {
            $this->error("Lỗi kết nối tới R2: " . $e->getMessage());
            return self::FAILURE;
        }

        $filesToDownload = [];
        $skippedCount = 0;
        $totalRemoteBytes = 0;

        foreach ($paginator as $result) {
            $contents = $result['Contents'] ?? [];
            foreach ($contents as $object) {
                $key = (string) ($object['Key'] ?? '');
                $size = (int) ($object['Size'] ?? 0);

                if (empty($key) || str_ends_with($key, '/')) {
                    continue;
                }

                $localPath = $localRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $key);

                if (! $force && file_exists($localPath) && filesize($localPath) === $size) {
                    $skippedCount++;
                    continue;
                }

                $filesToDownload[] = [
                    'key' => $key,
                    'size' => $size,
                    'local_path' => $localPath,
                ];
                $totalRemoteBytes += $size;
            }
        }

        $totalNeedDownload = count($filesToDownload);
        $totalScanned = $skippedCount + $totalNeedDownload;

        $this->info("Tổng file phát hiện: {$totalScanned}");
        $this->line(" - Đã có ở Local (Skip) : <comment>{$skippedCount}</comment> files");
        $this->line(" - Cần tải về           : <info>{$totalNeedDownload}</info> files (" . $this->formatBytes($totalRemoteBytes) . ")");
        $this->newLine();

        if ($totalNeedDownload === 0) {
            $this->info("Local storage đã khớp 100% với Cloudflare R2. Không cần tải thêm file nào.");
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($totalNeedDownload);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $bar->setMessage('Đang chuẩn bị...');
        $bar->start();

        $generator = function () use ($filesToDownload, $s3, $bucket) {
            foreach ($filesToDownload as $item) {
                $dir = dirname($item['local_path']);
                if (! is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }

                yield $s3->getObjectAsync([
                    'Bucket' => $bucket,
                    'Key' => $item['key'],
                    'SaveAs' => $item['local_path'],
                ]);
            }
        };

        $successCount = 0;
        $failedCount = 0;

        $pool = new EachPromise($generator(), [
            'concurrency' => $concurrency,
            'fulfilled' => function ($value) use ($bar, &$successCount) {
                $successCount++;
                $bar->setMessage("Đã tải {$successCount} files");
                $bar->advance();
            },
            'rejected' => function ($reason) use ($bar, &$failedCount) {
                $failedCount++;
                $bar->advance();
            },
        ]);

        $pool->promise()->wait();
        $bar->finish();
        $this->newLine(2);

        $this->info("=================================================");
        $this->info("                KẾT QUẢ TẢI VỀ                   ");
        $this->info("=================================================");
        $this->line("Thành công : <info>{$successCount}</info>");
        $this->line("Thất bại   : " . ($failedCount > 0 ? "<error>{$failedCount}</error>" : "0"));
        $this->line("Đã bỏ qua  : <comment>{$skippedCount}</comment>");
        $this->newLine();

        return $failedCount === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
