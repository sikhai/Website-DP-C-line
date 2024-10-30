<?php

namespace App\Services;

use App\Models\Attribute;

class ProductService
{
    public function getAttributesWithProductCount()
    {
        $attributes = Attribute::with('products')->get();
        $result_attributes = [];

        if (!$attributes) {
            return $result_attributes;
        }

        foreach ($attributes as $attribute) {
            if (!$attribute['value']) {
                continue;
            }

            $decodedValues = json_decode($attribute['value'], true);
            $productIds = $attribute->products->pluck('id')->toArray(); // Lấy danh sách id sản phẩm

            foreach ($decodedValues as $item) {
                $name = $item['name'];
                $value = $item['value'];

                // Khởi tạo mảng nếu chưa tồn tại
                if (!isset($result_attributes[$name])) {
                    $result_attributes[$name] = [];
                }

                if (!isset($result_attributes[$name][$value])) {
                    $result_attributes[$name][$value] = [];
                }

                // Thêm danh sách id vào phần tử list_ids
                $result_attributes[$name][$value]['list_ids'] = $productIds;
            }
        }

        return $result_attributes;
    }
}
