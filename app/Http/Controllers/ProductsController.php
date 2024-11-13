<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function detail($product_slug)
    {

        $collection = null;

        $attributes = null;

        $data_attributes = null;

        $products_orther = null;

        $categories = Category::where('is_featured', 1)->get();

        // Nếu có product_slug, tìm product theo slug
        $product = Product::with('category')->with('attributes')->where('slug', $product_slug)->where('is_featured', 1)->first();

        if (isset($product->category->parent_id)) {
            $collection = Category::with('childCategories.products')->find($product->category->parent_id);

            $products = collect();

            foreach ($collection->childCategories as $design) {
                $products_orther = $products->merge(
                    $design->products()
                        ->where('id', '!=', $product->id)
                        ->take(11 - $products->count())
                        ->get()
                );

                // Nếu đã đủ 10 sản phẩm, thoát khỏi vòng lặp
                if ($products_orther->count() >= 10) {
                    break;
                }
            }
        }

        if (!$product) {
            abort(404);
        }

        if (isset($product->attributes[0])) {
            $attributes = $product->attributes[0];
            $data_attributes = json_decode($attributes->value, true);
            $attributes = $this->caculateAttribute($data_attributes);
        }


        return view('product_detail', compact('collection', 'categories', 'product', 'attributes', 'products_orther'));
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

    public function loadMoreProducts(Request $request)
    {
        // Lấy số lượng sản phẩm cần tải thêm từ tham số request hoặc mặc định là 20
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);

        // Kiểm tra xem có bộ lọc hoặc điều kiện khác không
        $query = Product::query();

        // Nếu có điều kiện lọc (ví dụ: theo danh mục, từ khóa, giá, v.v.)
        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // Thêm các điều kiện khác nếu cần (ví dụ: lọc theo thuộc tính)

        // Phân trang sản phẩm
        $products = $query->paginate($perPage, ['*'], 'page', $page);

        // Trả về JSON cho AJAX
        return response()->json(["products" => $products]);
    }
}
