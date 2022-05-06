<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Product;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrdersController extends Controller
{
    /*Display all order_details of specified user*/
    public function index($id)
    {
        abort_if(Gate::denies('admin.orders.index'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order = Order::where("user_id", $id)->get()->toArray();

        if(count($order)>0){
            $order = $order[0];
        }

        $details_raw = Order_details::
        select("id", "quantity", "user_id", "product_id",
            DB::raw('DATE(created_at) as time'), "created_at")
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

        $this->array_temp = $arr_temp;

        return view("admin.orders.index")
            ->with(compact("arr_temp"))
            ->with(compact("unique_val_arr"))
            ->with(compact("order"));
    }

    /*Get specified order_details_id and redirect to page order_details show*/
    public function showSpecifiedOrder_Details_item($order_details_item_id)
    {

        abort_if(Gate::denies('admin.orders.showOrderDetailsItem'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $specified_user_order = Order::where("order_details_id",$order_details_item_id)->get()->toArray()[0];

        $specified_user_order_details_item = Order_details::where("id",$specified_user_order["order_details_id"])->get()->toArray()[0];


        $product_item = Product::where("id", $specified_user_order_details_item["product_id"])->get()->toArray()[0];


        $data = array();

        /*Add data*/
        $data["status"] = $specified_user_order["status"];
        $data["user_id"] = $specified_user_order["user_id"];
        $user = User::where("id", $specified_user_order["user_id"])->get()->toArray()[0];
        $data["username"] = $user["username"];
        $data["image"] = $product_item["image"];
        $data["name"] = $product_item["name"];
        $data["details"] = $product_item["details"];
        $data["description"] = $product_item["description"];
        $data["price"] = $product_item["price"];
        $data["quantity"] = $product_item["quantity"];
        $data["order_details_id"] = $specified_user_order_details_item["id"];
        $data["created_at"] = $specified_user_order_details_item["created_at"];
        $data["order_details_item_id"] = $order_details_item_id;

        return view("admin.orders.show")
            ->with(compact("data"));
    }

    /*Updating status of specified order_details*/
    public function update(Request $request)
    {
        abort_if(Gate::denies('admin.orders.update'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        Order::where("order_details_id", $request->order_details_id)
            ->where("user_id", $request->user_id)
            ->update(["status" => $request->status]);


        redirect()->route("admin.orders.index",$request->user_id);
    }


    /*Print order to pdf*/
    public function printPdf($id)
    {
        abort_if(Gate::denies('admin.orders.print'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order = Order::where('id', $id)->first();

        $details_raw = Order_details::
        select("id", "quantity", "user_id", "product_id",
            DB::raw('DATE(created_at) as time'), "created_at")
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
        $dompdf->loadHtml(view('admin.orders.print-order-to-pdf', compact(["order", "arr_temp", "unique_val_arr"])));

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('invoice.pdf');
    }
}
