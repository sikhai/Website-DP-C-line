<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function getAttributesWithProductCount()
    {
        // Cache key để định danh dữ liệu cache
        $cacheKey = 'attributes_with_product_count';

        // Lưu hoặc lấy dữ liệu từ cache với thời gian hết hạn là 720 phút
        return Cache::remember($cacheKey, 720, function () {
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

                    // Thêm danh sách ID vào phần tử list_ids
                    $result_attributes[$name][$value]['list_ids'] = array_merge(
                        $result_attributes[$name][$value]['list_ids'],
                        $productIds
                    );

                    // Cập nhật số lượng sản phẩm
                    $result_attributes[$name][$value]['product_count'] = count($result_attributes[$name][$value]['list_ids']);
                }
            }

            return $result_attributes;
        });
    }
}
