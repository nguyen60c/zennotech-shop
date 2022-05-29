<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Http\Controllers\traits\PermissionGateTraits;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartsController extends Controller
{
    use PermissionGateTraits;

    private $cart;
    private $prod;
    private $orderDetailsItem;

    public function __construct()
    {
        $this->cart = new Cart();
        $this->prod = new Product();
        $this->orderDetailsItem = new OrderDetails();
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
        $this->gateDeny('cart.index');

        $_cartItems = Cart::where("user_id", $this->getUserId())
            ->orderBy('creator_id', 'DESC')->paginate(10);
        $cartItems = array();
        $userId = $this->getUserId();

        /*Get cart items*/
        foreach ($_cartItems as $key => $item) {
            $prdItem = Product::where("id", $item->product_id)->first();
            $prdItem['seller'] = Product::find($prdItem['creator_id'])->user->name;
            $prdItem["quantity_item"] = $item->quantity;
            $prdItem["cart_id"] = $item->id;
            $cartItems = array_merge($cartItems, [$key => $prdItem]);
        }
        return view("carts.index", compact("cartItems"))
            ->with(compact("userId"));
    }

    /*
     * @param Product_id $id
     *
     * Add specified product when user click "Addtocart"
     *  Imediately adding one in cart item
     */
    public function store(Request $request)
    {
        $this->gateDeny('cart.store');

        $cartItem = Cart::where("product_id", $request->productId)
            ->where("user_id", $this->getUserId());

        $msg = $this->createCartItem($cartItem, $request->productId);

        $cartItemsTotal = $this->cart->totalDistinctItems();
        $cartItemsTotal = count($cartItemsTotal);

        $result = [
            0,
            $cartItemsTotal,
            $msg
        ];

        return $result;
    }

    /*
     * Delete specified existing item in cart
     */
    public function destroy($id)
    {
        $this->gateDeny('cart.destroy');

        Cart::where("product_id", $id)
            ->where("user_id", $this->getUserId())
            ->delete();
        return redirect()->route("cart.index");
    }


    /*
     * Delete all existing items in cart
     */
    public function clear(Request $request)
    {
        $this->gateDeny('cart.ajax.clear');
        Cart::where('user_id', $request->id)->delete();
    }


    /*using ajax*/
    /*Add cart items to order_details after click proceed to checkout*/
    public function addOrdDetailsItems(Request $request)
    {
        $cartItems = $request->all();
        $items = array();

        /*Check only seller*/
        foreach ($cartItems['request'] as $item) {
            $qtyProd = Product::where('id', $item['productId'])->firstOrFail()['quantity'];
            $qtyUpd = $qtyProd >= $item["userInputQuantity"] ? $item["userInputQuantity"] : $qtyProd;

            Cart::where("id", $item["cartId"])
                ->where("product_id", $item["productId"])
                ->update(["quantity" => $qtyUpd]);
            array_push($items, $item["cartId"]);
        }

        return $items;
    }


    /*
     * Add cart items into after user click "order" in checkout page
     * */
    public function createOrderDetailsItem(Request $request)
    {

        $cartId = explode("|", $request->cartid);
        if ($cartId[0] === "") {
            array_shift($cartId);
        }


        $validation = $request->validate([
            'name_customer' => 'required|min:3',
            'customer_address' => 'required|min:5',
            'customer_phone' => 'required|regex:/(0)[0-9]{9}/',
            'payment_method' => ""
        ]);

        foreach ($cartId as $item) {
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
    public function ChekoutPage(Request $request)
    {

        $cartItem = Cart::where("user_id", $this->getUserId())
            ->orderBy('updated_at', 'DESC')->first();

        $totalPriceItems = 0;
        $prdItemsArr = array();

        $cartItems = Cart::where("user_id", $this->getUserId())
            ->where("updated_at", $cartItem["updated_at"])
            ->orderBy('creator_id', 'DESC')->get()->toArray();

        $timeCreated = $cartItems[0]['created_at'];

        foreach ($cartItems as $key => $item) {
            $prod = Product::where('id',$item['product_id'])->first()->toArray();
            $seller = Product::find($item['creator_id'])->user->name;
            $prod = array_merge(
                $prod,
                ['seller' => $seller],
                ['cart_qty' => $item['quantity']],
                ['date_update' => date('d-m-Y', strtotime($item["created_at"]))],
                ['hour_update' => date('h:m:s', strtotime($item["created_at"]))],
                ['cart_id' => $cartItems[$key]["id"]]
            );
            $totalPriceItems += $item["quantity"] * $prod["price"];
            array_push($prdItemsArr, $prod);
        }

        $cartItems = $this->cart->totalDistinctItems();

        $param = array(
            [
                'totalPrice' => $totalPriceItems,
                'timeCreated' => $timeCreated,
                'prdItemsArr' => $prdItemsArr,
                'cartItems' => $cartItems
            ]
        );

        return view("orders_details.index")
            ->with(compact("param"));
    }

    /*Using with ajax*/
    public function updateQty(Request $request)
    {

        $cartItem = Cart::where("id", $request->cart_id)->first();
        $productItem = Product::where("id", $cartItem["product_id"])->first()->toArray();

        if ($request->quantity < 0 || $request->quantity === "")
            return [
                "price" => $productItem["price"],
                "qty" => $productItem["quantity"],
                "msg" => "Your quantity is invalid"
            ];

        if ($request->quantity <= $productItem["quantity"]
            && $productItem["quantity"] > 0) {
            return [
                "price" => $productItem["price"],
                "qty" => $productItem["quantity"],
                "inputQty" => $request->quantity,
                "msg" => ""
            ];
        } else {
            return [
                "price" => $productItem["price"],
                "qty" => $productItem["quantity"],
                "inputQty" => $request->quantity,
                "msg" => "Your quantity is out of bound"
            ];
        }

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

//                ddd($quantity);

            if ($quantity < 0 || $item["quantity"] < 0) {
                return redirect()->route("cart.index")
                    ->withErrors("Your input value quantity is invalid");
            }

            $orderDetailsItem = OrderDetails::create([
                "customer_name" => $validation["name_customer"],
                "customer_address" => $validation["customer_address"],
                "customer_phone" => $validation["customer_phone"],
                "payment_method" => $validation["payment_method"],

                "quantity" => $item["quantity"],
                "user_id" => $item["user_id"],
                "product_id" => $item["product_id"],
                "item_price" => $item["price"],
                "creator_id" => $item["creator_id"]
            ]);

            $productItem->update(["quantity" => $productQuantity - $item["quantity"]]);

            Order::create([
                "user_id" => $item["user_id"],
                "order_details_id" => $orderDetailsItem->id,
                "total_price" => $totalPrice,
                "payment_method" => $validation["payment_method"],
                "creator_id" => $orderDetailsItem->creator_id
            ]);
        }
    }

    public function selectedDel(Request $request)
    {
        foreach ($request["array"] as $item) {
            $cartDel = Cart::where("id", $item);
            $cartDel->delete();
        }
    }


    /**
     * @param $cartItems
     * @param $prdItemId
     *
     * @return string
     */
    public function createCartItem($cartItems, $prdItemId)
    {

        $msg = "";

        $cartItemsTotal = count($cartItems->get()->toArray());

        if ($cartItemsTotal > 0) {

            $cartItem = $cartItems->get()->toArray()[0];
            $prodItem = Product::where("id", $prdItemId)->get()->toArray()[0];

            if ($prodItem["quantity"] >= $cartItem["quantity"] + 1) {
                $cartItems->update(["quantity" => $cartItem["quantity"] + 1]);
                $msg = "Product has already been in cart. Plus cart item successfully";

            } else if ($prodItem["quantity"] < $cartItem["quantity"] + 1) {
                $cartItems->update(["quantity" => 0]);
                $msg = "Product has already been in cart. Plus cart item successfully";

            }
        } else {
            $productItems = Product::where("id", $prdItemId);
            $prdItem = $productItems->get()->toArray()[0];

            if ($prdItem["quantity"] > 0) {
                if (count($prdItem) > 0) {
                    $this->addCrtItem($prdItem, $prdItemId);
                    $msg = "Add cart item successfully";
                } else {
                    return redirect()->route("users.products.index");
                }
            } else {
                $this->addCrtItem($prdItem, $prdItemId);
                $msg = "Add cart item successfully.This Product is out of stock";
            }
        }
        return $msg;
    }

    /**
     * Handle add product item to cart in home page
     *
     * @param $prdItem
     * @param $prdItemId
     */
    public function addCrtItem($prdItem, $prdItemId)
    {
        Cart::create([
            'user_id' => $this->getUserId(),
            'product_id' => $prdItemId,
            'quantity' => 1,
            'creator_id' => $prdItem["creator_id"],
            'price' => $prdItem["price"]
        ]);
    }


    /**
     * Handle removing specified item
     *
     * @param Request $request
     * @return mixed
     */
    public function delCartItem(Request $request)
    {

        $isSuccess = Cart::where("id", $request["id"])->delete();

        return $isSuccess;

    }
}
