<?php

use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Storage;

if (!function_exists('storage_image_url')) {
    /**
     * Tạo URL đầy đủ cho ảnh lưu trong thư mục storage hoặc Cloudflare R2 CDN.
     *
     * @param string|null $path
     * @return string|null
     */
    function storage_image_url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'file://') || str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return $path;
        }

        $cleanPath = ltrim(preg_replace('#^/?storage/#', '', $path), '/');
        $cleanPath = str_replace('\\', '/', $cleanPath);

        // Phân giải disk qua ImageHelper
        $disk = ImageHelper::resolveDisk($cleanPath);
        if ($disk === 'r2_public') {
            $publicUrl = rtrim((string) config('filesystems.disks.r2_public.url'), '/');
            if ($publicUrl !== '') {
                return $publicUrl . '/' . $cleanPath;
            }
            return Storage::disk('r2_public')->url($cleanPath);
        }

        $baseUrl = config('app.web_url') ?: env('APP_WEB_URL') ?: config('app.url') ?: env('APP_URL');
        if (empty($baseUrl) && function_exists('url')) {
            $baseUrl = url('/');
        }

        return rtrim((string) $baseUrl, '/') . '/storage/' . $cleanPath;
    }
}
