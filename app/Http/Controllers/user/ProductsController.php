<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
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

    public function detailsProduct($id){

        $productItem = Product::where("id",$id)->get()->toArray()[0];
        $creator = User::where("id",$productItem["creator_id"])->get("username")->toArray()[0]["username"];
        return view("products.show-details")
            ->with(compact("productItem"))
        ->with(compact("creator"));
    }
}
