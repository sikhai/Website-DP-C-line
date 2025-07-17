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

        $dispatched = [];

        foreach ($validated['product_codes'] as $productCode) {
            GenerateQrCodeJob::dispatch($productCode);
            $dispatched[] = $productCode;
        }

        return response()->json([
            'message' => 'QR generation jobs dispatched.',
            'data' => $dispatched,
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
