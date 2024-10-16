<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Attribute;
use App\Models\Design;
use App\Models\Product;

class CategoryController extends Controller
{

    public function showCollection()
    {
        $category = null;

        $title_head = 'Collection';

        $categories = Category::where('is_featured', 1)->get();

        $designs = Design::with('products')
            ->where('is_featured', 1)
            ->get();

        $products = Product::where('is_featured', 1)->get();

        // Gọi hàm lấy attributes với số lượng sản phẩm
        $result_attributes = $this->getAttributesWithProductCount();
        
        // Trả về view hiển thị sản phẩm
        return view('product', compact('category', 'categories', 'result_attributes', 'products', 'designs', 'title_head'));
    }

    public function showCategory($category_slug)
    {
        // Tìm category dựa trên slug
        $category = Category::where('slug', $category_slug)->first();

        // Lấy các categories nổi bật
        $categories = Category::where('is_featured', 1)->get();

        // Lấy tất cả các thiết kế con thuộc về category
        $designs = Design::with('products')
            ->where('parent_id', $category->id)
            ->where('is_featured', 1)
            ->get();

        // Lấy danh sách product_ids từ các thiết kế nổi bật
        $design_ids = $designs->pluck('id')->toArray();

        // Lấy tất cả sản phẩm liên quan đến các thiết kế
        $products = Product::where('is_featured', 1)
            ->whereIn('category_id', $design_ids)
            ->get();

        // Gọi hàm lấy attributes với số lượng sản phẩm
        $result_attributes = $this->getAttributesWithProductCount();

        // Nếu không tìm thấy category, trả về lỗi 404
        if (!$category) {
            abort(404);
        }

        // Trả về view hiển thị thông tin category
        return view('product', compact('category', 'categories', 'result_attributes', 'products', 'designs'));
    }


    public function getAttributesWithProductCount()
    {
        $values = Attribute::pluck('value'); // chỉ lấy cột 'value'

        $result_attributes = [];

        foreach ($values as $value) {
            $decodedValues = json_decode($value, true);

            foreach ($decodedValues as $item) {
                $name = $item['name'];
                $value = $item['value'];

                $result_attributes[$name][$value] = true;
            }
        }

        foreach ($result_attributes as $name => $values) {
            $result_attributes[$name] = array_keys($values);
        }

        return $result_attributes;
    }
}
