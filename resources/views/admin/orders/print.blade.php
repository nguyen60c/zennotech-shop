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
    <div class="body flex-grow-1">
        <h1>Orders</h1>

        <div class="container bg-secondary bg-opacity-10 p-4" style="border-radius: 10px;">

            <div style="display: flex;justify-content: flex-start;">
                <h4 style="margin-right: 20px">Date: {{date('d-m-Y', strtotime($dateOrderDetailsItems))}}</h4>
                <h4 style="margin-right: 20px">Time: {{date('H:m:s', strtotime($dateOrderDetailsItems))}}</h4>
            </div>
            <div>
                {{$orderDetailsItemsStatus}}
            </div>
            <div>
                <h4>Customer: {{$customerName}}</h4>
                <h4>Address: {{$customerAddress}}</h4>
                <h4>Phone: {{$customerPhone}}</h4>
            </div>

            <table class="table" style="margin-top: 40px">
                <thead>
                <tr>
                    <th scope="col">Product</th>
                    <th scope="col">Price</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Total</th>
                    <th scope="col">Actions</th>
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
                        <td class="align-middle">{{$item["price"]}}</td>
                        <td class="align-middle">{{$item["quantity"]}}</td>
                        <td class="align-middle">${{$item["total_price"]}}</td>
                        <td class="align-middle">
                            <a class="btn btn-warning"
                               href="{{route("users.products.details",$item["product_id"])}}">
                                More infor
                            </a></td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
