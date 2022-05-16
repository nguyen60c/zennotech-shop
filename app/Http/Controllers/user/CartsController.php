<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\api\CartController;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetails;
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
        $this->order_details = new OrderDetails();
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
        abort_if(
            Gate::denies('cart.index'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $cartItems = Cart::where("user_id", $this->getUserId());

        $cartItems = $cartItems->paginate(10);
        $listCartItems = array();

        /*Get cart items*/
        foreach ($cartItems as $item) {
            $productItems = Product::where("id", $item->product_id)->get()->toArray();
            $productItems[0]["quantity_item"] = $item->quantity;
            $productItems[0]["cart_id"] = $item->id;
            $listCartItems = array_merge($listCartItems, $productItems);
        }

        return view("carts.index", compact("listCartItems"));
    }

    /*
     * @param Product_id $id
     *
     * Add specified product when user click "Addtocart"
     *  Imediately adding one in cart item
     */
    public function store($product_id)
    {
        abort_if(
            Gate::denies('cart.store'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $cart_item = Cart::where("product_id", $product_id)
            ->where("user_id", $this->getUserId());

        $is_empty = $cart_item->get()->toArray();


        $this->createCartItem($is_empty, $cart_item, $product_id);


        return redirect()->route("cart.index");
    }

    /*
     * Delete specified existing item in cart
     */
    public function destroy($id)
    {
        abort_if(
            Gate::denies('cart.destroy'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

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
        abort_if(
            Gate::denies('cart.clear'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        Cart::where("user_id", $this->getUserId())->delete();
        return redirect()->route("cart.index");
    }

    /*Add cart items to order_details after click proceed to checkout*/
    public function addCartItemsToOrderDetails(Request $request)
    {

        $cartItems = $request->all();

        /*Check only seller*/
        foreach ($cartItems as $itemArr) {

            foreach ($itemArr as $key => $item) {

                // array_push($test, $key);
                if (isset($itemArr[$key + 1])) {

                    if ($item["creatorId"] != $itemArr[$key + 1]["creatorId"]) {
                        return "false";
                    }
                }
            }
        }

        foreach ($cartItems as $itemArr) {

            foreach ($itemArr as $item) {

                Cart::where("id", $item["cartId"])
                    ->where("product_id", $item["productId"])
                    ->update(["quantity" => $item["userInputQuantity"]]);
            }
        }
        return $this->data_request_cart_item;
    }



    /*
     * Add cart items into after user click "order" in checkout page
     * */
    public function createOrderDetailsItem(Request $request)
    {
        abort_if(
            Gate::denies('cart.clear'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $cartId = explode("|",$request->cartid);
        if($cartId[0] === ""){
            array_shift($cartId);
        }

        $validation = $request->validate([
            'name_customer' => 'required|min:3',
            'customer_address' => 'required|min:5',
            'customer_phone' => 'required|regex:/(0)[0-9]{9}/'
        ]);

        $test = array();
        foreach($cartId as $item){
            $cart = Cart::where("user_id", $this->getUserId())
                ->where("id", $item)
                ->orderBy("creator_id", "DESC");

            $cartItems = $cart->get()->toArray();

            $this->handleCreating($cartItems, $validation);

            $cart->delete();
        }

        return redirect()->route("users.products.index");
    }

    /*Display checkout form checkout page*/
    public function displayCheckoutPage(Request $request)
    {

        $cartItem = Cart::where("user_id", $this->getUserId())
            ->orderBy('updated_at','DESC')->first();

        $time_created = 0;

        $cartItems = Cart::where("user_id", $this->getUserId())
            ->where("updated_at",$cartItem["updated_at"])->get()->toArray();

//        ddd($cartItems);
        foreach ($cartItems as $key => $item) {
            $product = Product::where("id", $item["product_id"])->get()->toArray()[0];
            $product["quantity_temp"] = $item["quantity"];
            $product["date_update"] = date('d-m-Y', strtotime($item["created_at"]));
            $product["hour_update"] = date('h:m:s', strtotime($item["created_at"]));
            $product["cart_id"] = $cartItems[$key]["id"];
            $time_created = $item["created_at"];
            $this->total_price += $item["quantity"] * $product["price"];
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

        foreach ($cart_item_list as $key => $item) {

            $product_item = Product::select("quantity", "price", "id", "creator_id")
                ->where("id", $item["product_id"])->get()->toArray()[0];

            $product_item["cart_id"] = $item["id"];

            $data = array_merge($data, [$key => $product_item]);
        }

        return $data;
    }


    /*
     * @param Cart $cart_items
     *
     * @return Illuminate\Routing\Redirector
     */
    public function handleCreating($cartItems, $validation)
    {

        $totalPrice = 0;

        /*Total price*/

        foreach ($cartItems as $key => $item) {
            $totalPrice += $item["price"];
        }

        /*Just Creating a newly order details every loops*/
        foreach ($cartItems as $key => $item) {

            $productItem = Product::where("id", $item["product_id"]);
            $productQuantity = $productItem->get()->toArray()[0]["quantity"];
            $quantity = $productQuantity - $item["quantity"];

            if ($quantity < $item["quantity"] || $item["quantity"] < 0) {
                return redirect()->route("cart.index")
                    ->withErrors("Your input value quantity is invalid");
            }

            $orderDetailsItem = OrderDetails::create([
                "customer_name" => $validation["name_customer"],
                "customer_address" => $validation["customer_address"],
                "customer_phone" => $validation["customer_phone"],

                "quantity" => $item["quantity"],
                "user_id" => $item["user_id"],
                "product_id" => $item["product_id"],
                "item_price" => $item["price"],
                "creator_id" => $item["creator_id"]
            ]);


            $productItem->update(["quantity" =>
            $productQuantity - $item["quantity"]]);

            Order::create([
                "user_id" => $item["user_id"],
                "order_details_id" => $orderDetailsItem->id,
                "total_price" => $totalPrice,
                "creator_id" => $orderDetailsItem->creator_id
            ]);
        }
    }

    /*
     * Creating a new orderDetails Item
     *
     * @return creator_id
     * */
    public
    function storeOrderDetailsItem($item, $validation)
    {

        $orderDetailsItem = new OrderDetails();
        $orderDetailsItem->customer_name = $validation["name_customer"];
        $orderDetailsItem->customer_address = $validation["customer_address"];
        $orderDetailsItem->customer_phone = $validation["customer_phone"];

        $orderDetailsItem->quantity = $item["quantity"];
        $orderDetailsItem->user_id = $item["user_id"];
        $orderDetailsItem->product_id = $item["product_id"];
        $orderDetailsItem->item_price = $item["price"];
        $orderDetailsItem->creator_id = $item["creator_id"];
        $orderDetailsItem->save();

        $productItem = Product::where("id", $item["product_id"]);
        $productQuantity = $productItem->get()->toArray()[0]["quantity"];

        $quantity = $productQuantity - $item["quantity"];

        if ($quantity < $item["quantity"] || $item["quantity"] < 0) {
            return redirect()->route("cart.index")
                ->withErrors("Your input value quantity is invalid");
        }

        $productItem->update(["quantity" => $productQuantity["quantity"] - $item["quantity"]]);

        $order = new Order();
        $order->user_id = $item["user_id"];
        $order->order_details_id = $orderDetailsItem->id;
        $order->total_price = $orderDetailsItem->item_price;
        $order->creator_id = $orderDetailsItem->creator_id;
        $order->save();
    }


    /*
     * @param Array $is_exist_cart_item
     * @param Cart $cart_items_db
     * @param int $id
     *
     * @return Illuminate\Routing\Redirector
     */
    public
    function createCartItem($is_empty, $cart_items, $product_id)
    {

        if (count($is_empty) > 0) {

            $cart_item = $cart_items->get()->toArray()[0];

            $product_item = Product::where("id", $product_id)->get()->toArray()[0];

            if ($product_item["quantity"] >= $cart_item["quantity"] + 1) {
                $cart_items->update(["quantity" =>
                $cart_item["quantity"] + 1]);
            } else {
                return redirect()->route("users.products.index");
            }
        } else {

            $product_item = Product::where("id", $product_id);
            $is_empty_product = $product_item->get()->toArray()[0];

            if (count($is_empty_product) > 0) {

                $product_item = $is_empty_product;
                $this->cart->user_id = $this->getUserId();
                $this->cart->product_id = $product_id;
                $this->cart->quantity = 1;
                $this->cart->creator_id = $product_item["creator_id"];
                $this->cart->price = $product_item["price"];
                $this->cart->save();
            } else {
                return redirect()->route("users.products.index");
            }
        }
    }
}
