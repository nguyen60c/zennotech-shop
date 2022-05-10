@extends("layouts.app-cart")
@section("title","Cart")
@section('content')
    <div class="container" style="">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>
        @if(session()->has('success_msg'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('success_msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @endif
        @if(session()->has('alert_msg'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session()->get('alert_msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @endif
        @if(count($errors) > 0)
            @foreach($errors->all() as $error)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ $error }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            @endforeach
        @endif
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <br>
                @if(count($cart_items) > 0)
                    <h4>{{ count($cart_items)}} Product(s) In Your Cart</h4><br>
                @else
                    <h4>No Product(s) In Your Cart</h4><br>
                    <a href="/" class="btn btn-dark">Continue Shopping</a>
                @endif

                @foreach($cart_items as $item)

                    <div class="row">
                        <div class="col-lg-3">
                            <img src="{{ asset('images/products/' . $item["image"]) }}" class="img-thumbnail"
                                 width="200" height="200">
                        </div>
                        <div class="col-lg-5">

                            <p>
                                <b>
                                    {{ $item["name"] }}
                                </b>
                                <br>
                                <b>Price: </b>
                                ${{ $item["price"] }}
                                <br>
                                <b>Quantity: </b>
                                {{ $item["quantity"] }}
                                <br>
                                <b>Total: </b>
                                <span class="total-price"></span>
                                <br>
                            </p>
                            <div class="row" style="margin-left: 15px">
                                <div class="form-group row">
                                    <span class="text-danger"></span>
                                    <input type="number"
                                           class="form-control form-control-sm quantity_check_cart"
                                           value="{{ $item["quantity_item"] }}"
                                           min="1"
                                           max="100"
                                           id="quantity" name="quantity"
                                           style="width: 70px; margin-right: 10px;"/>
                                </div>

                                <form action="{{route("cart.destroy",$item["id"])}}" method="post">
                                    <button class="btn btn-dark btn-sm"
                                            type="submit"
                                            style="margin-right: 10px;"><i
                                            class="fa fa-trash"></i></button>
                                    <input type="hidden" name="_method" value="delete" />
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </form>
                            </div>
                            <p class="text-danger quantity-annouce" style="width: 260px"></p>
                        </div>
                    </div>
                    <hr>
                @endforeach
                @if(count($cart_items)>0)
                    <form action="{{ route('cart.clear') }}" method="POST">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button class="btn btn-secondary btn-md">Clear Cart</button>
                    </form>
                @endif
            </div>
            @if(count($cart_items)>0)
                <div class="col-lg-5">
                    <div class="card">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <span class="cart-total"></span>
                            </li>
                        </ul>
                    </div>
                    <br>
                    <button class="btn btn-secondary btn-back">Continue Shopping</button>
                    {{--                    <form class="btn-proceed-checkout" style="display: inline-block">--}}
                    <button class="btn btn-success btn-process">Proceed To Checkout</button>
                    {{--                    </form>--}}

                </div>
            @endif
        </div>
        <br><br>
    </div>
@endsection

@section("script")
    <script>
        $(document).ready(function () {


            var price_product = $(".price_product");
            var quantity_cart_arr = $(".quantity_check_cart");
            var quantity_product_arr = $(".quantity_check_product");
            var cart_total = $(".cart-total");
            var total_price = $(".total-price");
            var notif_quantity = $(".quantity-annouce");


            var product_item_check_arr = [];

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            /*Catch event when user change quantity cart items*/
            $.ajax({
                url: "{{url("cart/checkQuantityCartItem")}}",
                method: "GET",
                dataType: "json",
                success: function (res) {
                    var cart_total_price = 0;

                    var product_item_check_arr = res;

                    product_item_arr = res;

                    total_price.each(function (index) {
                        var total_price_cart_item = quantity_cart_arr.eq(index).val() * product_item_check_arr[index]["price"];
                        $(this).text("$" + total_price_cart_item)


                        cart_total_price += total_price_cart_item

                        cart_total.text("Total: $" + cart_total_price)
                    })


                    quantity_cart_arr.each(function (index) {
                        quantity_cart_arr.change(function () {

                            var user_quantity = quantity_cart_arr.eq(index).val();

                            if (product_item_check_arr[index]["quantity"] >= user_quantity && user_quantity > 0 && user_quantity !== "") {

                                notif_quantity.eq(index).text("")

                                var total_price_cart_item = user_quantity * product_item_check_arr[index]["price"];

                                cart_total_price += total_price_cart_item;

                                cart_total.text("Total: $" + cart_total_price)

                                total_price.eq(index).text("$" + total_price_cart_item);

                            } else if (product_item_check_arr[index]["quantity"] < user_quantity || user_quantity < 0) {
                                quantity_cart_arr.eq(index).val(product_item_check_arr[index]["quantity"])
                                notif_quantity.eq(index).text("There are only " + product_item_check_arr[index]["quantity"] + " products!");
                            } else if (user_quantity === "") {

                                notif_quantity.eq(index).text("Please fill your input")
                            }

                        });
                    })

                }
            })

            $(".btn-process").click(function (e) {

                cart_item_quantity = [];

                quantity_cart_arr.each(function (index) {
                    if (quantity_cart_arr.eq(index).val() > 0) {

                        myObjec = {
                            quantity_origin: product_item_arr[index]["quantity"],
                            cart_id: product_item_arr[index]["cart_id"],
                            price_origin: product_item_arr[index]["price"],
                            id_origin: product_item_arr[index]["id"],
                            cart_item_quantity: quantity_cart_arr.eq(index).val()
                        }
                    }
                    cart_item_quantity.push(myObjec);
                });

                $.ajax({
                    method: "POST",
                    url: "{{url("cart/switch-to-checkout")}}",
                    dataType: "json",
                    data: {
                        cart_item_quantity,
                    },
                    success: function (res) {
                        window.location.href = "{{route("cart.checkout.index")}}";
                        console.log(res)
                    }
                })

                if (typeof flat != "undefined") {
                    console.log("hello world")
                }
            })

            $(".btn-back").click(function (e) {

                cart_item_quantity = [];

                quantity_cart_arr.each(function (index) {

                    if (quantity_cart_arr.eq(index).val() > 0) {

                        myObjec = {
                            quantity_origin: product_item_arr[index]["quantity"],
                            cart_id: product_item_arr[index]["cart_id"],
                            price_origin: product_item_arr[index]["price"],
                            id_origin: product_item_arr[index]["id"],
                            cart_item_quantity: quantity_cart_arr.eq(index).val()
                        }
                    }
                    cart_item_quantity.push(myObjec);
                });

                $.ajax({
                    method: "POST",
                    url: "{{url("cart/switch-to-checkout")}}",
                    dataType: "json",
                    data: {
                        cart_item_quantity,
                    },
                    success: function (res) {
                        window.location.href = "{{route("users.products.index")}}";
                        console.log(res)
                    }
                })

                if (typeof flat != "undefined") {
                    console.log("hello world")
                }
            })

            /*When user click Proceed to Checkout button*/


        })
    </script>
@endsection
