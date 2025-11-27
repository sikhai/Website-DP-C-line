<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Design;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;

use Illuminate\Support\Facades\Crypt;


class ProductsController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function detail($product_slug)
    {

        $collection = null;

        $attributes = null;

        $data_attributes = null;

        $products_orther = null;

        $categories = Category::where('is_featured', 1)->where('type', 'PRODUCT')->whereNull('parent_id')->get();

        // Nếu có product_slug, tìm product theo slug
        $product = Product::with('design.collection.category')
            ->where('slug', $product_slug)
            ->where('is_featured', 1)
            ->first();

        if (!$product) {
            abort(404);
        }

        // Breadcrumb
        $breadcrumb = [
            'category'   => $product->design->collection->category ?? null,
            'collection' => $product->design->collection ?? null,
            'design'     => $product->design ?? null,
            'product'    => $product,
        ];

        // Lấy attributes của design
        $data_attributes = $product->design->active_attributes ?? null;

        $attributes = $this->caculateAttribute($data_attributes);

        // Lấy các product khác cùng design
        $products_orther = $product->design
            ->products()
            ->where('is_featured', 1)
            ->where('id', '<>', $product->id)
            ->take(8)
            ->get();
            
        return view('product.detail', compact('collection', 'categories', 'product', 'attributes', 'products_orther', 'breadcrumb'));
    }

    public function caculateAttribute($data)
    {
        if (!$data) {
            return null;
        }

        $totalItems = count($data);
        $firstArraySize = ceil($totalItems / 2);

        $firstArray = array_slice($data, 0, $firstArraySize);
        $secondArray = array_slice($data, $firstArraySize);

        $attributes = [$firstArray, $secondArray];

        return $attributes;
    }

    public function fillerAttributes($array1, $array2)
    {

        if (!is_array($array1) || !is_array($array2)) {
            return false;
        }
        // Lọc dữ liệu từ mảng thứ hai (chỉ lấy những phần tử có status = 1)
        $filtered = collect($array2)
            ->filter(function ($item) {
                return $item['status'] == "1";
            })
            ->pluck('name') // Lấy tên của các phần tử có status = 1
            ->toArray();

        // Lọc dữ liệu từ mảng đầu tiên (chỉ lấy phần tử có name trong danh sách đã lọc)
        $result = collect($array1)
            ->filter(function ($item) use ($filtered) {
                return in_array($item['name'], $filtered);
            })
            ->values() // Sắp xếp lại chỉ số mảng
            ->toArray();

        return $result;
    }

    public function loadMoreProducts(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);

        $categorySlug = $request->get('category_slug'); // Lấy slug category từ yêu cầu

        $query = Product::with('attributes', 'category')->where('is_featured', 1)->orderBy('created_at', 'desc');

        // Nếu có slug category, thêm điều kiện lọc theo category
        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            "products" => $products->items(),
            "next_page" => $products->currentPage() < $products->lastPage() ? $products->currentPage() + 1 : null,
        ]);
    }
    
    public function loadMoreByDesign(Request $request, Design $design)
    {
        $page = $request->get('page', 1);

        $products = $design->products()
            ->orderBy('id')
            ->paginate(20, ['*'], 'page', $page);

        $html = view('partials.product-items', [
            'products' => $products
        ])->render();

        return response()->json([
            'html' => $html,
            'next_page' => $products->hasMorePages(),
        ]);
    }
}
