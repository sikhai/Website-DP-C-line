<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateQrCodeJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\JsonResponse;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class QrCodeController extends Controller
{
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'product_codes' => 'required|array|min:1',
            'product_codes.*' => 'required|string|max:191',
        ]);

        $results = [];

        foreach ($validated['product_codes'] as $productCode) {
            $filename = 'qrcodes/' . Str::slug($productCode) . '.png';
            $path = storage_path('app/public/' . $filename);

            if (file_exists($path)) {
                // Đã tồn tại, trả path luôn
                $results[$productCode] = $filename;
            } else {
                // Chưa có → dispatch job
                GenerateQrCodeJob::dispatch($productCode);
                $results[$productCode] = $filename;
            }
        }

        return response()->json([
            'message' => 'QR generation jobs dispatched.',
            'data' => $results,
        ]);
    }

    public function updateAllQrPaths(): JsonResponse
    {
        $updated = 0;

        Product::select('id', 'product_code') // Chỉ lấy field cần dùng
            ->chunk(500, function ($products) use (&$updated) {
                $data = [];

                foreach ($products as $product) {
                    $qrPath = 'qrcodes/' . Str::slug($product->product_code) . '.png';

                    $data[] = [
                        'id' => $product->id,
                        'qr_code_path' => $qrPath,
                    ];
                }

                // Bulk update via CASE WHEN
                $ids = collect($data)->pluck('id')->toArray();

                $cases = '';
                foreach ($data as $item) {
                    $cases .= "WHEN {$item['id']} THEN '{$item['qr_code_path']}' ";
                }

                $idsStr = implode(',', $ids);

                // Raw SQL for efficient update
                DB::update("UPDATE products SET qr_code_path = CASE id {$cases} END WHERE id IN ({$idsStr})");

                $updated += count($data);
            });

        return response()->json([
            'message' => 'All QR paths updated successfully.',
            'total_updated' => $updated
        ]);
    }


    // private function generateQrCode(string $data, array $options): string
    // {
    //     $filename = $options['filename'] ?? 'qrcodes/' . Str::slug($data) . '.png';
    //     $size = $options['size'] ?? 300;
    //     $margin = $options['margin'] ?? 1;

    //     $path = storage_path('app/public/' . $filename);

    //     if (!file_exists(dirname($path))) {
    //         mkdir(dirname($path), 0755, true);
    //     }

    //     QrCode::format('png')
    //         ->size($size)
    //         ->margin($margin)
    //         ->generate($data, $path);

    //     return $filename;
    // }
}
