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

Route::group(["namespace" => "App\Http\Controllers"], function () {

    /*Customer Routes*/
    Route::get("/","user\ProductsController@index")
        ->name("users.products.index");

    /**
     * Guest Routes
     */
    Route::group(["middleware" => ["guest"]], function () {

        /**
         * Register Routes
         */
        Route::get("/register", "auth\RegisterController@show")->name("register.show");
        Route::post("/register", "auth\RegisterController@register")->name("register.perform");

        /**
         * Login Routes
         */
        Route::get("/login", "auth\LoginController@show")->name("login.show");
        Route::post("/login", "auth\LoginController@login")->name("login.perform");
    });

    Route::group(["middleware" => ["auth"]], function () {

        /**
         * Logout Routes
         */
        Route::get("/logout", "home\LogoutController@perform")->name("logout.perform");

        Route::group(["prefix"=>"cart"],function(){

            Route::get("/","user\CartsController@index")->name("cart.index");
            Route::post("/","user\CartsController@store")->name("cart.store");
            Route::post("/update","user\CartsController@update")->name("cart.update");
            Route::delete("/clear","user\CartsController@clear")->name("cart.clear");
            Route::post("/delete","user\CartsController@destroy")->name("cart.destroy");
        });

        Route::group(["prefix" => "order_details"],function(){
            Route::get("/","user\Order_detailsController@index")
                ->name("users.order_details.index");
            Route::post("/","user\Order_detailsController@storeCartItems")
                ->name("users.order_details.store");
        });

        Route::group(["prefix" => "user/order"],function(){
            Route::get("/","user\OrdersController@index")->name("users.order.index");
        });


        /*
         * User Routes
         */
        Route::group(["prefix" => "admin"], function () {

            Route::get("/dashboard","admin\HomeController@index")
                ->name("admin.dashboard");

            Route::group(["prefix" => "users"], function () {
                Route::get("/", "admin\UserController@index")
                    ->name("admin.users.index");
                Route::get("/create", "admin\UserController@create")->name("admin.users.create");
                Route::post("/create", "admin\UserController@store")->name("admin.users.store");
                Route::get("/{user}/show", "admin\UserController@show")->name("admin.users.show");
                Route::get("/{user}/edit", "admin\UserController@edit")->name("admin.users.edit");
                Route::patch("/{user}/update", "admin\UserController@upate")->name("admin.users.update");
                Route::delete("/{user}/delete", "admin\UserController@destroy")->name("admin.users.destroy");
            });

            /*
             * Products Routes
             */
            Route::group(["prefix" => "products"], function () {
                Route::get("/", "admin\ProductsController@index")
                    ->name("admin.products.index");
                Route::get("/create", "admin\ProductsController@create")
                    ->name("admin.products.create");
                Route::post("/create", "admin\ProductsController@store")
                    ->name("admin.products.store");
                Route::get("/{product}/show", "admin\ProductsController@show")
                    ->name("admin.products.show");
                Route::get("/{product}/edit", "admin\ProductsController@edit")
                    ->name("admin.products.edit");
                Route::patch("/{product}/update", "admin\ProductsController@upate")
                    ->name("admin.products.update");
                Route::delete("/{product}/delete", "admin\ProductsController@destroy")
                    ->name("admin.products.destroy");
            });

            /*
             * Orderss Routes
             */
            Route::group(["prefix"=>"orders"],function(){

            });

            Route::resource('roles', "admin\RolesController");
            Route::resource('permissions',"admin\PermissionsController");
        });
    });
});
