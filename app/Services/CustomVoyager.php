<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Voyager as BaseVoyager;

class CustomVoyager extends BaseVoyager
{
    /**
     * Resolve image URL: Prioritizes Cloudflare R2 CDN for registered prefixes (e.g. products/),
     * and seamlessly falls back to base Voyager storage logic.
     *
     * @param string|null $file
     * @param string $default
     * @return string
     */
    public function image($file, $default = '')
    {
        if (empty($file)) {
            return $default;
        }

        // Nếu file là full URL hoặc base64 data
        if (str_starts_with($file, 'file://') || str_starts_with($file, 'http://') || str_starts_with($file, 'https://') || str_starts_with($file, 'data:')) {
            return $file;
        }

        $cleanPath = ltrim(preg_replace('#^/?storage/#', '', (string)$file), '/');
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

        return parent::image($file, $default);
    }
}
