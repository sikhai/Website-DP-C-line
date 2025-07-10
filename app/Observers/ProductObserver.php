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

        // Nếu chưa có thì tạo mới
        if (!Storage::disk('public')->exists($path)) {
            $qrPng = QrCode::format('png')->size(300)->generate($code);
            Storage::disk('public')->put($path, $qrPng);
        }

        // (Tuỳ chọn) Lưu path vào DB nếu có cột qr_code_path
        $product->qr_code_path = $path;
        $product->saveQuietly(); // tránh gọi lại observer
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
