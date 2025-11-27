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

    public function index()
    {
        $title_head = 'Design';

        // 1. Lấy danh sách ID của toàn bộ design để chạy accessor (dùng cache)
        $allDesigns = Design::select('id')->get();

        // Tổng design (không phân trang)
        $totalDesigns = $allDesigns->count();

        // 2. Tổng sản phẩm (dựa trên accessor cached)
        //    => KHÔNG gây n+1 query vì cache đã có.
        $totalProducts = $allDesigns->sum(function ($design) {
            return $design->total_products;
        });

        // 3. Lấy designs cho UI (paginate)
        $designs = Design::with('collection')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Featured categories
        $categories = Category::where('is_featured', 1)
            ->where('type', 'PRODUCT')
            ->whereNull('parent_id')
            ->get();

        $result_attributes = [];

        return view('design.index', compact(
            'title_head',
            'designs',
            'categories',
            'result_attributes',
            'totalProducts',
            'totalDesigns'
        ));
    }

    public function show(Design $design)
    {
        // Load products và category relation (eager load)
        $design->load('products', 'collection');

        // Nếu muốn chỉ lấy featured products
        $products = $design->products()
            ->where('is_featured', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // $design->clearProductsCountCache();
        // Lấy category dựa trên parent_id (Collection)
        $category = $design->collection;

        $category_slug = $category ? $category->slug : null;

        // Các categories featured (sidebar/filter)
        $categories = Category::where('is_featured', 1)
            ->where('type', 'PRODUCT')
            ->whereNull('parent_id')
            ->get();

        // Nếu bạn muốn filter attributes theo productService
        // $result_attributes = $this->filterAttributesWithStatus($this->productService, $category->id);
        $result_attributes = [];

        // Lấy tổng sản phẩm của design (có thể dùng accessor cache)
        $totalProducts = $design->total_products;

        return view('design.show', compact(
            'design',
            'category',
            'categories',
            'result_attributes',
            'products',
            'category_slug',
            'totalProducts'
        ));
    }

    public function loadMore(Request $request)
    {
        $page = $request->page ?? 1;

        $query = Design::with('products');

        if ($request->collection_id) {
            $query->where('parent_id', $request->collection_id);
        }

        $designs = $query->paginate(20, ['*'], 'page', $page);

        $html = '';
        if ($designs->count() > 0) {
            $html = view('partials.design-items', compact('designs'))->render();
        }

        return response()->json([
            'html' => $html,
            'next_page' => $designs->nextPageUrl() ? $page + 1 : false,
        ]);
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

        $categories = Category::where('is_featured', 1)->where('type', 'PRODUCT')->whereNull('parent_id')->get();

        if ($category_slug) {
            $category = Category::where('slug', $category_slug)->firstOrFail();
        } else {
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
        } else {
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
