<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Design;
use App\Models\Product;
use App\Services\ProductService;

use App\Traits\AttributeFilter;

class CollectionController extends Controller
{

    use AttributeFilter;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $title_head = 'Collection';
        $category  = 'Collection';
        $result_attributes = [];

        $collections = Collection::whereHas('designs.products')
            ->with('designs.products')
            ->get();

        $totalProducts = Product::whereHas('design.collection')->count();

        // Lấy các categories và designs liên quan đến category này
        $categories = Category::where('is_featured', 1)->where('type', 'PRODUCT')->whereNull('parent_id')->get();

        // Gọi ProductService để lấy attributes với số lượng sản phẩm
        // $result_attributes = $this->productService->getAttributesWithProductCount();
        // $result_attributes = $this->filterAttributesWithStatus($this->productService, $category->id);

        // Trả về view hiển thị thông tin category
        return view('collection.index', compact('category', 'categories', 'result_attributes', 'title_head', 'totalProducts', 'collections'));
    }

    public function show(Collection $collection)
    {
        $title_head = 'Collection';
        $category  = 'Collection';
        $result_attributes = [];

        $collection->load('category');

        // Paginate designs, 20 per page, kèm products và category của design
        $designs = $collection->designs()->with('products')->paginate(20);

        $categories = Category::where('is_featured', 1)->where('type', 'PRODUCT')->whereNull('parent_id')->get();

        $totalProducts = $designs->sum(function ($design) {
            return $design->products->count();
        });

        return view('collection.show', compact('categories', 'result_attributes', 'title_head', 'totalProducts', 'designs', 'collection'));
    }
}
