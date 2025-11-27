<?php

use Illuminate\Support\Facades\Route;

use  App\Http\Controllers\MainController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\AccessoryImportController;

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

Route::post('/update-status-attributes', [ProductController::class, 'updateStatusAttributes'])->name('update.status.attributes');
/* admin */
/* our-project */
Route::get('/our-project', [ProjectsController::class, 'show'])->name('project.show');
Route::get('/our-project/{project_slug}', [ProjectsController::class, 'detail'])->name('project.detail');

Route::prefix('collections')->as('collections.')->group(function () {
    Route::post('cache/clear-all', [CollectionController::class, 'clearProductsCache'])
        ->name('clearAllCache');

    Route::post('cache/{collection}/clear', [CollectionController::class, 'clearProductsCache'])
        ->name('clearCache');

    Route::get('/', [CollectionController::class, 'index'])->name('index');
    Route::get('{collection:slug}', [CollectionController::class, 'show'])->name('show');
});

// PRODUCTS ROUTES
Route::prefix('products')->name('products.')->group(function () {
    // Route::get('/', [ProductsController::class, 'index'])->name('index');
    Route::get('/{product:slug}', [ProductsController::class, 'detail'])->name('show');
});

// Route cho category với slug động
Route::prefix('category')->group(function () {
    Route::get('{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
});

Route::prefix('designs')->name('designs.')->group(function () {
    Route::get('/', [DesignController::class, 'index'])->name('index');
    // Load more designs
    Route::get('/load-more', [DesignController::class, 'loadMore'])->name('loadMore');
    // Load more products of a specific design
    Route::get(
        '{design}/products/load-more',
        [ProductsController::class, 'loadMoreByDesign']
    )->name('products.loadMore');
    // Detail of a design
    Route::get('{design:slug}', [DesignController::class, 'show'])->name('show');
});



// Route hiển thị danh sách hoặc chi tiết các Product thuộc Category
// Route::get('/{category_slug}/{product_slug}', [ProductsController::class, 'show'])->name('product.show');

// Import accessories
Route::get('/accessories/import', [AccessoryImportController::class, 'showForm'])->name('accessories.import.form');
Route::post('/accessories/import', [AccessoryImportController::class, 'import'])->name('accessories.import');
