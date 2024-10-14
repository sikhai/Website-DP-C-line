<?php

use Illuminate\Support\Facades\Route;

use  App\Http\Controllers\MainController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProjectsController;

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

// Route hiển thị chi tiết Category với slug
Route::get('/{category_slug}', [CategoryController::class, 'show'])->name('category.show');

// Route hiển thị danh sách hoặc chi tiết các Product thuộc Category
Route::get('/{category_slug}/{product_slug?}', [ProductsController::class, 'show'])->name('product.show');

