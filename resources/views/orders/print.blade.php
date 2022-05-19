<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table, th, td {
            padding: 10px;
        }
    </style>
</head>
<body>
<div class="body flex-grow-1" style="padding-right: 5rem; margin-top: 40px">
    <div class="container bg-secondary bg-opacity-10 p-4" style="border-radius: 10px;">
        <div style="margin-bottom: 30px">
            <span style="margin-right: 20px">Date: {{date('d-m-Y', strtotime($dateOrderDetailsItems))}}</span>
            <span>Time: {{date('H:m:s', strtotime($dateOrderDetailsItems))}}</span>
        </div>
        <div style="display: inline-block;width: 300px">
            <h4>By: {{$creator}}</h4>
            <h4>Customer: {{$customerName}}</h4>
            <h4>Address: {{$customerAddress}}</h4>
            <h4>Phone: {{$customerPhone}}</h4>
        </div>

        <span>Payment method: {{$paymentMethod}}</span>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Product</th>
                <th scope="col">Quantity</th>
                <th scope="col">Price</th>
                <th scope="col">Action</th>
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

                    <td class="align-middle"><h5>{{$item["name"]}}</h5></td>
                    <td class="align-middle"><h5>{{$item["quantity"]}}</h5></td>
                    <td class="align-middle"><h5>${{$item["total_price"]}}</h5></td>
                    <td class="align-middle"><a class="btn btn-warning"
                                                href="{{route("users.order.details",$item["product_id"])}}">
                            More infor
                        </a></td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
</div>
</body>
</html>
