@extends("layouts.app-master")

@section('title',"Orders")

@section('content')
    @auth
        <div class="body flex-grow-1" style="padding-right: 5rem; margin-top: 40px">
            <div class="container bg-secondary bg-opacity-10 p-4" style="border-radius: 10px;">
                <h4>Date: {{date('d-m-Y', strtotime($dateOrderDetailsItems))}}</h4>
                <h4>Time: {{date('H:m:s', strtotime($dateOrderDetailsItems))}}</h4>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Price</th>
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
                            <td class="align-middle">{{$item["quantity"]}}</td>
                            <td class="align-middle">${{$item["total_price"]}}</td>

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
