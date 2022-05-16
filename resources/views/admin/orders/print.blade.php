<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div class="main">
    <nav class="navbar navbar-expand navbar-light navbar-bg">
        <a class="sidebar-toggle js-sidebar-toggle">
            <i class="hamburger align-self-center"></i>
        </a>
    </nav>
    <div class="body flex-grow-1" style="padding-right: 5rem">
        <h1>Orders</h1>
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

                    </tr>
                @endforeach

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
</body>
</html>
