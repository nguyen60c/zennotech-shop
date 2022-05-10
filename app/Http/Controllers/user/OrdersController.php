<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Product;
use Dompdf\Dompdf;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrdersController extends Controller
{
    private $array_temp = array();

    public function getUserId()
    {
        return auth()->user()->id;
    }



    public function fail()
    {

        /*Get order_details_items array(id, quantity, user_id, product_id, date)*/
        $order_details_items = Order_details::select(
            "id",
            "quantity",
            "user_id",
            "product_id",
            DB::raw('DATE(created_at) as time'),
            "created_at"
        )
            ->where("user_id", $this->getUserId())
            ->latest()
            ->get()->toArray();

        $arr_date = array();
        $arr_time = array();
        $total_price_per_time = 0;
        $var_previous_increase = 0;
        $var_increase = 0;
        $arr_order_details_item = array();
        $arr_order_details = array();

        foreach ($order_details_items as $item) {
            array_push($arr_date, $item["time"]);
            array_push($arr_time, $item["created_at"]);
        }

        /*Remove duplicating and Order indexes in arrays*/
        $arr_date = array_values(array_unique($arr_date));
        $arr_time = array_values(array_unique($arr_time));


        for ($i = 0; $i < count($arr_date); $i++) {
            for ($j = 0; $j < count($order_details_items); $j++) {

                if ($arr_date[$i] == $order_details_items[$j]["time"]) { // Check did order_details buy in same date

                    // Check every order_details_items has the same created_at
                    if ($arr_time[$i] == $order_details_items[$j]["created_at"]) {

                        /*We will get attribute of every order_details to total count once*/
                        $product_item = Product::where("id", $order_details_items[$j]["product_id"])->get()->toArray()[0];
                        $total_price_per_time = $total_price_per_time + ($product_item["price"] * $order_details_items[$j]["quantity"]);


                        if ($arr_time[$i] != $order_details_items[$j + 1]["created_at"]) { //Check if current el not equal with next el

                            $arr_order_details_item["user_id"] = $order_details_items[$i]["user_id"];
                            $arr_order_details_item["product_id"] = $order_details_items[$i]["product_id"];
                            $arr_order_details_item["time"] = $order_details_items[$j]["time"];
                            $arr_order_details_item["created_at"] = $order_details_items[$i]["created_at"];
                            $arr_order_details_item["total_price"] = $total_price_per_time;

                            array_push($arr_order_details, $arr_order_details_item);
                            $var_increase = $var_previous_increase;
                        }
                    } else if ($arr_time[$var_increase + 1] == $order_details_items[$j]["created_at"]) {

                        /*We will get attribute of every order_details to total count once*/
                        $product_item = Product::where("id", $order_details_items[$j]["product_id"])->get()->toArray()[0];
                        $total_price_per_time = $total_price_per_time + ($product_item["price"] * $order_details_items[$j]["quantity"]);
                        $var_increase++;
                        if (isset($order_details_items[$j + 1]["created_at"]) == false) {
                            //Check if current el not equal with next el

                            $arr_order_details_item["user_id"] = $order_details_items[$var_increase]["user_id"];
                            $arr_order_details_item["product_id"] = $order_details_items[$var_increase]["product_id"];
                            $arr_order_details_item["time"] = $order_details_items[$var_increase]["time"];
                            $arr_order_details_item["created_at"] = $order_details_items[$var_increase]["created_at"];
                            $arr_order_details_item["total_price"] = $total_price_per_time;

                            array_push($arr_order_details, $arr_order_details_item);
                            $total_price_per_time = 0;
                        }
                    }

//                    else if($arr_time[$i] != $order_details_items[$j]["created_at"]){ //Case not equal
//
//                        /*We will get attribute of every order_details to total count once*/
//                        $product_item = Product::where("id", $order_details_items[$j]["product_id"])->get()->toArray()[0];
//                        $total_price_per_time = $total_price_per_time + ($product_item["price"] * $order_details_items[$j]["quantity"]);
//
//                        if (empty($order_details_items[$j + 1]["created_at"])||
//                            $arr_time[$i] != $order_details_items[$j + 1]["created_at"]) { //Check if current el not equal with next el
//
//                            $arr_order_details_item["user_id"] = $order_details_items[$i]["user_id"];
//                            $arr_order_details_item["product_id"] = $order_details_items[$i]["product_id"];
//                            $arr_order_details_item["time"] = $order_details_items[$j]["time"];
//                            $arr_order_details_item["created_at"] = $order_details_items[$i]["created_at"];
//                            $arr_order_details_item["total_price"] = $total_price_per_time;
//
//                            array_push($arr_order_details, $arr_order_details_item);
//                            $total_price_per_time = 0;
//
//                        }
//                    }
                }
            }
        }

        ddd($arr_order_details);

        $previous_date_item = "";
        $previous_time_item = "";

        $date_arr = array();
        $time_arr = array();


        return view("orders.index")
            ->with(compact("arr_temp"))
            ->with(compact("unique_val_arr"));
    }

    /*
     * Display user order_details list
     */
    public function index()
    {
        abort_if(Gate::denies('users.order.index'),
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
        $this->array_temp = $arr_temp;

        return view("orders.index")
            ->with(compact("arr_temp"))
            ->with(compact("unique_val_arr"));
    }



    public function printPdf($id)
    {
        abort_if(
            Gate::denies('users.orders.print'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $order = Order::where('id', $id)->first();

        $details_raw = Order_details::select(
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
