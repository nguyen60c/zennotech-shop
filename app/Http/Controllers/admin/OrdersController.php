<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\User;
//use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrdersController extends Controller
{
    /*Display all order_details of specified user in user list*/
    public function index($id)
    {

        abort_if(
            Gate::denies('admin.orders.index'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        /*Get items in order_details*/
        $orderDetailsItems = OrderDetails::select("*")
            ->where("user_id", $id)
            ->orderBy("updated_at", "DESC")
            ->get()->toArray();

        $timeArray = array();

        /*Check order details items arrray is not empty*/
        if (count($orderDetailsItems) > 0) {

            /*To seprate two date and time into a string*/
            foreach ($orderDetailsItems as $key => $item) {
                $time = date('Y-m-d|H:i:s', strtotime($item["created_at"]));

                $time = explode("|", $time);

                $orderDetailsItems[$key]["date"] = $time[0];
                $orderDetailsItems[$key]["time"] = $time[1];

                array_push($timeArray, $time);
            }


            $orderItemArr = $this->handleOrderDetailsItems($timeArray, $orderDetailsItems);
        } else {
            /*When array is empty*/
            $orderItemArr = array();
        }

        return view("admin.orders.index")
            ->with(compact("orderItemArr"))
            ->with(["userId" => $id]);

    }

    public function displayOrderDetailsItem($param)
    {

        $param = explode("|", $param);

        $time = $param[0] . " " . $param[1];

        $userId = $param[2];


        $orderItems = Order::where("user_id", $param[2])
            ->where("created_at", $time)->get()->toArray();

        $totalPrice = 0;

        $orderDetailsItemsArr = array();

        $orderDetailsItemsStatus = "";

        $orderDetailsId = 0;

        $dateOrderDetailsItems = $orderItems[0]["created_at"];

        foreach ($orderItems as $key => $item) {
            $orderDetailsItems = OrderDetails::where("id", $item["order_details_id"])
                ->get()->toArray()[0];
            $productItem = Product::where("id", $orderDetailsItems["product_id"])
                ->get()->toArray()[0];
            $orderDetailsItemsArr[$key]["name"] = $productItem["name"];
            $orderDetailsItemsArr[$key]["id"] = $orderDetailsItems["id"];
            $orderDetailsItemsArr[$key]["quantity"] = $orderDetailsItems["quantity"];
            $totalPrice = $totalPrice + intval($orderDetailsItems["item_price"]);
            $orderDetailsItemsArr[$key]["total_price"] = $totalPrice;
            $orderDetailsItemsStatus = $orderItems[$key]["status"];
        }

        return view("admin.orders.show")
            ->with(compact("orderDetailsItemsArr"))
            ->with(compact("dateOrderDetailsItems"))
            ->with(compact("orderDetailsItemsStatus"))
            ->with(compact("userId"))
            ->with(compact("time"));
    }

    /*Updating status of specified order_details*/
    public function update(Request $request)
    {
        abort_if(Gate::denies('admin.orders.update'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        Order::where("user_id", $request->user_id)
            ->where("created_at", $request->time)
            ->update(["status" => $request->status]);

        return redirect()->route("admin.orders.history");

    }

    public function updateOrderDetailsHistory(Request $request){
        abort_if(Gate::denies('admin.orders.updateOrderDetailsHistory'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        Order::where("user_id", $request->user_id)
            ->where("created_at", $request->time)
            ->update(["status" => $request->status]);

        return redirect()->route("admin.orders.index",$request->user_id);
    }


    /*Print order to pdf*/
    public function printPdf($id)
    {
        abort_if(Gate::denies('admin.orders.print'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');


        /*Get items in order_details*/
        $orderDetailsItems = OrderDetails::select("*")
            ->where("user_id", $id)
            ->orderBy("updated_at", "DESC")
            ->get()->toArray();

        $user = User::where("id",$id)->get()->toArray()[0];

        $currentTime = Carbon::now()->toDateTimeString();
        $currentTime = explode(" ", $currentTime);
        $currentTime[0] = str_replace("-","", $currentTime[0]);
        $currentTime[1] = str_replace(":","", $currentTime[1]);
        $nameFile = $currentTime[0] . "-" . $currentTime[1] . "-" . $user["name"];

        $timeArray = array();

        /*Check order details items arrray is not empty*/
        if (count($orderDetailsItems) > 0) {

            /*To seprate two date and time into a string*/
            foreach ($orderDetailsItems as $key => $item) {
                $time = date('Y-m-d|H:i:s', strtotime($item["created_at"]));

                $time = explode("|", $time);

                $orderDetailsItems[$key]["date"] = $time[0];
                $orderDetailsItems[$key]["time"] = $time[1];

                array_push($timeArray, $time);
            }


            $orderItemArr = $this->handleOrderDetailsItems($timeArray, $orderDetailsItems);
        } else {
            /*When array is empty*/
            $orderItemArr = array();
        }

        $pdf = PDF::loadView('admin.orders.print',  compact('orderItemArr'));
        return $pdf->download("$nameFile.pdf");
    }

    public function ordersHistory()
    {

        $orderItems = Order::where("creator_id", auth()->user()->id)
            ->orderBy("updated_at", "DESC")
            ->get()->toArray();

//        ddd(auth()->user()->id);

        $timeArray = array();

        $orderDetailsItemsArray = array();

//        $timeCheck =

        foreach ($orderItems as $key => $item) {
            $orderDetailsItems = OrderDetails::where("id", $item["order_details_id"])
                ->get()->toArray();
            $orderDetailsItems[0]["status"] = $item["status"];
            $orderDetailsItems[0]["userId"] = $item["user_id"];
            if(isset($orderItems[$key + 1])){
                if($orderItems[$key + 1]["created_at"] == $item["created_at"]){
                    continue;
                }
            }
            $orderDetailsItemsArray = array_merge($orderDetailsItemsArray, $orderDetailsItems);
        }


        return view("admin.orders.historyOrders")
            ->with(compact("orderDetailsItemsArray"));
    }

    public function showDetailsOrderItemsHistory($param)
    {
        $param = explode("|", $param);

        $time = $param[0] . " " . $param[1];

        $userId = $param[2];

        $orderItems = Order::where("user_id", $userId)
            ->where("created_at", $time)->get()->toArray();

        $totalPrice = 0;

        $orderDetailsItemsArr = array();

        $orderDetailsItemsStatus = "";

        $orderDetailsId = 0;

        $dateOrderDetailsItems = $orderItems[0]["created_at"];

        foreach ($orderItems as $key => $item) {
            $orderDetailsItems = OrderDetails::where("id", $item["order_details_id"])
                ->get()->toArray()[0];
            $productItem = Product::where("id", $orderDetailsItems["product_id"])
                ->get()->toArray()[0];
            $orderDetailsItemsArr[$key]["name"] = $productItem["name"];
            $orderDetailsItemsArr[$key]["id"] = $orderDetailsItems["id"];
            $orderDetailsItemsArr[$key]["quantity"] = $orderDetailsItems["quantity"];
            $totalPrice = $totalPrice + intval($orderDetailsItems["item_price"]);
            $orderDetailsItemsArr[$key]["total_price"] = $totalPrice;
            $orderDetailsItemsStatus = $orderItems[$key]["status"];
        }

        return view("admin.orders.showDetailsOrderHistory")
            ->with(compact("orderDetailsItemsArr"))
            ->with(compact("dateOrderDetailsItems"))
            ->with(compact("orderDetailsItemsStatus"))
            ->with(compact("userId"))
            ->with(compact("time"));
    }

    public function handleOrderDetailsItems($timeArray, $orderDetailsItems)
    {

        $lengthOrderDetails = count($orderDetailsItems);

        $orderDetailsItemTemp = array();

        $orderDetailsArray = array();

        $creatorId = $orderDetailsItems[0]["creator_id"];

        $totalPrice = 0;
        $previousTime = $orderDetailsItems[0]["time"];
        $previousDate = $orderDetailsItems[0]["date"];

        $start = 0;

        foreach ($orderDetailsItems as $key => $item) {
            if ($item["time"] == $previousTime && $previousDate == $item["date"]) {

                $start = 1;

                $orderItemTemp["order_details_id"] = $item["id"];
                $orderItemTemp["customer_name"] = $item["customer_name"];
                $orderItemTemp["customer_address"] = $item["customer_address"];
                $orderItemTemp["customer_phone"] = $item["customer_phone"];

                /*To sum price has the same creator_id*/
                $totalPrice += intval($item["item_price"]);

                /*To get status order details item from order*/
                $status = Order::select("status")
                    ->where("order_details_id", $item["id"])
                    ->get()->toArray()[0]["status"];

                $orderItemTemp["status"] = $status;
                $orderItemTemp["date"] = $item["date"];
                $orderItemTemp["time"] = $item["time"];
                $orderDetailsItemTemp = $orderItemTemp;

                if (!isset($orderDetailsItems[$key + 1])) {
                    $orderDetailsItemTemp["total_price"] = $totalPrice;
                    array_push($orderDetailsArray, $orderDetailsItemTemp);
                }
            } else if (
                $item["time"] !== $previousTime
                && $previousDate == $item["date"]
            ) {

                if ($start != 0) {
                    $orderDetailsItemTemp["total_price"] = $totalPrice;
                    array_push($orderDetailsArray, $orderDetailsItemTemp);
                    $start = 0;
                    $totalPrice = 0;
                }

                /*Update time and date*/
                $previousTime = $item["time"];
                $previousDate = $item["date"];

                $orderItemTemp["order_details_id"] = $item["id"];
                $orderItemTemp["customer_name"] = $item["customer_name"];
                $orderItemTemp["customer_address"] = $item["customer_address"];
                $orderItemTemp["customer_phone"] = $item["customer_phone"];

                /*To sum price has the same creator_id*/
                $totalPrice += intval($item["item_price"]);

                /*To get status order details item from order*/
                $status = Order::select("status")
                    ->where("order_details_id", $item["id"])
                    ->get()->toArray()[0]["status"];

                $orderItemTemp["status"] = $status;
                $orderItemTemp["date"] = $item["date"];
                $orderItemTemp["time"] = $item["time"];
                $orderDetailsItemTemp = $orderItemTemp;

                if (isset($orderDetailsItems[$key + 1])) {
                    if (
                        $orderItemTemp["date"] == $orderDetailsItems[$key + 1]["date"]
                        && $orderItemTemp["time"] != $orderDetailsItems[$key + 1]["time"]
                    ) {
                        $orderDetailsItemTemp["total_price"] = $totalPrice;
                        array_push($orderDetailsArray, $orderDetailsItemTemp);
                        $start = 0;
                        $totalPrice = 0;
                    }
                } else {
                    $orderDetailsItemTemp["total_price"] = $totalPrice;
                    array_push($orderDetailsArray, $orderDetailsItemTemp);
                    $start = 0;
                    $totalPrice = 0;
                }
            }
        }
        return ($orderDetailsArray);
    }
}
