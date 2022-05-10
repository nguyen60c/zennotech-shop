<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class OrdersController extends Controller
{
    private $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    public function getUserId()
    {
        return auth()->user()->id;
    }

    public function index()
    {
        abort_if(Gate::denies('cart_access'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');
        $cart = Cart::where("user_id", $this->getUserId())->get();
        if ($cart) {
            return $cart;
        } else {
            $message = "0 Items";
            return $message;
        }
    }

    public function store(Request $request)
    {


        if ($request->quantity > 0) {

            /*Get orders details items*/
            $cart_items = Cart::
            where("product_id", $request->id)
                ->where("user_id", $this->getUserId());

            $cart_items_collection = $cart_items->get();
            $cart_items_arr = $cart_items_collection->toArray();

            /*Check cart items exist*/
            if ($cart_items_arr) {
                $product = Product::where("id", $request->id)->get()->toArray();
                $condition_quantity = $product[0]["quantity"] -
                    ($request->quantity + $cart_items_collection[0]["quantity"]);

                if ($condition_quantity <= 0) {
                    $cart_items->update(["quantity" =>
                        $request->quantity + $cart_items_collection[0]["quantity"]]);
                    return $cart_items_collection;
                } else {
                    return "add to cart failed";
                }
            } else {
                $product = Product::where("id", $request->id)->get();


                if ($product->quantity >= $request->quantity) {
                    $this->cart->user_id = $this->getUserId();
                    $this->cart->quantity = $request->quantity;
                    $this->cart->product_id = $request->id;
                    $this->cart->save();
                } else {
                    return "Quantity is not equal with quantity of product";
                }

            }
            return "Add to cart failed";
        }
        return "fails";
    }

    public function remove($id)
    {

        $isSuccess = Cart::where("user_id", $this->getUserId())
            ->where("product_id", $id)
            ->delete();
        if ($isSuccess) {
            return "Cart items is successful deleted ";
        } else {
            return "Error!!!";
        }
    }

    public function clear(){

        $isSuccess = Cart::where("user_id", $this->getUserId())
            ->delete();
        if ($isSuccess) {
            return "Cart is cleared now! ";
        } else {
            return "Error!!!";
        }
    }

    public function update(Request $request){
        if ($request->quantity > 0) {

            /*Get orders details items*/
            $cart_items = Cart::
            where("product_id", $request->product_id)
                ->where("user_id", $this->getUserId());

            $cart_items_collection = $cart_items->get();
            $cart_items_arr = $cart_items_collection->toArray();

            /*Check cart items exist*/
            if ($cart_items_arr) {
                $product = Product::where("id", $request->id)->get()->toArray();
                $condition_quantity = $product[0]["quantity"] -
                    ($request->quantity + $cart_items_collection[0]["quantity"]);

                if ($condition_quantity <= 0) {
                    $cart_items->update(["quantity" =>
                        $request->quantity + $cart_items_collection[0]["quantity"]]);
                    return $cart_items_collection;
                } else {
                    return "add to cart failed";
                }
            } else {
                return "There are nothing in cart! Please fill me";
            }
        }
        return "Add to cart failed";
    }
}
