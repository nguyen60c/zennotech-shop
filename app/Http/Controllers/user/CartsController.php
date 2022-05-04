<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartsController extends Controller
{
    private $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    /*
     * Get current user id
     */
    public function getUserId()
    {
        return auth()->user()->id;
    }

    /*
     * Display product items in specified user cart
     */
    public function index()
    {
        $specified_user_cart = Cart::where("user_id", $this->getUserId());

        $cart_items_raw = $specified_user_cart->paginate(15);
        $cart_items = array();

        foreach ($cart_items_raw as $item) {
            $product_item = Product::where("id", $item->product_id)->get()->toArray();
            $product_item[0]["quantity_item"] = $item->quantity;
            $product_item[0]["cart_id"] = $item->id;
            $cart_items = array_merge($cart_items, $product_item);
        }

        return view("carts.index", compact("cart_items"));
    }

    /*
     * Add specified product when user click "Addtocart"
     *  Imediately adding one in cart item
     */
    public function store(Request $request)
    {
        $check_quantity_request = $request->quantity;

        if ($check_quantity_request == 1) {

            $cart_items_db = Cart::where("product_id",$request->id)
            ->where("user_id",$this->getUserId());

            $check_item_exist = $cart_items_db->get()->toArray();

            if (count($check_item_exist) > 0) {
                $cart_items_db->update(["quantity" => ($request->quantity + 1)]);

            } else {

                $this->cart->user_id = $this->getUserId();
                $this->cart->product_id = $request->id;
                $this->cart->quantity = $request->quantity;
                $this->cart->price = $request->price;
                $this->cart->save();
            }
            return redirect()->route("cart.index")
                ->with('success_msg', 'Item is Added to Cart!');
        }
        return redirect()->route("users.products.index");
    }

    /*
     * Update quantity of specified existing item in cart
     */
    public function update(Request $request){
        if ($request->quantity > 0) {

            Cart::where("user_id", $this->getUserId())
                ->where("product_id", $request->id)
                ->update(["quantity" => $request->quantity]);

            return redirect()
                ->route("cart.index")
                ->with('success_msg', 'Cart is Updated!');
        }

        return redirect()
            ->route("cart.index");
    }

    /*
     * Delete specified existing item in cart
     */
    public function destroy(Request $request){
        Cart::where("product_id",$request->product_id)
            ->where("user_id",$this->getUserId())
            ->delete();
        return redirect()->route("cart.index");
    }


    /*
     * Delete all existing items in cart
     */
    public function clear()
    {
        Cart::where("user_id", $this->getUserId())->delete();
        return redirect()->route("cart.index");
    }
}
