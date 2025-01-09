<?php
namespace App\Traits;

use Illuminate\Support\Collection;

trait AttributeFilter
{
    public function filterAttributesWithStatus($productService)
    {
        $data_attributes = $productService->getAttributesWithProductCount();

        $status_attributes = $productService->getStatusAttributes($data_attributes);

        return collect($status_attributes)
            ->filter(function ($item) {
                return $item['status'] == "1";
            })
            ->pluck('name') // Lấy các giá trị `name`
            ->mapWithKeys(function ($name) use ($data_attributes) {
                return [$name => $data_attributes[$name] ?? null];
            })
            ->filter(); // Loại bỏ các giá trị null (nếu không tồn tại trong data_attributes)
    }
}
