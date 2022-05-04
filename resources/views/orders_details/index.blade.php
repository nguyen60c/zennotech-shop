@extends("layouts.app-master");

@section('title',"Checkout")
@section('content')

    @auth
        @role("user")
        <div class="body flex-grow-1" style="margin-left: 300px; padding-right: 5rem">
            <h1>Checkout</h1>
            <div class="container bg-secondary bg-opacity-10 p-4" style="border-radius: 10px;">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Date Created</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $order)
                        <tr style="margin: auto;">
                            <td class="align-middle">{{ date('d/m/20y',
strtotime($order["created_at"])) }}</td>
                            <td class="align-middle">{{$order["quantity_temp"]}}</td>
                            <td class="align-middle">${{ number_format($order["price"]) }}</td>
                            <td class="align-middle">
                                <div class="d-flex flex-row">
                                    <div class="mx-2">
{{--                                        <a href="{{route("products.show",$order["id"])}}"--}}
{{--                                           class="btn btn-primary">Details</a>--}}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    <tr style="margin: auto;border-style:none !important; ">
                        <th style="border: none">Total</th>
                        <th style="border: none">${{number_format($total)}}</th>
                    </tr>
                    <tr class="align-middle" style="margin: auto;border-style:none !important; ">
                        <th>
                            <form method="post" action="{{route("users.order_details.store")}}">
                                @csrf
                                <button type="submit"
                                       class="btn btn-primary">
                                    Order
                                </button>
                            </form>
                        </th>
                        <th>
                            <a href="{{route("cart.index")}}"
                               class="btn btn-secondary">Back to cart</a>
                        </th>
                    </tr>

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
        @endrole
    @endauth
@endsection

@section("style")

@endsection
