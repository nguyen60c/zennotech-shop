@extends("admin.layouts.app")
@section("title","Orders")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="body flex-grow-1" style="margin-top: 10px">
            <h1>Orders</h1>
            <div class="container bg-secondary bg-opacity-10 p-2" style="border-radius: 10px;">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Time Created</th>
                        <th scope="col">Products</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Total</th>
                        <th scope="col"><span style="display: inline-block;width: 105px">Status</span>
                            Action
                        </th>

                    </tr>
                    </thead>
                    <tbody>

                    <?php $old_val = "" ?>
                    <?php $limit = 0 ?>
                    <?php $total_price = 0 ?>
                    <?php $old_val_num = 0 ?>
                    <?php $test = []; ?>

                    @if(count($arr_temp) > 0)
                        @foreach($arr_temp as $key => $order)

                            <?php $next = next($arr_temp);?>
                            @if($order["position"] == array_search($order["time"],$unique_val_arr)
                                && $old_val == $order["position"])
                                <tr style="margin: auto;">

                                    <td class="align-middle">{{ $order["time"] }}</td>
                                    <td class="align-middle">{{ date('h:i:s A', strtotime($order["created_at"])) }}</td>
                                    <td class="align-middle">{{$order["product_name"]}}</td>
                                    <td class="align-middle">{{$order["quantity"]}}</td>
                                    <td class="align-middle">${{number_format($order["total"])}}</td>

                                    <td class="align-middle text-danger bolder">
                                        <form method="post"
                                              action="{{route("admin.orders.showOrderDetailsItem",$order["user_id"])}}">
                                            @csrf

                                            <input type="text" name="status" value="{{$order["status"]}}" readonly
                                                   class="transparent-input"
                                                   style="width: 100px">
                                            <input type="hidden" name="product_name" class="input_status"
                                                   value="{{$order["product_name"]}}"/>
                                            <input type="hidden" name="status" class="input_status"
                                                   value="{{$order["status"]}}"/>
                                            <input type="hidden" name="user_id" class="input_user_id"
                                                   value="{{$order["user_id"]}}"/>
                                            <input type="hidden" name="order_details_id"
                                                   class="input_order_details_id" value="{{$order["id"]}}"/>

                                            <a type="submit" class="btn btn-primary"
                                               href="{{route("admin.orders.showOrderDetailsItem",$order["id"])}}">
                                                Details
                                            </a>
                                        </form>

                                    </td>

                                </tr>
                                <?php
                                if ($order["status"] == "Cancel") {
                                    $total_price = $total_price + 0;
                                } else {
                                    $total_price = $total_price + $order["total"];
                                }
                                ?>
                                <?php
                                $next_val = $next["time"] ?? "next_val";
                                $cur_val = $order["time"] ?? "cur_val";
                                ?>
                                @if($next_val != $cur_val)
                                    <tr style="margin: auto;border-style: none">
                                        <td class="align-middle" style="border:transparent !important;">
                                            <strong>Total: {{$total_price}} </strong>
                                            <?php $total_price = 0 ?>
                                        </td>
                                    </tr>
                                @endif

                            @elseif($order["position"] ==
                                array_search($order["time"],$unique_val_arr)
                                && $old_val != $order["position"])
                                @if($limit > 0)
                                    <tr style="margin: auto;">
                                        <td class="align-middle" style="border-bottom-width:0">
                                            <br></td>
                                    </tr>
                                @endif

                                <?php ++$limit ?>

                                <tr style="margin: auto;">
                                    <td class="align-middle">{{ $order["time"] }}</td>
                                    <td class="align-middle">{{ date('h:i:s A', strtotime($order["created_at"])) }}</td>
                                    <td class="align-middle">{{$order["product_name"]}}</td>
                                    <td class="align-middle">{{$order["quantity"]}}</td>
                                    <td class="align-middle">${{number_format($order["total"])}}</td>

                                    <td class="align-middle text-danger bolder">
                                        <form>
                                            <input type="text" name="status" value="{{$order["status"]}}" readonly
                                                   class="transparent-input"
                                                   style="width: 100px">
                                            <input type="hidden" name="product_name" class="input_status"
                                                   value="{{$order["product_name"]}}"/>
                                            <input type="hidden" name="status" class="input_status"
                                                   value="{{$order["status"]}}"/>
                                            <input type="hidden" name="user_id" class="input_user_id"
                                                   value="{{$order["user_id"]}}"/>
                                            <input type="hidden" name="order_details_id"
                                                   class="input_order_details_id" value="{{$order["id"]}}"/>
                                            <a type="submit" class="btn btn-primary"
                                               href="{{route("admin.orders.showOrderDetailsItem",$order["id"])}}">
                                                Details
                                            </a>
                                        </form>
                                    </td>

                                    <?php $old_val = $order["position"] ?>
                                </tr>
                            @endif
                        @endforeach

                        <tr>
                            <td class="align-middle text-danger bolder"
                                style="border-bottom-width:0">

                                <a href="{{route("admin.orders.print",$order["id"])}}"
                                   class="btn btn-primary form-control">Print</a>

                            </td>


                        </tr>
                    @else

                        <tr>

                            <td>

                                <span class="text-danger">There is no order details in this user</span>
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if(session()->has('success'))
            <div x-data="{ show: true }" x-show="show"
                 x-init="setTimeout(() => show = false, 3000)" class="card"
                 style="width: 16rem; position: fixed;bottom: 1rem;right: 1rem;padding: 5px; background-color: #d1e7dd">
                <p>{{ session('success') }}</p>
            </div>
        @endif
    </div>

@endsection
