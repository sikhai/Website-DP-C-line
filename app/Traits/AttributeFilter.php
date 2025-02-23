<?php

namespace App\Traits;

use App\Models\Filter;
use App\Models\Category;

use Illuminate\Support\Collection;

trait AttributeFilter
{
    public function filterAttributesWithStatus($productService, int $category_id)
    {

        //Lấy danh sách product ID từ childCategories.products
        $category = Category::where('id', $category_id)
            ->with('childCategories.products')
            ->firstOrFail();


        $product_ids = $category->childCategories->flatMap(fn($child) => $child->products->pluck('id'))->unique();


        //Lọc dữ liệu từ Filter
        $data_attributes_filter = collect();

        $data_filter = Filter::where('status', 1)->whereNotNull('attributes_value')->get();

        foreach ($data_filter as $item) {
            $decoded_attribute = json_decode($item['attributes_value'], true);

            if (is_array($decoded_attribute) && isset($decoded_attribute[0])) {
                $first_item = json_decode($decoded_attribute[0], true);
                if (is_array($first_item)) {
                    $data_attributes_filter = $data_attributes_filter->merge($first_item);
                }
            }
        }

        // dd($product_ids);
        // dd($data_attributes_filter);

        // Lọc bỏ các phần tử không chứa product_id hợp lệ và loại bỏ product_id không tồn tại trong list_ids
        $filtered_attributes = $data_attributes_filter->map(function ($attribute_values) use ($product_ids) {
            return collect($attribute_values)->map(function ($sub_value) use ($product_ids) {
                // Lọc lại list_ids chỉ giữ những ID hợp lệ
                $list_ids = collect($sub_value['list_ids'] ?? [])->filter(function ($id) use ($product_ids) {
                    return $product_ids->contains($id);
                })->values(); // Reset lại chỉ số mảng

                // Cập nhật lại list_ids và product_count
                if ($list_ids->isNotEmpty()) {
                    $sub_value['list_ids'] = $list_ids->toArray();
                    $sub_value['product_count'] = $list_ids->count();
                    return $sub_value;
                }

                return null; // Trả về null nếu không còn product_id hợp lệ
            })->filter()->toArray(); // Xóa phần tử null
        })->filter(function ($attribute_values) {
            return !empty($attribute_values); // Loại bỏ key cha nếu không còn con nào hợp lệ
        });

        // Kết quả cuối cùng
        // dd($filtered_attributes->toArray());
        return $filtered_attributes->toArray();

        // $data_attributes = $productService->getAttributesWithProductCount();

        // $status_attributes = $productService->getStatusAttributes($data_attributes);

        // return collect($status_attributes)
        //     ->filter(function ($item) {
        //         return $item['status'] == "1";
        //     })
        //     ->pluck('name') // Lấy các giá trị `name`
        //     ->mapWithKeys(function ($name) use ($data_attributes) {
        //         return [$name => $data_attributes[$name] ?? null];
        //     })
        //     ->filter(); // Loại bỏ các giá trị null (nếu không tồn tại trong data_attributes)
    }
}
