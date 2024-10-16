<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Attribute;
use App\Models\Design;
use App\Models\Product;

class DesignController extends Controller
{
    public function show($design_slug)
    {
        $designs = Design::with('products')->where('slug', $design_slug)->where('is_featured', 1)->first();

        // Tìm category theo slug
        $category = Category::where('id', $designs->parent_id)->first();

        $categories = Category::where('is_featured', 1)->get();

        $products = Product::with('attributes')->where('category_id', $designs->id)->get();

        $result_attributes = $this->getAttributesWithProductCount();

        // Nếu không tìm thấy category, trả về lỗi 404
        if (!$category) {
            abort(404);
        }

        // Trả về view hiển thị thông tin category
        return view('design', compact('category', 'categories', 'result_attributes', 'products', 'designs'));
    }

    public function showProducts()
    {
        $designs = Design::with('products')->where('is_featured', 1)->first();

        $category = null;

        $title_head = 'All Products';

        $categories = Category::where('is_featured', 1)->get();

        $products = Product::with('attributes')->where('category_id', $designs->id)->get();

        $result_attributes = $this->getAttributesWithProductCount();

        // Trả về view hiển thị thông tin category
        return view('design', compact('category', 'categories', 'result_attributes', 'products', 'designs', 'title_head'));
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
