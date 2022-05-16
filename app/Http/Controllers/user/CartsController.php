<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\api\CartController;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CartsController extends Controller
{
    private $cart;
    private $total_price;
    private $order_details;
    private $data = array();
    private $data_request_cart_item = array();


    public function __construct()
    {
        $this->cart = new Cart();
        $this->order_details = new Order_details();
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
        abort_if(Gate::denies('cart.index'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $specified_user_cart = Cart::where("user_id", $this->getUserId());

        $cart_items_raw = $specified_user_cart->paginate(10);
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
     * @param Product_id $id
     *
     * Add specified product when user click "Addtocart"
     *  Imediately adding one in cart item
     */
    public function store($id)
    {
        abort_if(Gate::denies('cart.store'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->data_request_cart_item = 1;

        $cart_items_db = Cart::where("product_id", $id)
            ->where("user_id", $this->getUserId());

        $is_exist_cart_item = $cart_items_db->get()->toArray();


        $this->createCartItem($is_exist_cart_item, $cart_items_db, $id);


        return redirect()->route("cart.index");
    }

    public function getDataRequest($data){
        return $data;
    }

    /*
     * Delete specified existing item in cart
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('cart.destroy'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        Cart::where("product_id", $id)
            ->where("user_id", $this->getUserId())
            ->delete();
        return redirect()->route("cart.index");
    }


    /*
     * Delete all existing items in cart
     */
    public function clear()
    {
        abort_if(Gate::denies('cart.clear'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        Cart::where("user_id", $this->getUserId())->delete();
        return redirect()->route("cart.index");
    }

    /*Add cart items to order_details after click proceed to checkout*/
    public function switchToCheckoutPage(Request $request){

        $cart_items = $request->all();

        foreach($cart_items as $item_arr){

            foreach ($item_arr as $item){

                Cart::where("id",$item["cart_id"])
                    ->where("product_id",$item["id_origin"])
                    ->update(["quantity" => $item["cart_item_quantity"]]);
            }
        }
        return $this->data_request_cart_item;
    }

    /*
     * Add cart items into after user click "order" in checkout page
     * */
    public function storeCartItems(Request $request)
    {

        abort_if(Gate::denies('cart.clear'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validation = $request->validate([
            'name_customer' => 'required|min:3',
            'customer_address' => 'required|min:5',
            'customer_phone' => 'required|regex:/(0)[0-9]{9}/'
        ]);


        $curr_user_cart = Cart::where("user_id", $this->getUserId());

        $cart_items = $curr_user_cart->get()->toArray();

        $this->isValidOrNot($cart_items);

        foreach ($cart_items as $item) {



            $order_details = new Order_details();
            $order_details->customer_name = $validation["name_customer"];
            $order_details->customer_address = $validation["customer_address"];
            $order_details->customer_phone = $validation["customer_phone"];

            $order_details->quantity = $item["quantity"];
            $order_details->user_id = $item["user_id"];
            $order_details->product_id = $item["product_id"];
            $order_details->item_price = $item["price"];
            $order_details->save();

            $order = new Order();
            $order->user_id = $item["user_id"];
            $order->order_details_id = $order_details->id;
            $order->total_price = $order_details->item_price;
            $order->save();
        }


        $curr_user_cart->delete();

        return redirect()->route("users.products.index");
    }

    /*Display checkout form checkout page*/
    public function displayCheckoutPage(Request $request)
    {

        $order_details_items = Cart::where("user_id", $this->getUserId());
        $details = $order_details_items->get();

        $time_created = 0;

        foreach ($details as $item) {
            $product = Product::where("id", $item["product_id"])->get()->toArray()[0];
            $product["quantity_temp"] = $item["quantity"];
            $product["date_update"] = $item["updated_at"]->toDateString();
            $product["hour_update"] = $item["updated_at"]->toTimeString();
            $this->total_price += $item->quantity * $product["price"];
            array_push($this->data, (array)$product);
        }


        $data = $this->data;
        $total = $this->total_price;

        return view("orders_details.index")
            ->with(compact("data"))
            ->with(compact("total"))
            ->with(compact("time_created"));
    }

    public function checkQuantityCartItem()
    {
        $cart_item_list = Cart::where("user_id", $this->getUserId())->get()->toArray();


        $data = array();

        foreach ($cart_item_list as $key=>$item) {

            $product_item = Product::select("quantity", "price", "id")
                ->where("id", $item["product_id"])->get()->toArray()[0];

            $product_item["cart_id"] = $item["id"];

            $data = array_merge($data,[$key => $product_item]);
        }

        return $data;
    }


    /*
     * Make sure all the cart_items have the same creator_id
     *
     * @param Cart $cart_items
     *
     * @return Illuminate\Routing\Redirector
     */
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


    /*
     * @param Array $is_exist_cart_item
     * @param Cart $cart_items_db
     * @param int $id
     *
     * @return Illuminate\Routing\Redirector
     */
    public function createCartItem($is_exist_cart_item, $cart_items_db, $id){

        if (count($is_exist_cart_item) > 0) {
            $is_exist_cart_item = $cart_items_db->get()->toArray()[0];

            $product_item = Product::where("id", $id)->get()->toArray()[0];

            if ($product_item["quantity"] >= $is_exist_cart_item["quantity"] + 1) {
                $cart_items_db->update(["quantity" =>
                    $is_exist_cart_item["quantity"] + 1]);
            } else {
                return redirect()->route("users.products.index");
            }
        } else {
            $product_item_db = Product::where("id", $id);
            $is_exist_product_item = $product_item_db->get()->toArray()[0];

            if (count($is_exist_product_item) > 0) {
                $this->cart->user_id = $this->getUserId();
                $this->cart->product_id = $id;
                $this->cart->quantity = 1;
                $this->cart->price = $is_exist_product_item["price"];
                $this->cart->save();
            } else {
                return redirect()->route("users.products.index");
            }
        }
    }
}
