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

        // Lấy tất cả collection có ít nhất 1 product qua designs
        $collections = Collection::whereHas('designs.products')
            ->with('designs')
            ->get();

        // Tính tổng products cho từng collection
        foreach ($collections as $collection) {
            $collection->total_products = $collection->products_count;

            //Xóa cache products count nếu cần thiết
            // $collection->clearProductsCountCache();
        }

        // Tính tổng products của tất cả collections cùng lúc (1 query duy nhất)
        $totalProducts = Product::whereHas('design', function ($q) use ($collections) {
            $q->whereIn('parent_id', $collections->pluck('id'));
        })->count();

        // Lấy các categories
        $categories = Category::where('is_featured', 1)
            ->where('type', 'PRODUCT')
            ->whereNull('parent_id')
            ->get();

        // Trả về view
        return view('collection.index', compact(
            'category',
            'categories',
            'result_attributes',
            'title_head',
            'totalProducts',
            'collections'
        ));
    }

    public function show(Collection $collection)
    {
        $title_head = 'Collection';
        $category  = 'Collection';
        $result_attributes = [];

        // Load category relation
        $collection->load('category');

        // Paginate designs 20 per page, eager load products to avoid N+1
        $designs = $collection->designs()->with('products')->paginate(20);

        // Tổng số designs của collection (toàn bộ)
        $totalDesigns = $collection->designs()->count();

        // Tổng số products của tất cả designs trong collection (toàn bộ)
        // Dùng 1 query duy nhất để tránh N+1
        $totalProducts = Product::whereHas('design', function ($query) use ($collection) {
            $query->where('parent_id', $collection->id);
        })->count();

        // Các categories featured
        $categories = Category::where('is_featured', 1)
            ->where('type', 'PRODUCT')
            ->whereNull('parent_id')
            ->get();

        return view('collection.show', compact(
            'categories',
            'result_attributes',
            'title_head',
            'totalProducts',
            'totalDesigns',
            'designs',
            'collection'
        ));
    }


    public function clearProductsCache($id = null)
    {
        if ($id) {
            // Xóa cache của collection theo id
            $collection = Collection::find($id);
            if ($collection) {
                $collection->clearProductsCountCache();
            }
        } else {
            // Xóa cache của tất cả collection
            $collections = Collection::all();
            foreach ($collections as $collection) {
                $collection->clearProductsCountCache();
            }
        }

        return redirect()->back()->with('success', 'Cache ProductsCount đã được xóa.');
    }
}
