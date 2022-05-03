<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\roles\RolesController;
use App\Http\Controllers\admin\permissions\PermissionsController;
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

    /**
     * Home Routes
     */
    Route::get("/", "home\HomeController@index")->name("home.index");

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

        /*Customer Routes*/
        Route::get("/","user\ProductsController@index")
            ->name("users.products.index");

        /*
         * User Routes
         */
        Route::group(["prefix" => "admin"], function () {

            Route::get("/dashboard","admin\home\HomeController@index")
                ->name("admin.dashboard");

            Route::group(["prefix" => "users"], function () {
                Route::get("/", "admin\users\UserController@index")
                    ->name("admin.users.index");
                Route::get("/create", "admin\users\UserController@create")->name("admin.users.create");
                Route::post("/create", "admin\users\UserController@store")->name("admin.users.store");
                Route::get("/{user}/show", "admin\users\UserController@show")->name("admin.users.show");
                Route::get("/{user}/edit", "admin\users\UserController@edit")->name("admin.users.edit");
                Route::patch("/{user}/update", "admin\users\UserController@upate")->name("admin.users.update");
                Route::delete("/{user}/delete", "admin\users\UserController@destroy")->name("admin.users.destroy");
            });

            /*
             * Products Routes
             */
            Route::group(["prefix" => "products"], function () {
                Route::get("/", "admin\\products\\ProductsController@index")
                    ->name("admin.products.index");
                Route::get("/create", "admin\\products\\ProductsController@create")
                    ->name("admin.products.create");
                Route::post("/create", "admin\\products\\ProductsController@store")
                    ->name("admin.products.store");
                Route::get("/{product}/show", "admin\\products\\ProductsController@show")
                    ->name("admin.products.show");
                Route::get("/{product}/edit", "admin\\products\\ProductsController@edit")
                    ->name("admin.products.edit");
                Route::patch("/{product}/update", "admin\\products\\ProductsController@upate")
                    ->name("admin.products.update");
                Route::delete("/{product}/delete", "admin\\products\\ProductsController@destroy")
                    ->name("admin.products.destroy");
            });

            /*
             * Orderss Routes
             */
            Route::group(["prefix"=>"orders"],function(){

            });

            Route::resource('roles', "admin\\roles\\RolesController");
            Route::resource('permissions',"admin\\permissions\\PermissionsController");
        });
    });
});
