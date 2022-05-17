@extends('layouts.app-cart')
@section('title', 'Cart')
@section('content')
    <div class="container" style="">
        @include('layouts.partials.message')
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>
        @if (session()->has('success_msg'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('success_msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @endif
        @if (session()->has('alert_msg'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session()->get('alert_msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @endif
        @if (count($errors) > 0)
            @foreach ($errors->all() as $error)
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
                <h4 class="text-danger announce_quantity_result"></h4>
                <br>
                @if (count($listCartItems) > 0)
                    <h4>{{ count($listCartItems) }} Product(s) In Your Cart</h4><br>
                    <h4 class="text-annouce text-danger"></h4>
                    <input type="checkbox" name="all_cart_items">
                    Select all

                    <br>
                @else
                    <h4>No Product(s) In Your Cart</h4><br>
                    <a href="/" class="btn btn-dark">Continue Shopping</a>
                @endif


                {{-- {{ddd($cart_items)}} --}}
                @foreach ($listCartItems as $item)
                    <input type="checkbox"
                           data-name="{{ $item['name'] }}" data-productId="{{ $item['id'] }}"
                           data-creatorId="{{ $item['creator_id'] }}" data-cartId="{{ $item['cart_id'] }}"
                           data-quantityCartItem="{{ $item['quantity_item'] }}"
                           onchange="selectedCrd({{$item["cart_id"]}})"
                           onkeyup="selectedCrd({{$item["cart_id"]}})"
                           class="cart_item cart_item_checkbox-{{$item["cart_id"]}}">

                    <div class="row">
                        <div class="col-lg-3">
                            <img src="{{ asset('images/products/' . $item['image']) }}" class="img-thumbnail"
                                 width="200"
                                 height="200">
                        </div>
                        <div class="col-lg-5">
                            <p>
                                <b>
                                    <span class="product-name">{{ $item['name'] }}</span>
                                </b>
                                <br>
                                <b>Price: $</b>
                                <span class="cart-item-price">{{ $item['price'] }}</span>
                                <br>
                                <b>Quantity: </b>
                                <input type="hidden" value="{{$item['quantity_item']}}"
                                       class="qty qty-{{$item["cart_id"]}}">
                                <input type="hidden" value="{{$item['price']}}"
                                       class="price price-{{$item["cart_id"]}}">
                                <span class="product-quantity">
                                {{ $item['quantity'] }}
                                </span>
                                <br>
                                <b>Total: </b>
                                <span class="total-price-{{$item["cart_id"]}}">${{$item["price"]}}</span>
                                <br>
                                <a class="btn btn-warning"
                                   href="{{route("users.products.details",$item["id"])}}"
                                   style="font-weight: 700">
                                    More infor
                                </a>
                            </p>
                            <div class="row" style="margin-left: 15px">
                                <div class="form-group row">
                                    <span class="text-danger"></span>
                                    <form id="frm-{{$item["cart_id"]}}">
                                        <input type="hidden" name="cart_id" value="{{$item["cart_id"]}}">
                                        <input type="hidden" class="creator-{{$item["cart_id"]}}" name="cart_id" value="{{$item["creator_id"]}}">
                                        <input type="hidden" class="product-{{$item["cart_id"]}}" name="cart_id" value="{{$item["id"]}}">
                                        <input type="number"
                                               class="form-control form-control-sm user_input_qty user_input_qty-{{$item["cart_id"]}}"
                                               value="{{ $item['quantity_item'] }}" min="1" max="100" id="quantity"
                                               name="quantity" style="width: 70px; margin-right: 10px;"
                                               data-id="{{$item["cart_id"]}}"
                                               onchange="updateQty({{$item["cart_id"]}})"
                                               onkeyup="updateQty({{$item["cart_id"]}})"/>
                                    </form>
                                </div>

                                <form action="{{ route('cart.destroy', $item['id']) }}" method="post">
                                    <button class="btn btn-dark btn-sm" type="submit" style="margin-right: 10px;"><i
                                            class="fa fa-trash"></i></button>
                                    <input type="hidden" name="_method" value="delete"/>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </form>
                            </div>
                            <p class="text-danger quantity-annouce-{{$item["cart_id"]}}" style="width: 260px"></p>
                        </div>
                    </div>
                    <hr>
                @endforeach
                @if (count($listCartItems) > 0)
                    <form action="{{ route('cart.clear') }}" method="POST">
                        <input type="hidden" name="_method" value="delete"/>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button class="btn btn-secondary btn-md">Clear Cart</button>
                    </form>
                @endif
            </div>
            @if (count($listCartItems) > 0)
                <div class="col-lg-5">
                    <div class="card">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Total:</strong>
                                <span class="cart-total">$0</span>
                            </li>
                        </ul>
                    </div>
                    <br>
                    <button class="btn btn-secondary btn-back">Continue Shopping</button>
                    <button class="btn btn-success btn-process">Proceed To Checkout</button>
                </div>
            @endif
        </div>
        <br><br>
    </div>
@endsection

@section('script')
    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let totalPriceItems = 0;
        let cartItemTotalPriceTemp = 0;

        function updateQty(id) {

            setTimeout(function () {
                $(".user_input_qty-" + id).prop('readonly', true)
            }, 0)

            $.ajax({
                url: "{{route("cart.ajax.updateQty")}}",
                method: "POST",
                data: $("#frm-" + id).serialize(),
                success: function (res) {


                    if (res["msg"] === "") {
                        let cartItemTotalPrice = res["inputQty"] * res["price"];
                        $(".total-price-" + id).text("$" + cartItemTotalPrice)
                        $(".quantity-annouce-" + id).text("");
                        $(".qty-" + id).val(res["inputQty"]);
                        $(".price-" + id).val(res["price"]);
                        $(".cart_item_checkbox-" + id).data("quantitycartitem", res["inputQty"]);

                        totalPriceItems = 0;
                        $(".user_input_qty").each(function (index) {
                            let isSelected = $(".cart_item_checkbox-" + id).prop("checked");

                            if (isSelected) {
                                let quantityItem = $(".qty").eq(index).val();
                                let PriceItem = $(".price").eq(index).val();

                                totalPriceItems += quantityItem * PriceItem;
                                $(".cart-total").text("$" + totalPriceItems);
                            }

                        })
                    } else if (res["msg"] === "Your quantity is invalid") {
                        $(".quantity-annouce-" + id).text("Your input is invalid value!. Please try again.")
                    } else if (res["msg"] === "Your quantity is out of bound") {

                        $(".quantity-annouce-" + id).text("Your quantity is out of bound.")
                    }

                }
            })

            setTimeout(function () {
                $(".user_input_qty-" + id).prop('readonly', false)
            }, 1000)
        }

        totalPriceItems = 0;
        /*Select all cart items*/
        $('[name="all_cart_items"]').on('click', function () {

            if ($(this).is(':checked')) {
                $.each($('.cart_item'), function (index) {
                    $(this).prop('checked', true);

                    let userInputQty = $(".qty").eq(index).val();
                    let cartItemPrice = $(".price").eq(index).val();

                    totalPriceItems += userInputQty * cartItemPrice;
                    $(".cart-total").text("$" + totalPriceItems);
                });
            } else {
                $.each($('.cart_item'), function () {
                    $(this).prop('checked', false);
                    totalPriceItems = 0;
                    $(".cart-total").text("$" + 0);
                });
            }
        });

        /*Reset total price items*/
        totalPriceItems = 0;

        /*Select and calculate selected cart items*/
        function selectedCrd(id) {

            let isSelected = $(".cart_item_checkbox-" + id).prop("checked");


            let userInputQty = $(".qty-" + id).val();
            let cartItemPrice = $(".price-" + id).val();

            if (isSelected) {
                totalPriceItems += userInputQty * cartItemPrice;
                $(".cart-total").text("$" + totalPriceItems);
            } else {
                totalPriceItems -= userInputQty * cartItemPrice;
                $(".cart-total").text("$" + totalPriceItems);
                $('[name="all_cart_items"]').prop("checked",false);
            }
        }


        $(".btn-process").click(function () {

            let request = [];

            $(".cart_item:checked").each(function (index) {
                let clSelectedCartItem = $(this).attr("class");
                let cartId = clSelectedCartItem.split("-")[1];
                let productId = $(".product-" + cartId).val();
                let qtyItem = $(".user_input_qty-" + cartId).val();
                let creatorId = $(".creator-" + cartId).val();

                console.log(cartId)
                if (qtyItem > 0) {
                    myObjec = {
                        userInputQuantity: qtyItem,
                        creatorId: creatorId,
                        cartId: cartId,
                        productId: productId
                    }

                    request.push(myObjec);
                }

            })

            console.log(request.length)
            if(request.length > 0){
                $.ajax({
                    url: "{{route("cart.ordersDetails.add")}}",
                    method: "POST",
                    dataType: "json",
                    data:{
                        request
                    },
                    success: function (res) {
                        console.log(res)
                        if (res[0]) {
                            window.location.href = "{{route("cart.checkout.index")}}";
                        } else if (res[0] === false) {
                            $(".announce_quantity_result").text("You must choose product have the same seller !!");
                        }
                    }
                })
            }


        })


    </script>
@endsection
