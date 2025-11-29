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

    public function index(Request $request)
    {
        $title_head = 'Design';
        $keyword = $request->get('search', null);

        // 1. Lấy danh sách ID của toàn bộ design để chạy accessor (dùng cache)
        $allDesignsQuery = Design::query();

        // Nếu có từ khóa → filter theo name hoặc slug
        if ($keyword) {
            $allDesignsQuery->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }

        $allDesigns = $allDesignsQuery->select('id')->get();

        $totalDesigns = $allDesigns->count();

        // Tổng sản phẩm (dựa trên accessor cached)
        $totalProducts = $allDesigns->sum(function ($design) {
            return $design->total_products;
        });

        // 2. Lấy designs cho UI (paginate)
        $designsQuery = Design::with('collection')->orderBy('created_at', 'desc');

        if ($keyword) {
            $designsQuery->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }

        $designs = $designsQuery->paginate(20);

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

    // Lấy designs theo các attribute filter
    public function getDesignByAttributes(Request $request)
    {
        $filters = $request->get('filters', []); // expect ['Supplier' => ['A','B'], 'Usage' => ['x']]

        $query = Design::with(['attributeValues.attribute']);

        foreach ($filters as $name => $values) {
            $query->whereHas('attributeValues', function ($q) use ($name, $values) {
                $q->whereHas('attribute', fn($a) => $a->where('name', $name))
                    ->whereIn('value', (array)$values);
            });
        }

        $result = $query->paginate(24);

        return response()->json($result);
    }

    public function fixAttributes($id = null)
    {
        // --------------------------------
        // CASE 1: CHỈ FIX 1 RECORD
        // --------------------------------
        if ($id) {
            $design = Design::find($id);

            if (!$design) {
                return response("Không tìm thấy design ID: $id", 404);
            }

            $raw = $design->getRawOriginal('attributes');

            // decode lần 1
            $attrs = json_decode($raw, true);

            // decode lần 2 nếu vẫn là string (double encoded)
            if (is_string($attrs)) {
                $attrs = json_decode($attrs, true);
            }

            // validate
            if (!is_array($attrs)) {
                return "⚠️ Dữ liệu attributes không decode được hoặc không đúng format.";
            }

            // save lại dạng array chuẩn → Laravel sẽ encode đúng 1 lần
            $design->attributes = $attrs;
            $design->save();

            return response()->json([
                'id' => $id,
                'raw' => $raw,
                'decoded' => $attrs,
                'status' => "Đã fix xong record ID: $id"
            ]);
        }

        // --------------------------------
        // CASE 2: FIX TOÀN BỘ DATABASE
        // --------------------------------
        Design::chunk(100, function ($designs) {
            foreach ($designs as $design) {

                $raw = $design->getRawOriginal('attributes');
                if (!$raw) continue;

                $attrs = json_decode($raw, true);

                if (is_string($attrs)) {
                    $attrs = json_decode($attrs, true);
                }

                if (!is_array($attrs)) {
                    continue;
                }

                $design->attributes = $attrs;
                $design->save();

                echo "Fixed ID: {$design->id}<br>";
            }
        });

        return "🎉 Đã fix toàn bộ!";
    }
}
