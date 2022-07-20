<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ProductController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);
Route::post('transaction', [TransactionController::class, 'store']);
Route::get('buyer', [BuyerController::class, 'index']);
Route::get('buyer-transaction', [BuyerController::class, 'transaction']);
Route::post('add-to-cart/{id}', [ProductController::class, 'addToCart']);
Route::get('cart', [ProductController::class, 'cart']);
Route::delete('delete-cart/{id}', [ProductController::class, 'deleteCart']);
Route::get('buyer-cart', [BuyerController::class, 'cart']);
