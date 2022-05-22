<?php

namespace App\Http\Controllers\user;

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
    private $data = array();


    public function __construct()
    {
        $this->cart = new Cart();
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
        abort_if(
            Gate::denies('cart.index'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $cartItems = Cart::where("user_id", $this->getUserId());
        $cartItems = $cartItems->paginate(10);
        $listCartItems = array();
        $userId = auth()->user()->id;

        /*Get cart items*/
        foreach ($cartItems as $item) {
            $productItems = Product::where("id", $item->product_id)->get()->toArray();

            $productItems[0]["quantity_item"] = $item->quantity;
            $productItems[0]["cart_id"] = $item->id;
            $listCartItems = array_merge($listCartItems, $productItems);
        }

        return view("carts.index", compact("listCartItems"))
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
        abort_if(
            Gate::denies('cart.store'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

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
    public function clear(Request $request)
    {
        abort_if(
            Gate::denies('cart.clear'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        Cart::where("user_id", $request->id)->delete();
        return redirect()->route("cart.index");
    }

    /*using ajax*/
    /*Add cart items to order_details after click proceed to checkout*/
    public function addCartItemsToOrderDetails(Request $request)
    {

        $cartItems = $request->all();

        $flat = array();

        foreach ($cartItems as $itemArr) {

            foreach ($itemArr as $key => $item) {

                if (isset($itemArr[$key + 1])) {
                    if ($item["creatorId"] != $itemArr[$key + 1]["creatorId"]) {
                        array_push($flat, false);
                    }
                }
            }
        }

        /*Check only seller*/
        foreach ($cartItems as $itemArr) {

            foreach ($itemArr as $key => $item) {

                $qtyProd = Product::where("id", $item["productId"])->get("quantity")[0]["quantity"];
                $qtyUpd = $item["userInputQuantity"] <= $qtyProd ? $item["userInputQuantity"] : $qtyProd;

                Cart::where("id", $item["cartId"])
                    ->where("product_id", $item["productId"])
                    ->update(["quantity" => $qtyUpd]);
                array_push($flat, true, $item["cartId"]);
            }
        }
        return $flat;
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


        $timeCreated = 0;
        $totalPriceItems = 0;
        $prdItemsArr = array();

        $cartItems = Cart::where("user_id", $this->getUserId())
            ->where("updated_at", $cartItem["updated_at"])->get()->toArray();

        foreach ($cartItems as $key => $item) {
            $product = Product::where("id", $item["product_id"])->get()->toArray()[0];
            $product["cart_qty"] = $item["quantity"];
            $product["date_update"] = date('d-m-Y', strtotime($item["created_at"]));
            $product["hour_update"] = date('h:m:s', strtotime($item["created_at"]));
            $product["cart_id"] = $cartItems[$key]["id"];
            $timeCreated = $item["created_at"];
            $totalPriceItems += $item["quantity"] * $product["price"];
            array_push($prdItemsArr, (array)$product);
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

        $cartItem = Cart::where("id", $request->cart_id)->get()->toArray()[0];
        $productItem = Product::where("id", $cartItem["product_id"])->first()->toArray();

        if ($request->quantity < 0 || $request->quantity === "") {
            return [
                "price" => $productItem["price"],
                "qty" => $productItem["quantity"],
                "msg" => "Your quantity is invalid"
            ];
        }

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


    /*Using with ajax*/
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
            $cartDel = Cart::where("id",$item);
            $cartDel->delete();
        }
    }


    /*
     * @param Array $is_exist_cart_item
     * @param Cart $cart_items_db
     * @param int $id
     *
     * @return Illuminate\Routing\Redirector
     */
    public
    function createCartItem($cartItems, $prdItemId)
    {

        $msg = "";

        $cartItemsTotal = count($cartItems->get()->toArray());

        if ($cartItemsTotal > 0) {

            $cartItem = $cartItems->get()->toArray()[0];
            $prodItem = Product::where("id", $prdItemId)->get()->toArray()[0];

            if ($prodItem["quantity"] >= $cartItem["quantity"] + 1) {
                $cartItems->update(["quantity" =>
                    $cartItem["quantity"] + 1]);
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

    public function addCrtItem($prdItem, $prdItemId){
        Cart::create([
            'user_id' => $this->getUserId(),
            'product_id' => $prdItemId,
            'quantity' => 1,
            'creator_id' => $prdItem["creator_id"],
            'price' => $prdItem["price"]
        ]);
    }

    public function checkQuantity(Request $request)
    {

        $input = $request->all();

        $updatingProduct = array();

        foreach ($input as $item) {
            $productItem = Product::where("name", $item[1])->get()->toArray()[0];

            if ($productItem["quantity"] >= $item[0]
                && $item[0] > 0
                && $item[0] !== "") {
                array_push($updatingProduct, 0, $productItem["quantity"]);
                return $updatingProduct;

            } else if ($productItem["quantity"] < $item[0]
                || $item[0] <= 0) {
                array_push($updatingProduct, 1, $productItem["quantity"]);
                return $updatingProduct;
            }
        }

        return $input;
    }

    public function delCartItem(Request $request)
    {

        $isSuccess = Cart::where("id", $request["id"])->delete();

        return $isSuccess;

    }
}
