<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Attribute;
use App\Models\Design;
use App\Models\Product;

class CategoryController extends Controller
{
    public function show($category_slug)
    {
        // Tìm category theo slug
        $category = Category::where('slug', $category_slug)->first();

        $categories = Category::where('is_featured', 1)->get();

        $designs = Design::with('products')->where('is_featured', 1)->get();

        $products = Product::where('is_featured', 1)->get();

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
