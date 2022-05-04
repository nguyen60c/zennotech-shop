<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Product;
use Illuminate\Http\Request;

class Order_detailsController extends Controller
{

    private $total_price;
    private $order_details;
    private $data = array();

    public function __construct()
    {
        $this->order_details = new Order_details();
    }

    public function getUserId(){
        return auth()->user()->id;
    }

    /*
     * Add cart items into after user click "order" in checkout page
     * */
    public function storeCartItems(Request $request){

    $curr_user_cart =Cart::where("user_id",$this->getUserId());

        $cart_items = $curr_user_cart->get()->toArray();

        foreach ($cart_items as $item) {
            $order_details = new Order_details();
            $order_details->quantity = $item["quantity"];
            $order_details->user_id = $item["user_id"];
            $order_details->product_id = $item["product_id"];
            $order_details->save();

            $order = new Order();
            $order->user_id = $item["user_id"];
            $order->order_details_id = $order_details->id;
            $order->save();
        }

        $curr_user_cart->delete();

        return redirect()->route("users.products.index");
    }

    /*Take request when user checkout cart items*/
    public function processToCheckout(){

    }

    /*Display checkout form checkout page*/
    public function index(){
        $order_details_items = Cart::where("user_id", $this->getUserId());
        $details = $order_details_items->get();

        foreach ($details as $item) {
            $product = Product::where("id", $item["product_id"])->get()->toArray()[0];
            $product["quantity_temp"] = $item["quantity"];
            $this->total_price += $item->quantity * $product["price"];
            array_push($this->data, (array)$product);
        }

        $data = $this->data;
        $total = $this->total_price;

        return view("orders_details.index")
            ->with(compact("data"))
            ->with(compact("total"));
    }

}
