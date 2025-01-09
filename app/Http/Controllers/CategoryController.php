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
        $categories = Category::where('is_featured', 1)->whereNull('parent_id')->get();
        $designs = Design::with(['products' => function ($query) {
            $query->where('is_featured', 1);
        }])->where('is_featured', 1)
            ->whereHas('products', function ($query) {
                $query->where('is_featured', 1);
            })->get();

        $products = Product::where('is_featured', 1)->get();

        // Gọi ProductService để lấy attributes với số lượng sản phẩm
        $result_attributes = $this->filterAttributesWithStatus($this->productService);

        // Trả về view hiển thị sản phẩm
        return view('product', compact('category', 'categories', 'result_attributes', 'products', 'designs', 'title_head'));
    }

    public function showCategory($category_slug)
    {
        // Tìm category dựa trên slug, trả về lỗi 404 nếu không tồn tại
        $category = Category::where('slug', $category_slug)->firstOrFail();

        // Lấy các categories và designs liên quan đến category này
        $categories = Category::where('is_featured', 1)->whereNull('parent_id')->get();
        $designs = Design::with('products')
            ->where('parent_id', $category->id)
            ->where('is_featured', 1)
            ->get();

        // Lấy danh sách product_ids từ các thiết kế liên quan
        $design_ids = $designs->pluck('id')->toArray();

        // Lấy tất cả sản phẩm liên quan đến các thiết kế
        $products = Product::where('is_featured', 1)
            ->whereIn('category_id', $design_ids)
            ->get();

        // Gọi ProductService để lấy attributes với số lượng sản phẩm
        // $result_attributes = $this->productService->getAttributesWithProductCount();
        $result_attributes = $this->filterAttributesWithStatus($this->productService);

        // Trả về view hiển thị thông tin category
        return view('product', compact('category', 'categories', 'result_attributes', 'products', 'designs'));
    }
}
