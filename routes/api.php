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
    Route::post("register", [AuthController::class, "register"]);
    Route::post("login", [AuthController::class, "login"]);
});

/*Products Routes*/
Route::group(["prefix" => "products"], function () {
    Route::get("/", [\App\Http\Controllers\api\ProductsController::class, "index"]);
    Route::get("/product/{id}", [\App\Http\Controllers\api\ProductsController::class, "show"]);
    Route::get("/search/{name}", [\App\Http\Controllers\api\ProductsController::class, "search"]);
});


/*Protected Routes*/
Route::group(["middleware" => ["auth:sanctum"]], function () {

    /*Products Routes*/
    Route::group(["prefix" => "products"], function () {
        Route::put("product/update/{id}",
            [\App\Http\Controllers\api\ProductsController::class, "update"]);
        Route::delete("product/delete/{id}",
            [\App\Http\Controllers\api\ProductsController::class, "destroy"]);
        Route::post("/create",
            [\App\Http\Controllers\api\ProductsController::class, "store"]);
    });

    /*Cart Routes*/
    Route::group(["prefix" => "cart"], function () {
        Route::get("/",[\App\Http\Controllers\api\CartController::class,"index"]);
        Route::post("/store",[\App\Http\Controllers\api\CartController::class,"store"]);
        Route::delete("/delete/{id}",[\App\Http\Controllers\api\CartController::class,"remove"]);
        Route::delete("/clear",[\App\Http\Controllers\api\CartController::class,"clear"]);
        Route::post("/update",[\App\Http\Controllers\api\CartController::class,"update"]);
    });

    /*Order Details Routes*/
    Route::group(["prefix"=>"order-details"],function(){
        Route::post("/add",[\App\Http\Controllers\api\Order_detailsController::class,"add"]);
        Route::get("/",[\App\Http\Controllers\api\Order_detailsController::class,"index"]);
        Route::post("/update/{id}",[\App\Http\Controllers\api\Order_detailsController::class,"updateStatusOrderDetails"]);
        Route::post("/show/{id}",[\App\Http\Controllers\api\Order_detailsController::class,"show"]);
    });

    /*Order Routes, for user*/
    Route::group(["prefix"=>"order"],function(){
        Route::get("/",[\App\Http\Controllers\api\OrdersController::class,"index"]);
    });

    Route::post("/logout", [\App\Http\Controllers\api\AuthController::class, "logout"]);

//    Route::apiResource("roles", RolesController::class);
//    Route::apiResource('permissions',PermissionsController::class);

    Route::group(["prefix"=>"roles"],function(){
        Route::get("/",[\App\Http\Controllers\admin\RolesController::class,"index"]);
    });
});
