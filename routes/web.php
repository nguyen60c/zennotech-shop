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
    Route::get("/", "user\ProductsController@index")
        ->name("users.products.index");
    Route::post("/search-products", "user\ProductsController@search")
        ->name("users.products.search");
    Route::get("/show-details/{product_id}","user\ProductsController@detailsProduct")
    ->name("users.products.details");

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

        Route::group(["prefix" => "cart"], function () {

            Route::get("/", "user\CartsController@index")->name("cart.index");
            Route::post("/cart-item", "user\CartsController@store")->name("cart.store");
//            Route::put("/update", "user\CartsController@update")->name("cart.update");
            Route::delete("/clear", "user\CartsController@clear")->name("cart.clear");
            Route::delete("/delete/{id}", "user\CartsController@destroy")->name("cart.destroy");
            Route::post("/add-to-order-details",
                "user\CartsController@addCartItemsToOrderDetails")->name("cart.ordersDetails.add");
            Route::post("/check-quantity","user\CartsController@checkQuantity");
            Route::get("/checkout", "user\CartsController@displayCheckoutPage")->name("cart.checkout.index");
            Route::post("/checkout", "user\CartsController@createOrderDetailsItem")->name("cart.checkout.store");
            Route::get("/checkQuantityCartItem", "user\CartsController@checkQuantityCartItem")->name("cart.checkQuantityCartItem");
        });


        Route::group(["prefix" => "user/order"], function () {
            Route::get("/", "user\OrdersController@index")->name("users.order.index");
            Route::get("/show/{orderDetailItem_id}","user\OrdersController@show")->name("users.order.show");
            Route::get("/print", "user\OrdersController@printPdf")->name("users.order.print");
        });


        /*
         * Admin/Seller Routes
         */
        Route::group(["prefix" => "admin"], function () {

            Route::get("/dashboard", "admin\HomeController@index")
                ->name("admin.dashboard");

            Route::group(["prefix" => "users"], function () {
                Route::get("/", "admin\UserController@index")
                    ->name("admin.users.index");
                Route::get("/create", "admin\UserController@create")->name("admin.users.create");
                Route::post("/create", "admin\UserController@store")->name("admin.users.store");
                Route::get("/{user}/show", "admin\UserController@show")->name("admin.users.show");
                Route::get("/{user}/edit", "admin\UserController@edit")->name("admin.users.edit");
                Route::patch("/{user}/update", "admin\UserController@update")->name("admin.users.update");
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
                Route::patch("/{product}/update", "admin\ProductsController@update")
                    ->name("admin.products.update");
                Route::delete("/{product}/delete", "admin\ProductsController@destroy")
                    ->name("admin.products.destroy");
            });

            /*
             * Orderss Routes
             */
            Route::group(["prefix" => "orders"], function () {
                Route::get("/{id}", "admin\OrdersController@index")
                    ->name("admin.orders.index");
//                Route::get("/list","admin\OrdersController@show")
//                    ->name("admin.orders.show");
                Route::get("/print/{id}", "admin\OrdersController@printPdf")
                    ->name("admin.orders.print");
                Route::get("/{order_details_id}/show",
                    "admin\OrdersController@displayOrderDetailsItem")
                    ->name("admin.orders.displayOrderDetailsItem");
                Route::post("/update", "admin\OrdersController@update")
                    ->name("admin.orders.update");
                Route::get("order/history/show","admin\OrdersController@ordersHistory")->name("admin.orders.history");
                Route::get("order/history/show-details/{id}","admin\OrdersController@showDetailsOrderItemsHistory")->name("admin.orders.details");
                Route::post("order/history/show-details/{id}","admin\OrdersController@showDetailsOrderItemsHistory")->name("admin.orders.details");
            });

            Route::resource('roles', "admin\RolesController");
            Route::resource('permissions', "admin\PermissionsController");
        });
    });


});
