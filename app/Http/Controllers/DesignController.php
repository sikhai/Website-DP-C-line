<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Attribute;
use App\Models\Design;
use App\Models\Product;

use App\Services\ProductService;

class DesignController extends Controller
{

    protected $productService;


    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    public function show($design_slug)
    {
        $designs = Design::with('products')->where('slug', $design_slug)->where('is_featured', 1)->first();

        // Tìm category theo slug
        $category = Category::where('id', $designs->parent_id)->first();

        $categories = Category::where('is_featured', 1)->get();

        $products = Product::with('attributes')->where('category_id', $designs->id)->get();

        $result_attributes = $this->getAttributesWithProductCount();

        // Nếu không tìm thấy category, trả về lỗi 404
        if (!$category) {
            abort(404);
        }

        // Trả về view hiển thị thông tin category
        return view('design', compact('category', 'categories', 'result_attributes', 'products', 'designs'));
    }

    public function showProducts()
    {
        $designs = Design::with('products')->where('is_featured', 1)->first();

        $category = null;

        $title_head = 'All Products';

        $categories = Category::where('is_featured', 1)->get();

        $products = Product::with('attributes')->where('category_id', $designs->id)->get();

        $result_attributes = $this->productService->getAttributesWithProductCount();

        // Trả về view hiển thị thông tin category
        return view('design', compact('category', 'categories', 'result_attributes', 'products', 'designs', 'title_head'));
    }
}
