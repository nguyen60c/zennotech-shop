@extends("admin.layouts.app")
@section("title","Orders")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="body flex-grow-1" style="padding-right: 5rem">
            <h1>Newly Orders</h1>
            <div class="container p-4" style="border-radius: 10px;">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Time</th>
                        <th scope="col">Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Status</th>
                        <th scope="col">Details</th>

                    </tr>
                    </thead>
                    <tbody>

                    <?php $old_val = "" ?>
                    <?php $limit = 0 ?>
                    <?php $total_price = 0 ?>
                    <?php $old_val_num = 0 ?>
                    <?php $test = []; ?>

                    @foreach($orderDetailsItemsArray as $item)

                        <tr style="margin: auto;">

                            <td class="align-middle">{{date('d-m-Y', strtotime($item["updated_at"]))}}</td>
                            <td class="align-middle">{{date('h:m:s', strtotime($item["updated_at"]))}}</td>
                            <td class="align-middle">{{$item["customer_name"]}}</td>
                            <td class="align-middle">{{$item["customer_address"]}}</td>
                            <td class="align-middle">{{$item["customer_phone"]}}</td>
                            @if($item["status"] === "Processing")
                                <td class="align-middle text-success bolder"
                                    style="font-weight: 700">{{$item["status"]}}</td>
                            @elseif($item["status"] === "Shipping")
                                <td class="align-middle text-warning bolder"
                                    style="font-weight: 700">{{$item["status"]}}</td>
                            @elseif($item["status"] === "Cancel")
                                <td class="align-middle text-danger bolder"
                                    style="font-weight: 700">{{$item["status"]}}</td>
                            @endif
                           <?php

                            $time = date('Y-m-d|H:i:s', strtotime($item["created_at"]));
                            $param = $time."|".$item["user_id"];
                           ?>
                            <td class="align-middle">
                                <a href="{{route("admin.orders.details",
                                $param)}}" class="btn btn-primary">Details</a>
                            </td>

                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>

        @if(session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="card"
                 style="width: 16rem; position: fixed;bottom: 1rem;right: 1rem;padding: 5px; background-color: #d1e7dd">
                <p>{{ session('success') }}</p>
            </div>
    @endif

@endsection


