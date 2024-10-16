<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class ProductsController extends Controller
{
    public function detail($product_slug)
    {

        $collection = null;

        $categories = Category::where('is_featured', 1)->get();

        // Nếu có product_slug, tìm product theo slug
        $product = Product::with('category')->with('attributes')->where('slug', $product_slug)->where('is_featured', 1)->first();

        if( isset($product->category->parent_id) ){
            $collection = Category::where('id', $product->category->parent_id)->where('is_featured', 1)->first();
        }
        
        if (!$product) {
            abort(404);
        }

        return view('product_detail', compact('collection', 'categories', 'product'));
    }
}
