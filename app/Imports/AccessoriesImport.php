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
        return new Accessory([
            'category_id'       => $row['category_id'],
            'product_code' => !empty(trim($row['product_code'])) ? trim($row['product_code']) : null,
            'name'              => $row['name'],
            'price'             => $row['price'],
            'dealer_price'      => $row['dealer_price'],
            'stock_quantity'    => $row['stock_quantity'],
            'measure'           => $row['measure'],
            'description'       => $row['description'] ?? '',
            'short_description' => $row['short_description'] ?? '',
            'slug'              => Str::slug($row['name'] . '-' . $row['product_code']),
        ]);
    }
}