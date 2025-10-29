<?php

namespace App\Imports;

use App\Models\Accessory;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AccessoriesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Chuẩn hóa dữ liệu số
        $price         = $this->sanitizeNumber($row['price'] ?? null);
        $dealerPrice   = $this->sanitizeNumber($row['dealer_price'] ?? null);
        $stockQuantity = $this->sanitizeNumber($row['stock_quantity'] ?? null);

        // Tạo slug & product_code cơ bản
        $baseCode = Str::slug(($row['name'] ?? 'unknown') . '-' . ($row['collection'] ?? ''));
        $baseSlug = $baseCode;

        // Tạo mã duy nhất cho cả product_code và slug
        $uniqueCode = $this->generateUniqueValue('product_code', $baseCode);
        $uniqueSlug = $this->generateUniqueValue('slug', $baseSlug);

        return new Accessory([
            'category_id'         => $row['category_id'] ?? null,
            'delivery_vendors_id' => $row['delivery_vendors_id'] ?? null,
            'product_code'        => $uniqueCode,
            'name'                => trim(($row['name'] ?? '') . ' - ' . ($row['collection'] ?? '')),
            'price'               => $price,
            'dealer_price'        => $dealerPrice,
            'stock_quantity'      => $stockQuantity,
            'measure'             => $row['measure'] ?? '',
            'description'         => $row['description'] ?? '',
            'short_description'   => $row['short_description'] ?? '',
            'slug'                => $uniqueSlug,
        ]);
    }

    /**
     * Xóa ký tự không phải số và gán mặc định = 0
     */
    private function sanitizeNumber($value): int
    {
        $clean = preg_replace('/\D/', '', $value ?? '');
        return !empty($clean) ? (int) $clean : 0;
    }

    /**
     * Tạo giá trị duy nhất cho một cột bất kỳ (slug, product_code)
     */
    private function generateUniqueValue(string $column, string $baseValue): string
    {
        $value = $baseValue;
        $counter = 1;

        while (Accessory::where($column, $value)->exists()) {
            $value = $baseValue . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $value;
    }
}
