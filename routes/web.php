<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/addToCart', [App\Http\Controllers\CartController::class, "addToCart"]);
Route::post('/updateProductQuantityInCart', [App\Http\Controllers\CartController::class, "updateProductQuantityInCart"]);
Route::delete('/emptyCart/{userId}', [App\Http\Controllers\CartController::class, "emptyCart"]);
