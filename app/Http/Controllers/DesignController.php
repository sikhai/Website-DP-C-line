<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Attribute;
use App\Models\Design;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;


class DesignController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function show($design_slug)
    {
        // Lấy Design dựa trên slug, nếu không tồn tại trả về lỗi 404
        $designs = Design::with('products')->where('slug', $design_slug)->where('is_featured', 1)->firstOrFail();

        // Lấy category dựa vào `parent_id` của Design
        $category = Category::where('id', $designs->parent_id)->first();

        $categories = Category::where('is_featured', 1)->get();
        $products = Product::with('attributes')->where('category_id', $designs->id)->where('is_featured', 1)->get();

        // Sử dụng ProductService để lấy attributes và đếm số lượng
        $result_attributes = $this->productService->getAttributesWithProductCount();

        return view('design', compact('category', 'categories', 'result_attributes', 'products', 'designs'));
    }

    public function showProducts(Request $request)
    {
        $attributes_filler = [];
        $all_list_ids = [];

        $attributeString = $request->input('attribute');
        if (isset($attributeString)) {
            $attributePairs = explode(',', $attributeString);

            // Duyệt qua từng cặp và tách thành 'attribute-name' và 'value'
            foreach ($attributePairs as $pair) {
                list($attributeName, $value) = explode('-', $pair);
                $attributes_filler[] = [
                    'attribute-name' => $attributeName,
                    'value' => $value
                ];
            }
        }

        $searchString = $request->input('search');

        $category = null;
        $title_head = 'All Products';

        // dd($designs->id);

        $categories = Category::where('is_featured', 1)->get();
        $products = Product::with('attributes')->with('category')->where('is_featured', 1)->get();

        $result_attributes = $this->productService->getAttributesWithProductCount();


        if (count($attributes_filler) > 0) {
            foreach ($attributes_filler as $attribute) {
                // Lấy tên thuộc tính và giá trị từ mảng
                $attributeName = $attribute['attribute-name'];
                $value = $attribute['value'];

                // Kiểm tra xem thuộc tính có trong $result_attributes hay không
                if (isset($result_attributes[$attributeName][$value])) {
                    // Gộp các list_ids vào mảng all_list_ids
                    $all_list_ids = array_merge($all_list_ids, $result_attributes[$attributeName][$value]['list_ids']);
                }
            }

            // Xóa các giá trị trùng lặp (nếu cần)
            $all_list_ids = array_unique($all_list_ids);
        }

        return view('design', compact('category', 'categories', 'result_attributes', 'products', 'title_head'));
    }
}
