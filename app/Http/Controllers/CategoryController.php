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

    public function show(Category $category)
    {
        // Preload quan hệ tối ưu — chỉ load những gì cần
        $category->load([
            'collections' => function ($q) {
                $q->whereHas('designs.products'); // chỉ lấy collections có products
            },
            'collections.designs' => function ($q) {
                $q->whereHas('products'); // chỉ lấy designs có sản phẩm
            },
            'collections.designs.products' // preload products để tránh N+1
        ]);

        // Featured categories (sidebar)
        $categories = Category::where('is_featured', 1)
            ->productType()
            ->whereNull('parent_id')
            ->get();

        // Filter attributes
        $result_attributes = [];
        // $result_attributes = $this->filterAttributesWithStatus($this->productService, $category->id);

        $title_head = 'Collection';

        return view('category.show', [
            'category'        => $category,
            'categories'      => $categories,
            'result_attributes' => $result_attributes,
            'category_slug'   => $category->slug,
            'title_head'      => $title_head,
        ]);
    }
}
