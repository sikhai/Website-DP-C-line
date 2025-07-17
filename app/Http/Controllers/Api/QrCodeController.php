<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateQrCodeJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
