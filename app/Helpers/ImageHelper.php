<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ImageHelper
{
    private const MAX_ORIGINAL_NAME_LENGTH = 100;

    /**
     * Chuẩn hóa tên gốc để metadata và giao diện không bị phá bởi tên quá dài.
     */
    private static function normalizeOriginalName(string $name): string
    {
        $name = trim($name);
        if ($name === '' || mb_strlen($name) <= self::MAX_ORIGINAL_NAME_LENGTH) {
            return $name;
        }

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $suffix = $extension !== '' ? '.'.$extension : '';
        $availableLength = self::MAX_ORIGINAL_NAME_LENGTH - mb_strlen($suffix) - 3;

        if ($availableLength <= 0) {
            return mb_substr($name, 0, self::MAX_ORIGINAL_NAME_LENGTH);
        }

        $prefixLength = (int) ceil($availableLength / 2);
        $suffixLength = $availableLength - $prefixLength;
        $baseName = pathinfo($name, PATHINFO_FILENAME);

        return mb_substr($baseName, 0, $prefixLength)
            .'...'
            .($suffixLength > 0 ? mb_substr($baseName, -$suffixLength) : '')
            .$suffix;
    }

    /**
     * Xác định quyền ghi đè (Overwrite Policy):
     * Chỉ cho phép ghi đè nếu ở môi trường PRODUCTION VÀ có cờ allow_overwrite được cấu hình bật.
     */
    public static function canOverwrite(): bool
    {
        return app()->isProduction() && (bool) config('filesystems.disks.r2_public.allow_overwrite', false);
    }

    /**
     * Kiểm tra an toàn file tồn tại trên disk với xử lý timeout & exception.
     */
    public static function safeExists(string $disk, string $path): bool
    {
        try {
            return Storage::disk($disk)->exists($path);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("[ImageHelper] Không thể kiểm tra tồn tại trên disk [{$disk}] cho path [{$path}]: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tự động xác định disk lưu trữ tối ưu theo tiền tố thư mục nghiệp vụ nếu caller không truyền disk cụ thể.
     */
    public static function resolveDisk(string $directory, ?string $disk = null): string
    {
        if ($disk !== null && $disk !== '') {
            return $disk;
        }

        $r2Prefixes = [
            'expenses-',
            'print-orders',
            'project-advances',
            'salary-advances',
            'quotes',
            'products',
        ];

        foreach ($r2Prefixes as $prefix) {
            if (str_starts_with($directory, $prefix)) {
                return 'r2_public';
            }
        }

        return 'public';
    }

    /**
     * Nén và chuyển đổi hình ảnh sang định dạng WebP để tối ưu dung lượng,
     * tích hợp bảo vệ chống ghi đè từ Local (Local skip nếu đã tồn tại, Prod được override).
     *
     * @param UploadedFile $file File hình ảnh từ request
     * @param string $directory Thư mục lưu trữ (ví dụ: 'payroll-payments', 'products/marvel')
     * @param int|null $width Chiều rộng tối đa (null nếu giữ nguyên)
     * @param int|null $height Chiều cao tối đa (null nếu giữ nguyên)
     * @param int $quality Chất lượng hình ảnh (1-100)
     * @param string|null $disk Disk lưu trữ (mặc định tự động theo thư mục nghiệp vụ hoặc public)
     * @param array $options Tùy chọn bổ sung cho Flysystem/S3 (Headers, Cache-Control,...)
     * @param string|null $customFilename Tên file tùy chỉnh (nếu muốn định danh theo slug)
     * @param bool|null $overwrite Cờ ghi đè (null = tự động theo canOverwrite())
     * @return array Trả về mảng chứa thông tin path và metadata
     */
    public static function storeAsWebp(
        UploadedFile $file, 
        string $directory, 
        ?int $width = null, 
        ?int $height = null, 
        int $quality = 80, 
        ?string $disk = null,
        array $options = [],
        ?string $customFilename = null,
        ?bool $overwrite = null
    ): array {
        $disk = self::resolveDisk($directory, $disk);

        // Tạo tên file ngẫu nhiên hoặc theo customFilename
        if ($customFilename !== null && $customFilename !== '') {
            $filename = Str::slug(pathinfo($customFilename, PATHINFO_FILENAME)) . '.webp';
        } else {
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = Str::slug($filename) . '_' . time() . '_' . Str::random(5) . '.webp';
        }
        $fullPath = rtrim($directory, '/') . '/' . $filename;

        // KIỂM SOÁT BẢO VỆ MÔI TRƯỜNG (LOCAL VS PROD OVERWRITE POLICY)
        $allowOverwrite = $overwrite ?? self::canOverwrite();

        if (! $allowOverwrite && self::safeExists($disk, $fullPath)) {
            \Illuminate\Support\Facades\Log::info("[ImageHelper] Môi trường Non-Prod: File đã tồn tại trên [{$disk}:{$fullPath}]. Bỏ qua upload để bảo vệ dữ liệu PROD.");

            return [
                'path' => $fullPath,
                'disk' => $disk,
                'original_name' => self::normalizeOriginalName($file->getClientOriginalName()),
                'mime_type' => 'image/webp',
                'size' => (int) $file->getSize(),
                'skipped_upload' => true,
            ];
        }

        // CỔNG TỐI ƯU SIÊU TỐC (FAST-PATH BYPASS):
        // Nếu file đã là WebP chuẩn từ Client và dung lượng hợp lý (<= 800KB)
        // và không yêu cầu cưỡng chế resize kích thước -> Bỏ qua Intervention Image (giảm từ 1000ms xuống < 5ms)
        $isClientWebp = strtolower($file->getClientOriginalExtension()) === 'webp' || $file->getMimeType() === 'image/webp';
        if ($isClientWebp && $file->getSize() <= 800 * 1024 && ! $width && ! $height) {
            $webpData = (string) file_get_contents($file->getRealPath());
            $size = strlen($webpData);
        } else {
            // Xử lý bằng Intervention Image
            $image = Image::make($file);
            
            // Resize nếu có yêu cầu (giữ nguyên tỷ lệ)
            if ($width || $height) {
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Encode sang webp với chất lượng chỉ định
            $webpData = (string) $image->encode('webp', $quality);
            $size = strlen($webpData);
            $image->destroy();
        }

        // Cấu hình headers tối ưu cho Cloudflare R2 / S3 Object Storage và CDN caching
        $defaultOptions = [
            'ContentType' => 'image/webp',
            'CacheControl' => 'public, max-age=31536000, immutable',
        ];

        try {
            Storage::disk($disk)->put($fullPath, $webpData, array_merge($defaultOptions, $options));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("[ImageHelper] Lỗi upload lên disk [{$disk}]: " . $e->getMessage());

            // Tự động Fallback sang Local Public Disk nếu R2 lỗi mạng hoặc thiếu credentials
            if ($disk !== 'public') {
                \Illuminate\Support\Facades\Log::warning("[ImageHelper] Fallback lưu trữ vào disk local 'public' cho file [{$fullPath}]");
                Storage::disk('public')->put($fullPath, $webpData, $defaultOptions);
                $disk = 'public';
            } else {
                throw $e;
            }
        }

        return [
            'path' => $fullPath,
            'disk' => $disk,
            'original_name' => self::normalizeOriginalName($file->getClientOriginalName()),
            'mime_type' => 'image/webp',
            'size' => $size,
            'skipped_upload' => false,
        ];
    }

    /**
     * Store file directly if it's a PDF, or compress as WebP if it's an image.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param int|null $width
     * @param int|null $height
     * @param int $quality
     * @param string|null $disk
     * @param array $options
     * @return array
     */
    public static function storeFileOrWebp(
        UploadedFile $file, 
        string $directory, 
        ?int $width = null, 
        ?int $height = null, 
        int $quality = 80, 
        ?string $disk = null,
        array $options = []
    ): array {
        $disk = self::resolveDisk($directory, $disk);

        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension === 'pdf') {
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = Str::slug($filename) . '_' . time() . '_' . Str::random(5) . '.pdf';
            $fullPath = rtrim($directory, '/') . '/' . $filename;
            
            $defaultOptions = [
                'ContentType' => 'application/pdf',
                'CacheControl' => 'public, max-age=31536000, immutable',
            ];

            Storage::disk($disk)->putFileAs($directory, $file, $filename, array_merge($defaultOptions, $options));
            
            return [
                'path' => $fullPath,
                'disk' => $disk,
                'original_name' => self::normalizeOriginalName($file->getClientOriginalName()),
                'mime_type' => 'application/pdf',
                'size' => $file->getSize(),
            ];
        }

        return self::storeAsWebp($file, $directory, $width, $height, $quality, $disk, $options);
    }

    /**
     * Decode and save base64 encoded image as WebP.
     *
     * @param string $base64String
     * @param string $directory
     * @param string|null $disk
     * @param array $options
     * @return string URL of the saved image
     */
    public static function storeBase64AsWebp(
        string $base64String, 
        string $directory, 
        ?string $disk = null,
        array $options = []
    ): string {
        $disk = self::resolveDisk($directory, $disk);

        if (preg_match('/^data:image\/(\w+);base64,(.+)$/i', $base64String, $matches)) {
            $imageData = base64_decode($matches[2]);
            if ($imageData === false) {
                return '';
            }
            $filename = 'inline_' . time() . '_' . Str::random(5) . '.webp';
            $fullPath = rtrim($directory, '/') . '/' . $filename;

            try {
                $image = Image::make($imageData);
                $webpData = (string) $image->encode('webp', 80);
                $image->destroy();

                $defaultOptions = [
                    'ContentType' => 'image/webp',
                    'CacheControl' => 'public, max-age=31536000, immutable',
                ];

                Storage::disk($disk)->put($fullPath, $webpData, array_merge($defaultOptions, $options));

                return Storage::disk($disk)->url($fullPath);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Base64 image conversion error: ' . $e->getMessage());
                return '';
            }
        }

        return '';
    }
}
