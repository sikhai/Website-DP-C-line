<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\Product;

class ProductService
{

    public function getAttributesWithProductCount()
    {
        // Lấy tất cả các thuộc tính
        $attributes = Attribute::get();
        $result_attributes = [];

        if ($attributes->isEmpty()) {
            return $result_attributes; // Trả về mảng rỗng nếu không có thuộc tính
        }

        foreach ($attributes as $attribute) {
            if (!$attribute->value) {
                continue; // Bỏ qua nếu không có giá trị
            }

            // Giải mã giá trị JSON thành mảng
            $decodedValues = json_decode($attribute->value, true);

            foreach ($decodedValues as $item) {
                $name = $item['name'];
                $value = $item['value'];

                // Khởi tạo mảng nếu chưa tồn tại
                if (!isset($result_attributes[$name])) {
                    $result_attributes[$name] = [];
                }

                if (!isset($result_attributes[$name][$value])) {
                    $result_attributes[$name][$value] = [
                        'list_ids' => [], // Khởi tạo danh sách ID rỗng
                        'product_count' => 0 // Khởi tạo số lượng sản phẩm
                    ];
                }

                // Truy vấn sản phẩm có thuộc tính cụ thể
                $productIds = Product::whereHas('attributes', function ($query) use ($item) {
                    $query->whereRaw("JSON_CONTAINS(value, ?)", [json_encode($item)]);
                })->pluck('id')->toArray();

                dd($productIds);

                // Thêm danh sách ID vào phần tử list_ids
                $result_attributes[$name][$value]['list_ids'] = array_merge(
                    $result_attributes[$name][$value]['list_ids'],
                    $productIds
                );

                // Cập nhật số lượng sản phẩm
                $result_attributes[$name][$value]['product_count'] = count($result_attributes[$name][$value]['list_ids']);
            }
        }

        return $result_attributes; // Trả về mảng kết quả
    }
}
