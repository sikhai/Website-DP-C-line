<?php

use Illuminate\Support\Facades\Route;

use  App\Http\Controllers\MainController;
use App\Http\Controllers\ProductController;

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
Route::get('/news', [MainController::class, 'news'])->name('news');
Route::get('/contact', [MainController::class, 'contact'])->name('contact');
Route::get('/product', [MainController::class, 'product'])->name('product');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
