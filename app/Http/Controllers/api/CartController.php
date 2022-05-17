<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        abort_if(Gate::denies('cart.index'),
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
        abort_if(Gate::denies('cart.store'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->quantity > 0) {

            /*Get orders details items*/
            $cart_items = Cart::
            where("product_id", $request->product_id)
                ->where("user_id", $this->getUserId());

            $cart_items_collection = $cart_items->get();
            $cart_items_arr = $cart_items_collection->toArray();

            /*Check cart items exist*/
            if (count($cart_items_arr) > 0) {
                $product = Product::where("id", $request->product_id)->get()->toArray();
                $condition_quantity = $product[0]["quantity"] -
                    ($request->quantity + $cart_items_collection[0]["quantity"]);

                if ($condition_quantity >= 0) {
                    $cart_items->update(["quantity" =>
                        $request->quantity + $cart_items_collection[0]["quantity"]]);
                    return $cart_items_collection;
                } else {
                    return "add to cart failed";
                }
            } else {
                $product = Product::where("id", $request->product_id)->get()[0];

                if ($product["quantity"] >= $request->quantity) {
                    $this->cart->user_id = $this->getUserId();
                    $this->cart->quantity = $request->quantity;
                    $this->cart->product_id = $request->product_id;
                    $this->cart->price = $product["price"];
                    $this->cart->save();
                    return "Add to cart success";
                } else {
                    return "Quantity is not equal with quantity of product";
                }

            }
            return "Add to cart failed";
        }

    }

    public function remove($id)
    {
        abort_if(Gate::denies('cart.destroy'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

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

        abort_if(Gate::denies('cart.clear'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $isSuccess = Cart::where("user_id", $this->getUserId())
            ->delete();
        if ($isSuccess) {
            return "Cart is cleared now! ";
        } else {
            return "Error!!!";
        }
    }

    /*
     * @param Product $id
     * @param Request $request
     */
    public function update(Request $request, $id){
        abort_if(Gate::denies('cart.update'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->quantity > 0) {

            /*Get orders details items*/
            $cart_items = Cart::
            where("product_id", $id)
                ->where("user_id", $this->getUserId());

            $cart_items_collection = $cart_items->get();
            $cart_items_arr = $cart_items_collection->toArray()[0];

            /*Check cart items exist*/
            if (count($cart_items_arr) > 0) {
                $product = Product::where("id", $id)->get()->toArray();
                $condition_quantity = $product[0]["quantity"] -
                    ($request->quantity + $cart_items_collection[0]["quantity"]);

                if ($condition_quantity >= 0) {
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

    /*
     * @param Request $request
     * @param Order_details
     *
     * Update status order details*/
    public function updateStatusOrderDetails(Request $request,$id){
        abort_if(Gate::denies('cart.updateStatusOrderDetails'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $newOrderDetails = Order::where("order_details_id", $id)
            ->where("user_id", $request->user_id)
            ->update(["status" => $request->status]);
        return $newOrderDetails;
    }

    /*Display order_details for current user*/
    public function displayOrderDetailsItemsCurrentUser()
    {
        abort_if(Gate::denies('cart.displayOrderDetailsItemsCurrentUser'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $details_raw = OrderDetails::
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

    public function addCartItemsToOrderDetails(Request $request)
    {
        abort_if(Gate::denies('cart.addCartItemsToOrderDetails'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validation = $request->validate([
            'customer_name' => 'required|min:3',
            'customer_address' => 'required|min:5',
            'customer_phone' => 'required|regex:/(0)[0-9]{9}/'
        ]);

        $curr_user_cart = Cart::where("user_id", $this->getUserId());

        $cart_items = $curr_user_cart->get()->toArray();

        $this->isValidOrNot($cart_items);

        foreach ($cart_items as $item) {

            $order_details = new OrderDetails();

            $order_details->customer_name = $validation["customer_name"];
            $order_details->customer_address = $validation["customer_address"];
            $order_details->customer_phone = $validation["customer_phone"];

            $order_details->quantity = $item["quantity"];
            $order_details->user_id = $item["user_id"];
            $order_details->product_id = $item["product_id"];
            $order_details->item_price = $item["price"];
            $order_details->save();

            $product_item = Product::where("id", $item["product_id"]);
            $product_quantity = $product_item->get()->toArray()[0];

            $quantity_update = $product_quantity["quantity"] - $item["quantity"];

            if($quantity_update < $item["quantity"] || $item["quantity"] < 0){
                return redirect()->route("cart.index")
                    ->withErrors("Your input value quantity is invalid");
            }

            $product_item->update(["quantity" => $product_quantity["quantity"] - $item["quantity"]]);

            $order = new Order();
            $order->user_id = $item["user_id"];
            $order->order_details_id = $order_details->id;
            $order->total_price = $order_details->item_price;
            $order->save();
        }


        $is_Success = $curr_user_cart->delete();

        if($is_Success){
            return "Success";
        }
        return "Cart items are not available in cart!";
    }

    public function showSpecifiedOrderDetailsItem(Request $request, $id)
    {
        abort_if(Gate::denies('cart.showSpecifiedOrderDetailsItem'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order_details = Order::where("order_details_id", $id)
            ->where("user_id", $request->user_id)->get();
        if (count($order_details) == 1) {
            return $order_details;
        } else {
            return "There are no order details in your order!!!";
        }
    }

    public function isValidOrNot($cart_items){
        $creator_array = array();

        foreach ($cart_items as $key => $item){
            $creator = Product::select("creator_id")->where("id",$item["product_id"])->get()->toArray()[0]["creator_id"];
            array_push($creator_array,$creator);
        }

        $creator_array = array_flip($creator_array);
        $result = array_unique($creator_array);

        /*Make sure that all the order_details_item have the same creator*/
        if(count($result) != 1){
            return redirect()->route("cart.checkout.index");
        }
    }
}
