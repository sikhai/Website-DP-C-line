<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ProductsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/attributes', [PerformanceController::class, 'getAttributesPerformance']);
Route::get('/get-products-by-attribute', [PerformanceController::class, 'getProductsByAttributePerformance']);
Route::get('/load-more-products', [ProductsController::class, 'loadMoreProducts']);
Route::get('/load-more-filter-products', [ProductsController::class, 'loadMoreFilterProducts']);
