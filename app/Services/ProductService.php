<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

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

            $this->getAttributes($attributes, $result_attributes);
            $this->getProductsByAttribute($result_attributes);

            return $result_attributes;
        });
    }

    public function getStatusAttributes($result_attributes)
    {
        // Cache key để định danh dữ liệu cache
        $cacheKey = 'status_attributes';

        // Lưu hoặc lấy dữ liệu từ cache với thời gian hết hạn là 720 phút
        return Cache::remember($cacheKey, 720, function () use ($result_attributes) {
            $status_attributes = [];

            // Duyệt qua các thuộc tính trong $result_attributes
            foreach ($result_attributes as $key => $value) {
                $status_attributes[] = [
                    'name'   => $key,
                    'status' => 1,
                ];
            }

            return $status_attributes;
        });
    }


    // Hàm xử lý thuộc tính để xây dựng mảng result_attributes
    public function getAttributes($attributes, &$result_attributes)
    {
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
            }
        }

        return $result_attributes;
    }

    // Hàm truy vấn sản phẩm và cập nhật danh sách ID và số lượng
    public function getProductsByAttribute(&$result_attributes)
    {
        foreach ($result_attributes as $name => $values) {
            foreach ($values as $value => $data) {
                // Truy vấn sản phẩm có thuộc tính cụ thể
                $productIds = Product::whereHas('attributes', function ($query) use ($name, $value) {
                    $query->whereRaw("JSON_CONTAINS(value, ?)", [json_encode(['name' => $name, 'value' => $value])]);
                })->pluck('id')->toArray();

                // Thêm danh sách ID vào phần tử list_ids
                $result_attributes[$name][$value]['list_ids'] = array_merge(
                    $result_attributes[$name][$value]['list_ids'],
                    $productIds
                );

                // Loại bỏ trùng lặp trong list_ids
                $result_attributes[$name][$value]['list_ids'] = array_unique($result_attributes[$name][$value]['list_ids']);

                // Cập nhật số lượng sản phẩm
                $result_attributes[$name][$value]['product_count'] = count($result_attributes[$name][$value]['list_ids']);
            }
        }

        return $result_attributes;
    }

    public function loadMoreProducts(Request $request)
    {
        $searchString = $request->input('search');
        $page = $request->input('page', 1); // Lấy trang hiện tại, mặc định là trang 1

        $query = Product::with('attributes', 'category')->where('is_featured', 1);

        if ($searchString) {
            $query->where('name', 'LIKE', "%{$searchString}%");
        }

        $products = $query->paginate(20, ['*'], 'page', $page);

        return response()->json([
            'products' => $products
        ]);
    }
}
