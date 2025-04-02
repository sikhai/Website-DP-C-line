<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Attribute;
use App\Models\Design;
use App\Models\Product;
use App\Services\ProductService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use App\Traits\AttributeFilter;


class DesignController extends Controller
{

    use AttributeFilter;

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

        $category_slug = $category->slug ? $category->slug : null;

        $categories = Category::where('is_featured', 1)->whereNull('parent_id')->get();
        $products = Product::with('attributes')
            ->where('category_id', $designs->id)
            ->where('is_featured', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Sử dụng ProductService để lấy attributes và đếm số lượng
        $result_attributes = $this->filterAttributesWithStatus($this->productService, $category->id);

        return view('design', compact('category', 'categories', 'result_attributes', 'products', 'designs', 'category_slug'));
    }

    public function showProducts(Request $request)
    {
        $attributes_filler = [];
        $all_list_ids = [];

        $category_slug = $request->input('category');

        $attributeString = $request->input('attribute');

        if (isset($attributeString)) {
            $attributePairs = explode(',', $attributeString);
            $attributes_filler = [];

            $lastAttributeName = null;
            $combinedValue = '';

            foreach ($attributePairs as $pair) {
                // Nếu chứa dấu '-', tách thành attribute-name và value
                if (strpos($pair, '-') !== false) {
                    // Lưu giá trị trước đó nếu có
                    if ($lastAttributeName !== null && $combinedValue !== '') {
                        $attributes_filler[] = [
                            'attribute-name' => $lastAttributeName,
                            'value' => $combinedValue
                        ];
                    }

                    // Tách attribute-name và value mới
                    [$lastAttributeName, $combinedValue] = explode('-', $pair, 2);
                } else {
                    // Nếu không có attribute-name, tiếp tục nối với giá trị trước đó
                    $combinedValue .= ($combinedValue !== '' ? ',' : '') . $pair;
                }
            }

            // Thêm dữ liệu cuối cùng vào mảng nếu có
            if ($lastAttributeName !== null && $combinedValue !== '') {
                $attributes_filler[] = [
                    'attribute-name' => $lastAttributeName,
                    'value' => $combinedValue
                ];
            }
        }

        $category = null;

        $title_head = 'All Products';

        $searchString = $request->input('search');

        $searchString = strip_tags($searchString);

        $categories = Category::where('is_featured', 1)->whereNull('parent_id')->get();

        if ($category_slug) {
            $category = Category::where('slug', $category_slug)->firstOrFail();
        }else{
            $category_slug = $categories[0]['slug'];
        }

        // Base query for featured products
        $query_products = Product::with('attributes', 'category')->where('is_featured', 1)->orderBy('created_at', 'desc');

        if ($searchString) {
            $title_head = 'Search';

            $query_products->where(function ($q) use ($searchString) {
                $q->where('name', 'LIKE', "%{$searchString}%");
                // ->orWhere('Description', 'LIKE', "%{$searchString}%");
            });
        }

        if (!$category) {
            $category_id = $categories[0]['id'];
        }else{
            $category_id = $category->id;
        }

        $result_attributes = $this->filterAttributesWithStatus($this->productService, $category_id);

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

            $query_products->where(function ($q) use ($all_list_ids) {
                $q->whereIn('id', $all_list_ids);
            });
        }

        $encrypted_ids = Crypt::encrypt($all_list_ids);

        $products = $query_products->paginate(20);

        return view('design', compact('category', 'categories', 'result_attributes', 'products', 'title_head', 'attributeString', 'category_slug', 'encrypted_ids'));
    }
}
