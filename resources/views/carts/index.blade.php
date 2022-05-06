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
                                    {{ $item["name"] }}</a>
                                </b>
                                <br>
                                <b>Price: </b>
                                ${{ $item["price"] }}
                                <br>
                                <b>Total: </b>
                                <span class="total-price"></span>
                                <br>
                            </p>
                            <div class="row" style="margin-left: 15px">
                                <form action="{{route("cart.update")}}" method="POST">
                                    {{ csrf_field() }}
                                    <div class="form-group row">
                                        <input type="hidden" value="{{ $item["id"]}}" id="id" name="id">
                                        <span class="text-danger"></span>
                                        <input type="hidden" class="price_product" readonly
                                               style="display: none" value="{{ $item["price"] }}">
                                        <input type="hidden" class="quantity_check_product" readonly
                                               style="display: none" value="{{ $item["quantity"] }}">
                                        <input type="number"
                                               class="form-control form-control-sm quantity_check_cart"
                                               value="{{ $item["quantity_item"] }}"
                                               min="1"
                                               max="100"
                                               id="quantity" name="quantity"
                                               style="width: 70px; margin-right: 10px;">
                                        <button class="btn btn-secondary btn-sm"
                                                style="margin-right: 25px;"><i
                                                class="fa fa-edit"></i></button>
                                    </div>
                                </form>
                                <form action="{{route("cart.destroy")}}" method="post">
                                    {{ csrf_field() }}
                                    <input type="hidden" style="display: none" value="{{ $item["cart_id"] }}" id="id" name="cart_id">
                                    <input type="hidden" style="display: none" value="{{ $item["id"] }}" id="p_id" name="product_id">
                                    <button class="btn btn-dark btn-sm"
                                            style="margin-right: 10px;"><i
                                            class="fa fa-trash"></i></button>
                                </form>
                            </div>
                            <p class="text-danger quantity-annouce"></p>
                        </div>
                    </div>
                    <hr>
                @endforeach
                @if(count($cart_items)>0)
                    <form action="{{ route('cart.clear') }}" method="POST">
                        {{ csrf_field() }}
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
                    <br><a href="{{route("users.products.index")}}"
                           class="btn btn-dark">Continue Shopping</a>
                    <a href="{{route("users.order_details.index")}}"
                       class="btn btn-success">Proceed To Checkout</a>
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


            $(quantity_cart_arr).each(function (index) {
                $.ajax({
                    url: "http://127.0.0.1:8000/cart/update",
                    method: "GET",
                    datatype: "json",
                    success: function (res) {

                        var quantity_cart = parseInt(quantity_cart_arr.eq(index).val());
                        var quantity_product = parseInt(quantity_product_arr.eq(index).val());
                        var total = quantity_cart * parseInt(price_product.eq(index).val());
                        var total_price = 0;

                        $(".total-price").eq(index).text("$" + total)
                        $(".quantity-annouce").eq(index).text("");


                        $.each(quantity_cart_arr, function (index, value) {
                            total_price += parseInt(quantity_cart_arr.eq(index).val()) * parseInt(price_product.eq(index).val());
                        });

                        cart_total.text("Total: $" + total_price);

                    }
                })
            });

            $(quantity_cart_arr).each(function (index) {
                $(this).on("change click keypress", function () {
                    $.ajax({
                        url: "http://127.0.0.1:8000/cart",
                        method: "GET",
                        datatype: "json",
                        success: function (res) {

                            var quantity_cart = parseInt(quantity_cart_arr.eq(index).val());
                            var quantity_product = parseInt(quantity_product_arr.eq(index).val());

                            if (quantity_product < quantity_cart || quantity_cart <= 0) {
                                $(".quantity-annouce").eq(index).text("Out of reach");
                            } else {
                                var total = quantity_cart * parseInt(price_product.eq(index).val());
                                console.log(total)
                                $(".total-price").eq(index).text("$" + total)
                                $(".quantity-annouce").eq(index).text("");
                            }
                        }
                    })
                });
            });


        })
    </script>
@endsection
