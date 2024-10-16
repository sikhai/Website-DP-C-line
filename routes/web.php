<?php

use Illuminate\Support\Facades\Route;

use  App\Http\Controllers\MainController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\DesignController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [MainController::class, 'home'])->name('home');
/* admin */
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::post('/check-product-code', [ProductController::class, 'checkProductCode'])->name('check.product.code');
/* admin */
Route::get('/our-project', [ProjectsController::class, 'show'])->name('project.show');

// Route cho sản phẩm
Route::get('/collection', [CategoryController::class, 'showCollection'])->name('collection.show');
Route::get('/products', [DesignController::class, 'showProducts'])->name('products.show');
// Route hiển thị chi tiết sản phẩm với slug
Route::get('/products/{product_slug}', [ProductsController::class, 'detail'])->name('product.detail');


// Route cho category với slug động
Route::get('/{category_slug}', [CategoryController::class, 'showCategory'])->name('category.show');


Route::prefix('design')->group(function () {
    // Route hiển thị chi tiết Design với slug
    Route::get('{design_slug}', [DesignController::class, 'show'])->name('design.show');
});

// Route hiển thị danh sách hoặc chi tiết các Product thuộc Category
Route::get('/{category_slug}/{product_slug?}', [ProductsController::class, 'show'])->name('product.show');

