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
        <div class="body flex-grow-1">
            <h1>Orders</h1>

            <div class="container bg-secondary bg-opacity-10 p-4" style="border-radius: 10px;">

                <div style="display: flex;justify-content: flex-start;">
                    <h4 style="margin-right: 20px">Date: {{date('d-m-Y', strtotime($dateOrderDetailsItems))}}</h4>
                    <h4 style="margin-right: 20px">Time: {{date('H:m:s', strtotime($dateOrderDetailsItems))}}</h4>

                    <div>

                        <input class="input_status border-0 transparent-input"
                               type="hidden" readonly
                               value="">

                        <?php
                        $color = "";
                        switch ($orderDetailsItemsStatus) {
                            case "Processing":
                                $color = "bg-primary";
                                break;
                            case "Shipping":
                                $color = "bg-warning";
                                break;
                            case "Cancel":
                                $color = "bg-danger";
                                break;
                        }
                        ?>
                        <select name="status"
                                class="form-control text-light select_status {{$color}}"
                                style="font-weight: 700; width: 150px !important;">

                            <option {{$orderDetailsItemsStatus == "Processing" ? "selected" : ""}}
                                    value="Processing" style="background: #a0aec0">
                                Processing
                            </option>

                            <option {{$orderDetailsItemsStatus == "Shipping" ? "selected" : ""}}
                                    value="Shipping">
                                Shipping
                            </option>

                            <option {{$orderDetailsItemsStatus == "Cancel" ? "selected" : ""}}
                                    value="Cancel">
                                Cancel
                            </option>

                        </select>

                    </div>
                </div>
                <div>
                    <h4>Customer: {{$customerName}}</h4>
                    <h4>Address: {{$customerAddress}}</h4>
                    <h4>Phone: {{$customerPhone}}</h4>
                </div>

                <table class="table" style="margin-top: 40px">
                    <thead>
                    <tr>
                        <th scope="col">Image</th>
                        <th scope="col">Product</th>
                        <th scope="col">Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Total</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php $old_val = "" ?>
                    <?php $limit = 0 ?>
                    <?php $total_price = 0 ?>
                    <?php $old_val_num = 0 ?>
                    <?php $test = []; ?>


                    @foreach( $orderDetailsItemsArr as $item)

                        <tr style="margin: auto;">

                            <td class="align-middle">{{$item["name"]}}</td>

                            <td>
                                <img src="{{ asset('images/products/' . $item['image']) }}" class="img-thumbnail"
                                     width="100"
                                     height="100">
                            </td>
                            <td class="align-middle">{{$item["price"]}}</td>
                            <td class="align-middle">{{$item["quantity"]}}</td>
                            <td class="align-middle">${{$item["total_price"]}}</td>

                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
            <form method="post" action="{{route("admin.orders.update")}}">
                @csrf
                <input type="hidden" name="status" class="input_status"
                       value="{{$orderDetailsItemsStatus}}"/>
                <input type="hidden" name="user_id" class="input_user_id"
                       value="{{$userId}}"/>
                <input type="hidden" name="time" class="input_user_time"
                       value="{{$time}}"/>
                <button class="btn btn-primary" type="submit"><strong>Save</strong></button>
                <a href="{{ route('admin.orders.history') }}" class="btn btn-default">Back</a>

            </form>
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
