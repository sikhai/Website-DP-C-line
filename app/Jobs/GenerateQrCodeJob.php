<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateQrCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $productCode;

    public function __construct(string $productCode)
    {
        $this->productCode = $productCode;
    }

    public function handle(): void
    {
        try {
            $filename = 'qrcodes/' . Str::slug($this->productCode) . '.png';
            $path = storage_path('app/public/' . $filename);

            if (file_exists($path)) {
                Log::info("QR already exists: {$filename}");
                return;
            }

            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            QrCode::format('png')
                ->size(300)
                ->margin(1)
                ->generate($this->productCode, $path);

            Log::info("QR generated: {$filename}");
        } catch (\Throwable $e) {
            Log::error("QR generation failed for {$this->productCode}: " . $e->getMessage());
        }
    }
}

