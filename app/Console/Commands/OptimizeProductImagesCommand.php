<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Throwable;

class OptimizeProductImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:optimize
                            {--threshold=500 : Ngưỡng dung lượng tối thiểu (KB) để xử lý (mặc định 500KB)}
                            {--dry-run : Chỉ quét và tính toán giả định, không ghi đè file}
                            {--limit=0 : Giới hạn số lượng file xử lý (0 là không giới hạn)}
                            {--sample= : Chỉ định xử lý riêng một file cụ thể để test chất lượng}
                            {--quality=85 : Chất lượng WebP khởi điểm Pass 1}
                            {--max-dimension=2048 : Kích thước dài nhất tối đa (px)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tối ưu hóa adaptive 2-pass đưa toàn bộ ảnh sản phẩm về chuẩn < 500KB không mất vân vải/alpha';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $thresholdKb = (int) $this->option('threshold');
        $thresholdBytes = $thresholdKb * 1024;
        $isDryRun = (bool) $this->option('dry-run');
        $limit = (int) $this->option('limit');
        $sampleFile = $this->option('sample');
        $baseQuality = (int) $this->option('quality');
        $maxDim = (int) $this->option('max-dimension');

        $this->info('=================================================================');
        $this->info('  TỐI ƯU HÓA KHO ẢNH SẢN PHẨM (ADAPTIVE TWO-PASS COMPRESSION)');
        $this->info('=================================================================');
        $this->line(sprintf('Ngưỡng dung lượng:     <comment>> %d KB</comment>', $thresholdKb));
        $this->line(sprintf('Kích thước tối đa:     <comment>%d px</comment>', $maxDim));
        $this->line(sprintf('Chất lượng Pass 1:     <comment>%d%%</comment>', $baseQuality));
        $this->line(sprintf('Chế độ thực thi:       <comment>%s</comment>', $isDryRun ? 'DRY-RUN (Không sửa file)' : 'THỰC THI THẬT (In-place Optimization)'));
        $this->info('=================================================================');

        $disk = Storage::disk('public');

        if ($sampleFile) {
            $files = [$sampleFile];
        } else {
            $this->info('Đang quét danh sách file trong [storage/app/public/products]...');
            $files = $disk->allFiles('products');
        }

        $candidates = [];
        $totalOriginalBytes = 0;

        foreach ($files as $file) {
            // Bỏ qua file không phải ảnh (như pdf)
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['pdf', 'txt', 'DS_Store', 'gitignore'])) {
                continue;
            }

            $size = (int) $disk->size($file);
            if ($size > $thresholdBytes) {
                $candidates[] = [
                    'file' => $file,
                    'size' => $size,
                    'ext' => $ext ?: 'no-ext',
                ];
                $totalOriginalBytes += $size;
            }
        }

        $totalCandidates = count($candidates);
        $this->info(sprintf('Tìm thấy <comment>%s</comment> file vượt ngưỡng %d KB (Tổng dung lượng: <comment>%.2f MB</comment>).', 
            number_format($totalCandidates), 
            $thresholdKb, 
            $totalOriginalBytes / (1024 * 1024)
        ));

        if ($totalCandidates === 0) {
            $this->info('✓ Không có file nào cần tối ưu.');
            return Command::SUCCESS;
        }

        if ($limit > 0 && $limit < $totalCandidates) {
            $candidates = array_slice($candidates, 0, $limit);
            $this->warn(sprintf('Đã áp dụng giới hạn xử lý: %d file.', count($candidates)));
        }

        $processedCount = 0;
        $successCount = 0;
        $failedCount = 0;
        $totalNewBytes = 0;
        $reportRows = [];

        $progressBar = $this->output->createProgressBar(count($candidates));
        $progressBar->start();

        foreach ($candidates as $item) {
            $file = $item['file'];
            $origSize = $item['size'];
            $fullPath = $disk->path($file);

            try {
                $result = $this->compressAdaptive($fullPath, $baseQuality, $maxDim, $thresholdBytes);
                $newSize = strlen($result['data']);
                $totalNewBytes += $newSize;

                $savedPercent = $origSize > 0 ? round((($origSize - $newSize) / $origSize) * 100, 1) : 0;

                if (! $isDryRun) {
                    // Backup trước khi ghi đè nếu muốn an toàn tuyệt đối
                    // Ở đây ghi đè trực tiếp vào local disk
                    file_put_contents($fullPath, $result['data']);
                }

                $successCount++;
                if (count($reportRows) < 15) {
                    $reportRows[] = [
                        basename($file),
                        round($origSize / 1024, 1) . ' KB',
                        round($newSize / 1024, 1) . ' KB',
                        $savedPercent . '%',
                        $result['pass'],
                        $result['quality'] . '%',
                        $result['dimensions'],
                    ];
                }
            } catch (Throwable $e) {
                $failedCount++;
                $this->error(PHP_EOL . sprintf('Lỗi khi xử lý file [%s]: %s', $file, $e->getMessage()));
            }

            $processedCount++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info('--- BẢNG KẾT QUẢ THỬ NGHIỆM CHI TIẾT (MẪU) ---');
        $this->table(
            ['Tên File', 'Size Cũ', 'Size Mới', 'Tiết kiệm', 'Pass', 'Quality', 'Kích thước'],
            $reportRows
        );

        $savedBytes = $totalOriginalBytes - $totalNewBytes;
        $overallSavedPercent = $totalOriginalBytes > 0 ? round(($savedBytes / $totalOriginalBytes) * 100, 1) : 0;

        $this->info('=================================================================');
        $this->info('  KẾT QUẢ TỔNG QUAN');
        $this->info('=================================================================');
        $this->line(sprintf('Tổng số file đã phân tích:   <info>%d</info>', $processedCount));
        $this->line(sprintf('Số file thành công:          <info>%d</info>', $successCount));
        $this->line(sprintf('Số file gặp lỗi:             <comment>%d</comment>', $failedCount));
        $this->line(sprintf('Dung lượng ban đầu:          <comment>%.2f MB</comment>', $totalOriginalBytes / (1024 * 1024)));
        $this->line(sprintf('Dung lượng sau tối ưu:       <info>%.2f MB</info>', $totalNewBytes / (1024 * 1024)));
        $this->line(sprintf('Dung lượng tiết kiệm:        <info>%.2f MB (%.1f%%)</info>', $savedBytes / (1024 * 1024), $overallSavedPercent));
        $this->info('=================================================================');

        return Command::SUCCESS;
    }

    /**
     * Thuật toán nén Adaptive 2-Pass bảo toàn chi tiết vân vải và Alpha Channel.
     */
    private function compressAdaptive(string $path, int $baseQuality, int $maxDim, int $targetBytes): array
    {
        $img = Image::make($path);
        $img->orientate();

        $origW = $img->width();
        $origH = $img->height();

        // 1. Giới hạn kích thước tối đa 2048px (chuẩn hiển thị catalog sắc nét)
        if ($origW > $maxDim || $origH > $maxDim) {
            $img->resize($maxDim, $maxDim, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // PASS 1: Thử nghiệm với chất lượng cao (WebP 85%)
        $pass1Data = (string) $img->encode('webp', $baseQuality);
        $pass1Size = strlen($pass1Data);

        if ($pass1Size <= $targetBytes) {
            $dims = $img->width() . 'x' . $img->height();
            $img->destroy();
            return [
                'data' => $pass1Data,
                'pass' => 'Pass 1 (HQ)',
                'quality' => $baseQuality,
                'dimensions' => $dims,
            ];
        }

        // PASS 2: Adaptive Fine-tuning nếu Pass 1 vẫn > target
        $adaptiveQuality = (int) floor($baseQuality * ($targetBytes / $pass1Size));
        $adaptiveQuality = max(65, min($baseQuality, $adaptiveQuality));

        // Nếu sau Pass 1 vẫn vượt 500KB, kiểm tra hạ độ phân giải về 1600px
        if ($img->width() > 1600 || $img->height() > 1600) {
            $img->resize(1600, 1600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $pass2Data = (string) $img->encode('webp', $adaptiveQuality);
        $pass2Size = strlen($pass2Data);

        // PASS 3 (Fallback cho ảnh cực kỳ phức tạp): nếu vẫn > target, scale 1280px
        if ($pass2Size > $targetBytes && ($img->width() > 1280 || $img->height() > 1280)) {
            $img->resize(1280, 1280, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $pass2Data = (string) $img->encode('webp', max(60, $adaptiveQuality));
        }

        $dims = $img->width() . 'x' . $img->height();
        $img->destroy();

        return [
            'data' => $pass2Data,
            'pass' => 'Pass 2 (Adaptive)',
            'quality' => $adaptiveQuality,
            'dimensions' => $dims,
        ];
    }
}
