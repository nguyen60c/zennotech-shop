@extends("layouts.app-master")

@section('title',"Checkout")
@section('content')

    @auth
        <div class="body flex-grow-1" style="padding-right: 5rem;padding-top: 10px">
            <h1>Checkout</h1>
            <h3>Date: {{date('d/m/y',strtotime($time_created))}}</h3>
            <div class="row" style="margin-top: 30px">
                <div class="container bg-secondary bg-opacity-10 p-4 col" style="border-radius: 10px; margin-right: 10px">

                    <label for="title" class="form-label" style="margin-top: 5px">Name</label>
                    <input type="text"
                           class="form-control input_name"
                           name="customer_name"
                           placeholder="name" required>

                    @if ($errors->has('customer_name'))
                        <span class="text-danger text-left">{{ $errors->first('customer_name') }}</span>
                    @endif

                    <label for="title" class="form-label" style="margin-top: 5px">Address</label>
                    <input type="text"
                           class="form-control input_address"
                           name="customer_address"
                           placeholder="Address" required>

                    @if ($errors->has('customer_address'))
                        <span class="text-danger text-left">
                            {{ $errors->first('customer_address') }}</span>
                    @endif

                    <label for="title" class="form-label" style="margin-top: 5px">Phone Number</label>
                    <input type="number"
                           class="form-control input_phone"
                           name="customer_phone"
                           placeholder="Phone number" required>

                    @if ($errors->has('customer_phone'))
                        <span class="text-danger text-left">{{ $errors->first('customer_phone') }}</span>
                    @endif

                </div>

                <div class="container bg-secondary bg-opacity-10 p-4 col" style="border-radius: 10px;">

                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Date Created</th>
                            <th scope="col">Quantity</th>
                            <th scope="col" class="text-center">Price</th>
                        </tr>
                        </thead>
                        <tbody>

{{--                        {{ddd($data)}}--}}
                        <?php $cartIdString = ""; ?>

                        @foreach($data as $order)

                            <?php
                                $cartIdString .= "|" . $order["cart_id"];
                            ?>

                            <tr style="margin: auto;">

                                <td class="align-middle">{{ date('h:i:s A',strtotime($order["hour_update"])) }}</td>
                                <td class="align-middle">{{$order["quantity_temp"]}}</td>
                                <td class="align-middle text-center">${{ number_format($order["price"]) }}</td>
                            </tr>
                        @endforeach

                        <tr style="margin: auto;border-style:none !important; ">
                            <th style="border: none">Total</th>
                            <th style="border: none"></th>
                            <th style="border: none" class="text-center">${{number_format($total)}}</th>
                        </tr>


                        </tbody>
                    </table>


                    <div style="display: flex;justify-content: flex-end">
                        <th>
                            <form method="post" style="margin-right: 10px" action="{{route("cart.checkout.store")}}">
                                @csrf
                                <input type="hidden" value="{{$cartIdString}}" class="input-cartId-hidden" name="cartid">
                                <input type="hidden" value="" class="input-name-hidden" name="name_customer">
                                <input type="hidden" value="" class="input-address-hidden" name="customer_address">
                                <input type="hidden" value="" class="input-phone-hidden" name="customer_phone">
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
                    </div>

                </div>
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

@section("script")

    <script>
        $(document).ready(function() {

            var input_name = $(".input_name");
            var input_address = $(".input_address");
            var input_phone = $(".input_phone");

            input_name.change(function(){
                $(".input-name-hidden").val($(this).val());
            })

            input_address.change(function(){
                $(".input-address-hidden").val($(this).val());
            })

            input_phone.change(function(){
                $(".input-phone-hidden").val($(this).val());
            })

        });
    </script>

@endsection
