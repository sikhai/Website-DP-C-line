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

    public function show($category_slug)
    {
        // Tìm category dựa trên slug, trả về lỗi 404 nếu không tồn tại
        $category = Category::where('slug', $category_slug)
            ->with(['collections' => function ($q) {
                $q->whereHas('designs.products');
            }, 'collections.designs.products'])
            ->firstOrFail();

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
