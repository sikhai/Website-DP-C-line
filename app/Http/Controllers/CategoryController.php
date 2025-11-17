<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Design;
use App\Models\Product;
use App\Services\ProductService;

use App\Traits\AttributeFilter;

class CategoryController extends Controller
{

    use AttributeFilter;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function showCollection()
    {
        $category = null;
        $title_head = 'Collection';

        // Lấy categories và designs nổi bật
        $categories = Category::where('is_featured', 1)->where('type', 'PRODUCT')->whereNull('parent_id')->get();

        $category_slug = $categories[0]['slug'] ? $categories[0]['slug'] : null;

        $designs = Design::with(['products' => function ($query) {
            $query->where('is_featured', 1);
        }])->where('is_featured', 1)
            ->whereHas('products', function ($query) {
                $query->where('is_featured', 1);
            })->get();

        $products = Product::where('is_featured', 1)->get();

        // Gọi ProductService để lấy attributes với số lượng sản phẩm
        $result_attributes = $this->filterAttributesWithStatus($this->productService, $categories[0]['id']);

        // Trả về view hiển thị sản phẩm
        return view('product', compact('category', 'categories', 'result_attributes', 'products', 'designs', 'title_head', 'category_slug'));
    }

    public function show($category_slug)
    {
        // Tìm category dựa trên slug, trả về lỗi 404 nếu không tồn tại
        $category = Category::where('slug', $category_slug)->with('collections.designs.products')->firstOrFail();

        $category_slug = $category->slug ? $category->slug : null;

        // Lấy các categories và designs liên quan đến category này
        $categories = Category::where('is_featured', 1)->where('type', 'PRODUCT')->whereNull('parent_id')->get();

        // Gọi ProductService để lấy attributes với số lượng sản phẩm
        // $result_attributes = $this->productService->getAttributesWithProductCount();
        $result_attributes = $this->filterAttributesWithStatus($this->productService, $category->id);


        $title_head = 'Collection';

        // Trả về view hiển thị thông tin category
        return view('category.show', compact('category', 'categories', 'result_attributes', 'category_slug', 'title_head'));
    }
}
