<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:191',
        ]);

        $productCode = $validated['product_code'];

        // Tạo tên file QR như yêu cầu ban đầu
        $filename = 'qrcodes/' . Str::slug($productCode) . '.png';

        $this->generateQrCode($productCode, [
            'filename' => $filename,
            'size' => 300,
            'margin' => 1,
        ]);

        return response()->json([
            'message' => 'QR Code generated successfully.',
            'file_path' => $filename,
            'url' => asset('storage/' . $filename),
        ]);
    }

    private function generateQrCode(string $data, array $options): string
    {
        $filename = $options['filename'] ?? 'qrcodes/' . Str::slug($data) . '.png';
        $size = $options['size'] ?? 300;
        $margin = $options['margin'] ?? 1;

        $path = storage_path('app/public/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        QrCode::format('png')
            ->size($size)
            ->margin($margin)
            ->generate($data, $path);

        return $filename;
    }
}
