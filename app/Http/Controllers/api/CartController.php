<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;

class CartController extends Controller
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

    /*Update status order details*/
    public function updateStatusOrderDetails(Request $request,$id){
        $newOrderDetails = Order::where("order_details_id", $id)
            ->where("user_id", $request->user_id)
            ->update(["status" => $request->status]);
        return $newOrderDetails;
    }

    /*Display order_details for current user*/
    public function displayOrderDetailsItemsCurrentUser()
    {
        abort_if(Gate::denies('order_details_access'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');
        $details_raw = Order_details::
        select("id", "quantity", "user_id", "product_id",
            DB::raw('DATE(created_at) as time'), "created_at")
            ->where("user_id", $this->getUserId())
            ->latest()
            ->get()->toArray();

        $data = $details_raw;
        $arr_time = [];
        $arr_temp = [];

        foreach ($data as $item) {
            array_push($arr_time, $item["time"]);
        }

        $unique_val_arr = array_values(array_unique($arr_time));

        foreach ($data as $item) {
            if (array_search($item["time"], $unique_val_arr) >= 0) {
                $status = Order::where("order_details_id", $item["id"])->get()->toArray();
                $product = Product::where("id", $item["product_id"])->get()->toArray();
                $item["position"] = array_search($item["time"], $unique_val_arr);
                $item["product_name"] = $product[0]["name"];
                if (isset($status[0]["status"])) {
                    $item["status"] = $status[0]["status"];
                } else {
                    $item["status"] = "bị lỗi";
                }
                $item["total"] = $item["quantity"] * $product[0]["price"];
                array_push($arr_temp, $item);
            }
        }
        return $arr_temp;
    }

    public function addCartItemsToOrderDetails()
    {

        $cart_items_raw = Cart::where("user_id", $this->getUserId());
        $cart_items = $cart_items_raw->get()->toArray();

        if (count($cart_items) > 0) {
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
            $cart_items_raw->delete();
        }
        return "Cart items are not available in cart!";
    }

    public function showSpecifiedOrderDetailsItem(Request $request, $id)
    {
        $order_details = Order::where("order_details_id", $id)
            ->where("user_id", $request->user_id)->get();
        if (count($order_details) == 1) {
            return $order_details;
        } else {
            return "There are no order details in your order!!!";
        }
    }
}
