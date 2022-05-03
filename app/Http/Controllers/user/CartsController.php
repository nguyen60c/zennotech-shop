<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartsController extends Controller
{
    /*
     * Get current user id
     */
    public function getUserId(){
        return auth()->user()->id;
    }

    /*
     * Display product items in specified user cart
     */
    public function index(){
        $specified_user_cart = Cart::find($this->getUserId());

        $cart_items = $specified_user_cart->paginate(15);

        return view("cart.index",compact("cart_items"));
    }

    /*
     * Add specified product when user click "Addtocart"
     *  Imediately adding one in cart item
     */
    public function store(Request $request){
        $test = Cart::find($request->id)->get()->toArray();
        ddd($test);


        $check_quantity_request = $request->quantity;


        if($check_quantity_request > 0){
            $check_quantity_db = Cart::find($request->id)->get()->toArray();
        }

    }
}
