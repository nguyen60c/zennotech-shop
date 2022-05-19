<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    private $cart;
    public function __construct()
    {
        $this->cart = new Cart();
    }

    /*
     * Display a listing of products data
     */
    public function index(){
        $products = Product::latest()->paginate(6);

        $cartItems = Cart::select("product_id")->distinct()->get();

        return view("products.index",compact("products"))
            ->with(compact("cartItems"));
    }

    public function search(Request $request){

        $products = Product::where("name","like","%".$request->searching."%")->paginate(6);
        $cartItems = $this->cart->totalDistinctItems();
        return view("products.show",compact("products"))->with(compact("cartItems"));

    }

    public function detailsProduct($id){

        $productItem = Product::where("id",$id)->get()->toArray()[0];
        $creator = User::where("id",$productItem["creator_id"])->get("username")->toArray()[0]["username"];
        $cartItems = Cart::all();

        $previousUrl = url()->previous();
        if(isset(explode("?",$previousUrl)[1])){
            $previousPage = explode("?",$previousUrl)[1];
            $previousPage = explode("=",$previousPage)[1];
        }else{
            $previousPage = "";
        }

        return view("products.show-details")
            ->with(compact("productItem"))
            ->with(compact("cartItems"))
            ->with(compact("previousPage"))
            ->with(compact("creator"));
    }
}
