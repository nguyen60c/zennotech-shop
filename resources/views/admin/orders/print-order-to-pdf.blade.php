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
<div class="body flex-grow-1">
    <h1>Your Orders</h1>
    <div class="container bg-secondary bg-opacity-10 p-4" style="border-radius: 10px;">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Time Created</th>
                <th scope="col">Products</th>
                <th scope="col">Quantity</th>
                <th scope="col">Total</th>
                <th scope="col">Status</th>

            </tr>
            </thead>
            <tbody>

            <?php $old_val = "" ?>
            <?php $limit = 0 ?>
            <?php $total_price = 0 ?>
            <?php $old_val_num = 0 ?>
            <?php $test = []; ?>
            @foreach($arr_temp as $key => $order)

                <?php $next = next($arr_temp);?>
                @if($order["position"] == array_search($order["time"],$unique_val_arr)
                    && $old_val == $order["position"])
                    <tr style="margin: auto;">

                        <td class="align-middle">{{ $order["time"] }}</td>
                        <td class="align-middle">{{ date('h:i:s A', strtotime($order["created_at"])) }}</td>
                        <td class="align-middle">{{$order["product_name"]}}</td>
                        <td class="align-middle text-center">
                            {{$order["quantity"]}}</td>
                        <td class="align-middle">${{number_format($order["total"])}}</td>
                        @if($order["status"] === "Processing")
                            <td class="align-middle text-success bolder"
                                style="font-weight: 700">{{$order["status"]}}</td>
                        @elseif($order["status"] === "Shipping")
                            <td class="align-middle text-warning bolder"
                                style="font-weight: 700">{{$order["status"]}}</td>
                        @elseif($order["status"] === "Cancel")
                            <td class="align-middle text-danger bolder"
                                style="font-weight: 700">{{$order["status"]}}</td>
                        @endif
                    </tr>
                    <?php $total_price = $total_price + $order["total"] ?>
                    <?php
                    $next_val = $next["time"] ?? "next_val";
                    $cur_val = $order["time"] ?? "cur_val";
                    ?>
                    @if($next_val != $cur_val)
                        <tr style="margin: auto;">
                            <td class="align-middle">
                                <strong>Total: ${{$total_price}} </strong>
                                <?php $total_price = 0 ?>
                            </td>
                        </tr>
                    @endif

                @elseif($order["position"] == array_search($order["time"],$unique_val_arr)
                    && $old_val != $order["position"])
                    @if($limit > 0)
                        <tr style="margin: auto;">
                            <td class="align-middle">
                                <br><br></td>
                        </tr>
                    @endif

                    <?php ++$limit ?>

                    <tr style="margin: auto;">
                        <td class="align-middle">{{ $order["time"] }}</td>
                        <td class="align-middle">{{ date('h:i:s A', strtotime($order["created_at"])) }}</td>
                        <td class="align-middle">{{$order["product_name"]}}</td>
                        <td class="align-middle text-center">{{$order["quantity"]}}</td>
                        <td class="align-middle">${{number_format($order["total"])}}</td>
                        @if($order["status"] === "Processing")
                            <td class="align-middle text-success bolder"
                                style="font-weight: 700">{{$order["status"]}}</td>
                        @elseif($order["status"] === "Shipping")
                            <td class="align-middle text-warning bolder"
                                style="font-weight: 700">{{$order["status"]}}</td>
                        @elseif($order["status"] === "Cancel")
                            <td class="align-middle text-danger bolder"
                                style="font-weight: 700">{{$order["status"]}}</td>
                        @endif
                        <?php $old_val = $order["position"] ?>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
