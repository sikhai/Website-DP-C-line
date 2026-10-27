<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        // Lấy mã sản phẩm làm nội dung QR
        $code = $product->product_code;

        // Tạo tên file QR PNG
        $filename = Str::slug($code) . '.png';
        $path = "qrcodes/{$filename}";

        try {
            if (!Storage::disk('public')->exists($path)) {
                if (extension_loaded('imagick')) {
                    $qrPng = QrCode::format('png')->size(300)->generate($code);
                    Storage::disk('public')->put($path, $qrPng);
                } else {
                    $path = "qrcodes/{$filename}.svg";
                    $qrSvg = QrCode::format('svg')->size(300)->generate($code);
                    Storage::disk('public')->put($path, $qrSvg);
                }
            }

            // (Tuỳ chọn) Lưu path vào DB nếu có cột qr_code_path
            $product->qr_code_path = $path;
            $product->saveQuietly(); // tránh gọi lại observer
        } catch (\Throwable $e) {
            \Log::warning("QR Code generation failed for product {$code}: " . $e->getMessage());
        }

        \App\Services\PrintProductSearchRevision::bumpAfterCommit();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        \App\Services\PrintProductSearchRevision::bumpAfterCommit();
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        \App\Services\PrintProductSearchRevision::bumpAfterCommit();
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        \App\Services\PrintProductSearchRevision::bumpAfterCommit();
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        \App\Services\PrintProductSearchRevision::bumpAfterCommit();
    }
}