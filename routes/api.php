<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/*Users Routes*/
Route::group(["prefix" => "/"], function () {
    Route::post("register", "api\AuthController@register");
    Route::post("login", "api\AuthController@login");
});

/*Products Routes*/
Route::group(["prefix" => "products"], function () {
    Route::get("/", "api\ProductsController@index");
    Route::get("/{id}", "api\ProductsController@show");
    Route::get("/search/{name}", "api\ProductsController@search");
});


/*Protected Routes*/
Route::group(["middleware" => ["auth:sanctum"]], function () {

    /*Products Routes*/
    Route::group(["prefix" => "products"], function () {
        Route::put("update/{id}", "api\ProductsController@update");
        Route::delete("delete/{id}", "api\ProductsController@destroy");
        Route::post("/create","api\ProductsController@store");
    });

    /*Cart Routes*/
    Route::group(["prefix" => "cart"], function () {
        Route::get("/", "api\CartController@index");
        Route::post("/store", "api\CartController@store");
        Route::delete("/delete/{id}", "api\CartController@remove");
        Route::delete("/clear", "api\CartController@clear");
        Route::put("/update", "api\CartController@update");
        Route::put("/update-status/{id}", "api\CartController@updateStatusOrderDetails");
        Route::post("/store-orderdetails", "api\CartController@addCartItemsToOrderDetails");
        Route::get("/showOrderDetailsItem/{id}", "api\CartController@showSpecifiedOrderDetailsItem");
    });

    /*Order Routes, for user*/
    Route::group(["prefix" => "order"], function () {
        Route::get("/", "api\OrdersController@index");
    });

    Route::post("/logout", "api\AuthController@logout");

//    Route::apiResource("roles", RolesController::class);
//    Route::apiResource('permissions',PermissionsController::class);

});
