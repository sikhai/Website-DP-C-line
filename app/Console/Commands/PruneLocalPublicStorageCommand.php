<?php

namespace App\Console\Commands;

use Aws\S3\S3Client;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class PruneLocalPublicStorageCommand extends Command
{
    protected $signature = 'storage:prune-local
                            {--disk=r2_public : Cloud disk in config/filesystems.php}
                            {--prefix=products/ : Relative path inside storage/app/public/ to prune}
                            {--keep-days=7 : Minimum age of local file in days based on mtime (grace period)}
                            {--verify-checksum : Strict byte-for-byte check: compare local MD5 with R2 ETag}
                            {--dry-run : Simulate and preview files to be pruned without deleting}
                            {--clean-empty-dirs : Automatically remove empty subdirectories after pruning}
                            {--force : Force execution without interactive confirmation}';

    protected $description = 'Giải phóng dung lượng local an toàn: Chỉ xóa sau khi đã xác thực file tồn tại và toàn vẹn trên Cloudflare R2';

    protected array $allowedPrefixes = [
        'products',
        'samples',
        'designs',
        'fabrics',
        'attachments',
    ];

    public function handle(): int
    {
        @ini_set('max_execution_time', '0');
        @set_time_limit(0);

        $diskName = (string) $this->option('disk');
        $prefix = trim((string) $this->option('prefix'), '/\\');
        $keepDays = max(0, (int) $this->option('keep-days'));
        $verifyChecksum = (bool) $this->option('verify-checksum');
        $dryRun = (bool) $this->option('dry-run');
        $cleanEmptyDirs = (bool) $this->option('clean-empty-dirs');
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

        $localRoot = realpath(storage_path('app/public'));
        if (! $localRoot || ! is_dir($localRoot)) {
            $this->error("Không tìm thấy thư mục storage/app/public cục bộ!");
            return self::FAILURE;
        }

        $targetDir = realpath($localRoot . DIRECTORY_SEPARATOR . $prefix);
        if (! $targetDir || ! str_starts_with($targetDir, $localRoot)) {
            $this->error("LỖI BẢO MẬT: Đường dẫn prefix không hợp lệ hoặc vượt ngoài boundary: [{$prefix}]");
            return self::FAILURE;
        }

        $basePrefix = explode('/', str_replace('\\', '/', $prefix))[0];
        if (! in_array($basePrefix, $this->allowedPrefixes, true)) {
            $this->error("Tiền tố [{$basePrefix}] không nằm trong Whitelist an toàn: " . implode(', ', $this->allowedPrefixes));
            return self::FAILURE;
        }

        $this->info("=================================================");
        $this->info("   GIẢI PHÓNG BỘ NHỚ LOCAL SERVER (AN TOÀN 5 TẦNG) ");
        $this->info("=================================================");
        $this->line("Thư mục quét     : {$targetDir}");
        $this->line("Cloud Disk       : {$diskName} ({$bucket})");
        $this->line("Grace Period     : " . ($keepDays > 0 ? "{$keepDays} ngày (chỉ xóa file cũ hơn {$keepDays} ngày)" : "0 ngày (quét tất cả file)"));
        $this->line("Verify Checksum  : " . ($verifyChecksum ? 'BẬT (Khớp MD5 với R2 ETag)' : 'TẮT (So sánh ContentLength)'));
        $this->line("Chế độ           : " . ($dryRun ? '<comment>DRY-RUN (GIẢ LẬP - KHÔNG XÓA)</comment>' : '<fg=red;options=bold>THỰC THI THẬT</>'));
        $this->newLine();

        if (! $dryRun && ! $force) {
            if (! $this->confirm("CẢNH BÁO: Thao tác này sẽ xóa các file local đã được lưu trữ an toàn trên Cloudflare R2. Tiếp tục?", false)) {
                $this->warn("Đã hủy thao tác.");
                return self::SUCCESS;
            }
        }

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

        $this->info("Đang nạp danh mục R2 Catalog vào bộ nhớ để đối soát O(1)...");
        $cloudCatalog = [];
        try {
            $r2Prefix = str_replace('\\', '/', $prefix);
            if (! empty($r2Prefix)) {
                $r2Prefix .= '/';
            }
            $paginator = $s3->getPaginator('ListObjectsV2', [
                'Bucket' => $bucket,
                'Prefix' => $r2Prefix,
            ]);

            foreach ($paginator as $result) {
                foreach ($result['Contents'] ?? [] as $object) {
                    $key = (string) ($object['Key'] ?? '');
                    if (! empty($key) && ! str_ends_with($key, '/')) {
                        $cloudCatalog[$key] = [
                            'size' => (int) ($object['Size'] ?? 0),
                            'etag' => trim((string) ($object['ETag'] ?? ''), '"'),
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->error("Không thể kết nối đến Cloudflare R2 để nạp Catalog: " . $e->getMessage());
            return self::FAILURE;
        }

        $this->info("Đã nạp " . count($cloudCatalog) . " file từ R2. Đang quét và kiểm định file local...");

        $finder = new Finder();
        $finder->files()
            ->in($targetDir)
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);

        if ($keepDays > 0) {
            $threshold = time() - ($keepDays * 86400);
            $finder->filter(fn (SplFileInfo $file) => $file->getMTime() <= $threshold);
        }

        $pruneCandidates = [];
        $skippedNotOnCloud = 0;
        $skippedSizeMismatch = 0;
        $skippedChecksumMismatch = 0;
        $totalBytes = 0;
        $totalScanned = 0;

        $bar = $this->output->createProgressBar();
        $bar->start();

        foreach ($finder as $file) {
            $totalScanned++;
            $bar->advance();

            $fullLocalPath = $file->getRealPath();
            $relSub = str_replace($localRoot, '', $fullLocalPath);
            $relativeKey = ltrim(str_replace('\\', '/', $relSub), '/');
            $localSize = $file->getSize();

            // CỔNG 1: Kiểm tra tồn tại trên Cloud Catalog
            if (! isset($cloudCatalog[$relativeKey])) {
                $skippedNotOnCloud++;
                continue;
            }

            $remoteInfo = $cloudCatalog[$relativeKey];

            // CỔNG 2: Kiểm tra kích thước chính xác từng byte
            if ($remoteInfo['size'] !== $localSize) {
                $skippedSizeMismatch++;
                continue;
            }

            // CỔNG 3: Kiểm tra Checksum (nếu bật cờ --verify-checksum)
            if ($verifyChecksum) {
                $remoteEtag = $remoteInfo['etag'];
                // Nếu single-part upload MD5 (32 hex characters không chứa dấu gạch)
                if (strlen($remoteEtag) === 32 && ! str_contains($remoteEtag, '-')) {
                    $localMd5 = md5_file($fullLocalPath);
                    if ($localMd5 !== $remoteEtag) {
                        $skippedChecksumMismatch++;
                        continue;
                    }
                }
            }

            $pruneCandidates[] = [
                'path' => $fullLocalPath,
                'key' => $relativeKey,
                'size' => $localSize,
            ];
            $totalBytes += $localSize;
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Tổng file quét', 'File an toàn đủ điều kiện xóa', 'Chưa có trên Cloud (Giữ lại)', 'Sai lệch Size/MD5 (Giữ lại)', 'Dung lượng giải phóng'],
            [
                [
                    $totalScanned,
                    count($pruneCandidates),
                    $skippedNotOnCloud,
                    $skippedSizeMismatch + $skippedChecksumMismatch,
                    $this->formatBytes($totalBytes),
                ],
            ]
        );

        if (count($pruneCandidates) === 0) {
            $this->info("Không có file nào đủ điều kiện giải phóng dung lượng.");
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info("<comment>[DRY-RUN HOÀN TẤT]</comment> Sẽ giải phóng {$this->formatBytes($totalBytes)} dung lượng khi chạy lệnh thật.");
            return self::SUCCESS;
        }

        $deletedCount = 0;
        foreach ($pruneCandidates as $candidate) {
            if (@unlink($candidate['path'])) {
                $deletedCount++;
            }
        }

        $this->info("ĐÃ GIẢI PHÓNG THÀNH CÔNG: {$deletedCount} files (" . $this->formatBytes($totalBytes) . ").");

        if ($cleanEmptyDirs) {
            $this->cleanEmptyDirectories($targetDir);
            $this->info("Đã dọn dẹp các thư mục rỗng.");
        }

        return self::SUCCESS;
    }

    private function cleanEmptyDirectories(string $dir): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir()) {
                @rmdir($fileinfo->getRealPath());
            }
        }
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
