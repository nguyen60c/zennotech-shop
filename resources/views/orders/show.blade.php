@extends("layouts.app-master")

@section('title',"Orders")

@section('content')
    @auth
        <div class="body flex-grow-1" style="padding-right: 5rem; margin-top: 40px">
            <div class="container bg-secondary bg-opacity-10 p-4" style="border-radius: 10px;">
                <h4>Date: {{date('d-m-Y', strtotime($dateOrderDetailsItems))}}</h4>
                <h4>Time: {{date('H:m:s', strtotime($dateOrderDetailsItems))}}</h4>
                <h4>By: {{$creator}}</h4>
                <h4>Customer: {{$customerName}}</h4>
                <h4>Address: {{$customerAddress}}</h4>
                <h4>Phone: {{$customerPhone}}</h4>
                <h4>Payment method: {{$paymentMethod}}</h4>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Image</th>
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
                            <td class="align-middle">
                                <img src="{{ asset('images/products/' . $item['image']) }}" class="img-thumbnail"
                                     width="100"
                                     height="100"></td>
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
                <a href="{{route("users.order.index")}}" class="btn btn-secondary">Back to order</a>
            </div>
        </div>

        @if(session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="card"
                 style="width: 16rem; position: fixed;bottom: 1rem;right: 1rem;padding: 5px; background-color: #d1e7dd">
                <p>{{ session('success') }}</p>
            </div>
        @endif
    @endauth
@endsection

@section("style")

@endsection
