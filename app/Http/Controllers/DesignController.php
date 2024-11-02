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
        // Lấy Design dựa trên slug, nếu không tồn tại trả về lỗi 404
        $designs = Design::with('products')->where('slug', $design_slug)->where('is_featured', 1)->firstOrFail();

        // Lấy category dựa vào `parent_id` của Design
        $category = Category::where('id', $designs->parent_id)->firstOrFail();

        $categories = Category::where('is_featured', 1)->get();
        $products = Product::with('attributes')->where('category_id', $designs->id)->get();

        // Sử dụng ProductService để lấy attributes và đếm số lượng
        $result_attributes = $this->productService->getAttributesWithProductCount();

        return view('design', compact('category', 'categories', 'result_attributes', 'products', 'designs'));
    }

    public function showProducts()
    {
        $designs = Design::with('products')->where('is_featured', 1)->first();

        // Kiểm tra null cho `$designs`
        if (!$designs) {
            abort(404);
        }

        $category = null;
        $title_head = 'All Products';
        $categories = Category::where('is_featured', 1)->get();
        $products = Product::with('attributes')->where('category_id', $designs->id)->get();

        $result_attributes = $this->productService->getAttributesWithProductCount();

        return view('design', compact('category', 'categories', 'result_attributes', 'products', 'designs', 'title_head'));
    }
}
