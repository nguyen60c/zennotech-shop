<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ProductsController;
use App\Http\Controllers\api\CartController;

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
    Route::post("register", [AuthController::class, "register"]);
    Route::post("login", [AuthController::class, "login"]);
});

/*Products Routes*/
Route::group(["prefix" => "products"], function () {
    Route::get("/", [ProductsController::class, "index"]);
    Route::get("/{id}", [ProductsController::class, "show"]);
    Route::get("/search/{name}", [ProductsController::class, "search"]);
});


/*Protected Routes*/
Route::group(["middleware" => ["auth:sanctum"]], function () {

    /*Products Routes*/
    Route::group(["prefix" => "products"], function () {
        Route::put("update/{id}", "api\ProductsController@update");
        Route::delete("delete/{id}", "api\ProductsController@destroy");
        Route::post("/create", "api\ProductsController@store");
    });

    /*Cart Routes*/
    Route::group(["prefix" => "cart"], function () {
        Route::get("/", [CartController::class, "index"]);
        Route::post("/store", [CartController::class, "store"]);
        Route::delete("/delete/{id}", [CartController::class, "remove"]);
        Route::delete("/clear", [CartController::class, "clear"]);
        Route::put("/update/{id}", [CartController::class, "update"]);
        Route::put("/update-status/{id}", [CartController::class, "updateStatusOrderDetails"]);
        Route::post("/store-orderDetails", [CartController::class, "addCartItemsToOrderDetails"]);
        Route::get("/showOrderDetailsItem/{id}", [CartController::class, "showSpecifiedOrderDetailsItem"]);
        Route::post("/order_details", [CartController::class, "displayOrderDetailsItemsCurrentUser"]);
    });

    /*Order Routes, for user*/
    Route::group(["prefix" => "order"], function () {
        Route::get("/", "api\OrdersController@index");
    });

    Route::post("/logout", "api\AuthController@logout");

//    Route::apiResource("roles", RolesController::class);
//    Route::apiResource('permissions',PermissionsController::class);

});
