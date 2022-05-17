@extends("admin.layouts.app")
@section("title","Orders")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="body flex-grow-1" style="padding-right: 5rem">
            <h1>Orders</h1>
            <h4><a href="{{route("admin.orders.print",$userId)}}">Print Order</a> </h4>
            <div class="container bg-secondary bg-opacity-10 p-4" style="border-radius: 10px;max-width: 1040px">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Time</th>
                        <th scope="col">Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Total Price</th>
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

                    @if(count($orderItemArr) == 0)
                        <tr style="margin: auto;">
                            <td>
                                <h3 class="text-danger">You don't have any order</h3>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            {{--                            <td></td>--}}

                        </tr>
                    @endif

                    @foreach($orderItemArr as $item)

                        <tr style="margin: auto;">

                            <td class="align-middle">{{ $item["date"] }}</td>
                            <td class="align-middle">{{ $item["time"] }}</td>
                            <td class="align-middle">{{$item["customer_name"]}}</td>
                            <td class="align-middle">{{$item["customer_address"]}}</td>
                            <td class="align-middle">{{$item["customer_phone"]}}</td>
                            <td class="align-middle">${{number_format($item["total_price"])}}</td>
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

                            <?php $paramOrderShow = $item["date"] . "|"
                                . $item["time"] . "|" .$userId?>

                            <td class="align-middle">
                                <a href="{{route("admin.orders.displayOrderDetailsItem",
                                $paramOrderShow)}}" class="btn btn-primary">Details</a>
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
    </div>

@endsection
