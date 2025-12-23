<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Public API Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Products API
Route::get('/products', 'Api\ApiProductController@index');
Route::get('/products/featured', 'Api\ApiProductController@featured');
Route::get('/products/{id}', 'Api\ApiProductController@show');

// Categories API
Route::get('/categories', 'Api\ApiCategoryController@index');
Route::get('/categories/{id}', 'Api\ApiCategoryController@show');
Route::get('/categories/{id}/products', 'Api\ApiProductController@byCategory');

// Contact API
Route::post('/contact', 'Api\ApiContactController@store');

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});
