<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class ProductsController extends Controller
{
    public function show($category_slug, $product_slug = null)
    {
        // Tìm category theo slug
        $category = Category::where('slug', $category_slug)->first();

        // Nếu không tìm thấy category, trả về lỗi 404
        if (!$category) {
            abort(404);
        }

        // Nếu không có product_slug, hiển thị danh sách các sản phẩm trong category
        if (!$product_slug) {
            $products = $category->products; // Quan hệ giữa Category và Product
            return view('product.index', compact('category', 'products'));
        }

        // Nếu có product_slug, tìm product theo slug
        $products = Product::where('slug', $product_slug)->where('category_id', $category->id)->get();

        // Nếu không tìm thấy product, trả về lỗi 404
        if (!$products) {
            abort(404);
        }

        // Trả về view hiển thị chi tiết sản phẩm
        return view('product.show', compact('category', 'products'));
    }
}
