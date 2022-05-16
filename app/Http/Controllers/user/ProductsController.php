<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /*
     * Display a listing of products data
     */
    public function index(){
        $products = Product::latest()->paginate(6);

        return view("products.index",compact("products"));
    }

    public function search(Request $request){

        $products = Product::where("name","like","%".$request->searching."%")->paginate(6);
        return view("products.show",compact("products"));

    }
}
