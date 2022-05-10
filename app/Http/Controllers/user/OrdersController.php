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




    /*
     * Display user order_details list
     */
    public function index()
    {
        abort_if(Gate::denies('users.order.index'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        /*Get items in order_details*/
        $order_details_items = Order_details::
        select("id", "quantity", "user_id", "product_id", "customer_name","item_price",
            "customer_address", "customer_phone",
            DB::raw('DATE(created_at) as date'),
            DB::raw('TIME(created_at) as hour'))
            ->where("user_id", $this->getUserId())
            ->latest()
            ->get()->toArray();

        $arr_time = array();

        foreach ($order_details_items as $item) {

            $time = $item["date"] . "|" . $item["hour"];

            array_push($arr_time, $time);
        }

        $array_order_item = array();

        $total_order_details_item_price = 0;

        foreach ($order_details_items as $key => $item) {

            $arr_order_temp = array();

            $time_explode = explode("|", $arr_time[$key]);
            if ($item["date"] == $time_explode[0] &&
                $item["hour"] == $time_explode[1]){

                $arr_order_temp["order_details_id"] = $item["id"];
                $arr_order_temp["customer_name"] = $item["customer_name"];
                $arr_order_temp["customer_address"] = $item["customer_address"];
                $arr_order_temp["customer_phone"] = $item["customer_phone"];
                $total_order_details_item_price += $item["item_price"];
                $arr_order_temp["total_price"] = $total_order_details_item_price;
                $status = Order::select("status")
                    ->where("order_details_id",$item["id"])->get()->toArray()[0]["status"];
                $arr_order_temp["status"] = $status;
                $arr_order_temp["date"] = $item["date"];
                $arr_order_temp["time"] = $item["hour"];

                if(isset($order_details_items[$key + 1])){
                    if($order_details_items[$key + 1]["date"] != $item["date"] ||
                        $order_details_items[$key + 1]["hour"] != $item["hour"]){
                        array_push($array_order_item, $arr_order_temp);
                    }
                }else{
                    array_push($array_order_item, $arr_order_temp);

                }

            }else if($item["date"] != $time_explode[0] &&
                $item["hour"] != $time_explode[1]){
                continue;
            }
        }

        return view("orders.index")
            ->with(compact("array_order_item"));
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
