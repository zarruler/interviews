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

Route::get('category/all', 'CatalogCategoryController@index'); // show list of all categories
Route::get('category/{id}', 'CatalogCategoryController@show'); // show category {id} details
Route::put('category/{id}', 'CatalogCategoryController@update'); // update category {id} details
Route::delete('category/{id}', 'CatalogCategoryController@destroy'); // delete category {id}
Route::post('category', 'CatalogCategoryController@store'); // create new category
Route::get('category/{id}/items', 'CatalogCategoryController@listProducts'); // show all products in category {id}

Route::get('product/{id}', 'CatalogProductController@index'); // show product {id} details
Route::delete('product/{id}', 'CatalogProductController@destroy'); // delete product {id}
Route::post('product', 'CatalogProductController@store'); // create new product without category
Route::post('product/category/{id}', 'CatalogProductController@store'); // create new product in category
Route::put('product/{id}', 'CatalogProductController@update'); // update product {id} details

Route::post('login', 'UserController@login'); // user login to receive token
Route::post('register', 'UserController@store'); // register new user and create token

