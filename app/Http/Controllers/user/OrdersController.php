<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrdersController extends Controller
{
    private $array_temp = array();

    public function getUserId()
    {
        return auth()->user()->id;
    }


    /*
     * Display user order_details list
     */
    public function index()
    {
        abort_if(
            Gate::denies('users.order.index'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        /*Get items in order_details*/
        $orderDetailsItems = OrderDetails::select("*")
            ->where("user_id", $this->getUserId())
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

        return view("orders.index")
            ->with(compact("orderItemArr"));
    }

    public function handleOrderDetailsItems($timeArray, $orderDetailsItems)
    {


        $lengthOrderDetails = count($orderDetailsItems);

        $orderDetailsItemTemp = array();

        $orderDetailsArray = array();

        $totalPrice = 0;
        $previousTime = $orderDetailsItems[0]["time"];

        $start = 0;

        foreach ($orderDetailsItems as $key => $item) {
            if ($item["time"] == $previousTime) {

                $start = 1;

                $orderItemTemp["order_details_id"] = $item["id"];
                $orderItemTemp["customer_name"] = $item["customer_name"];
                $orderItemTemp["customer_address"] = $item["customer_address"];
                $orderItemTemp["customer_phone"] = $item["customer_phone"];

                /*To sum price has the same creator_id*/
                $totalPrice += intval($item["item_price"]) * $item["quantity"];

                /*To get status order details item from order*/
                $status = Order::select("status")
                    ->where("order_details_id", $item["id"])
                    ->get()->toArray()[0]["status"];

                $orderItemTemp["status"] = $status;
                $orderItemTemp["time"] = $item["time"];
                $orderItemTemp["date"] = $item["date"];
                $orderDetailsItemTemp = $orderItemTemp;

                if (!isset($orderDetailsItems[$key + 1])) {
                    $orderDetailsItemTemp["total_price"] = $totalPrice;
                    array_push($orderDetailsArray, $orderDetailsItemTemp);
                }
            } else if (
                $item["time"] !== $previousTime) {

                if ($start != 0) {
                    $orderDetailsItemTemp["total_price"] = $totalPrice;
                    array_push($orderDetailsArray, $orderDetailsItemTemp);
                    $start = 0;
                    $totalPrice = 0;
                }

                /*Update time and date*/
                $previousTime = $item["time"];

                $orderItemTemp["order_details_id"] = $item["id"];
                $orderItemTemp["customer_name"] = $item["customer_name"];
                $orderItemTemp["customer_address"] = $item["customer_address"];
                $orderItemTemp["customer_phone"] = $item["customer_phone"];

                /*To sum price has the same creator_id*/
                $totalPrice += intval($item["item_price"]) * $item["quantity"];

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
                        $orderItemTemp["time"] != $orderDetailsItems[$key + 1]["time"]
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

    public function show($time)
    {

        $time = str_replace("|", " ", $time);

        $orderItems = Order::where("user_id", $this->getUserId())
            ->where("created_at", $time)->get()->toArray();

        $totalPrice = 0;

        $creator = "";

        $orderDetailsItemsArr = array();

        $dateOrderDetailsItems = $orderItems[0]["created_at"];

        foreach ($orderItems as $key => $item) {
            $orderDetailsItems = OrderDetails::where("id", $item["order_details_id"])
                ->get()->toArray()[0];
            $productItem = Product::where("id", $orderDetailsItems["product_id"])
                ->get()->toArray()[0];
            $creator = User::where("id", $productItem["creator_id"])->get("username")->toArray()[0]["username"];
            $orderDetailsItemsArr[$key]["name"] = $productItem["name"];
            $orderDetailsItemsArr[$key]["image"] = $productItem["image"];
            $orderDetailsItemsArr[$key]["name"] = $productItem["name"];
            $orderDetailsItemsArr[$key]["quantity"] = $orderDetailsItems["quantity"];
            $orderDetailsItemsArr[$key]["total_price"] = intval($orderDetailsItems["item_price"]);
        }



        return view("orders.show")
            ->with(compact("orderDetailsItemsArr"))
            ->with(compact("dateOrderDetailsItems"))
            ->with(compact("creator"));
    }

    public function printPdf($id)
    {
        abort_if(
            Gate::denies('users.orders.print'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $order = Order::where('id', $id)->first();

        $details_raw = OrderDetails::select(
            "id",
            "quantity",
            "user_id",
            "product_id",
            DB::raw('DATE(created_at) as time'),
            "created_at"
        )
            ->where("user_id", $id)
            ->latest()
            ->get()->toArray();

        $data = $details_raw;

        $arr_temp = [];

        $arr_time = [];
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

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('pdf.pdf', compact(["order", "arr_temp", "unique_val_arr"])));

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('invoice.pdf');
    }
}
