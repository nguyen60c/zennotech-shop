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
                    <input type="checkbox" name="all_cart_items">
                    Select all

                    <br>
                @else
                    <h4>No Product(s) In Your Cart</h4><br>
                    <a href="/" class="btn btn-dark">Continue Shopping</a>
                @endif


                {{-- {{ddd($cart_items)}} --}}
                @foreach ($listCartItems as $item)
                    <input type="checkbox" data-name="{{ $item['name'] }}" data-productId="{{ $item['id'] }}"
                           data-creatorId="{{ $item['creator_id'] }}" data-cartId="{{ $item['cart_id'] }}"
                           data-quantityCartItem="{{ $item['quantity_item'] }}" class="cart_item">

                    <div class="row">
                        <div class="col-lg-3">
                            <img src="{{ asset('images/products/' . $item['image']) }}" class="img-thumbnail"
                                 width="200"
                                 height="200">
                        </div>
                        <div class="col-lg-5">

                            <p>
                                <b>
                                    {{ $item['name'] }}
                                </b>
                                <br>
                                <b>Price: $</b>
                                <span class="cart-item-price">{{ $item['price'] }}</span>
                                <br>
                                <b>Quantity: </b>
                                {{ $item['quantity'] }}
                                <br>
                                <b>Total: </b>
                                <span class="total-price"></span>
                                <br>
                            </p>
                            <div class="row" style="margin-left: 15px">
                                <div class="form-group row">
                                    <span class="text-danger"></span>
                                    <input type="number" class="form-control form-control-sm quantity_check_cart"
                                           value="{{ $item['quantity_item'] }}" min="1" max="100" id="quantity"
                                           name="quantity" style="width: 70px; margin-right: 10px;"/>
                                </div>

                                <form action="{{ route('cart.destroy', $item['id']) }}" method="post">
                                    <button class="btn btn-dark btn-sm" type="submit" style="margin-right: 10px;"><i
                                            class="fa fa-trash"></i></button>
                                    <input type="hidden" name="_method" value="delete"/>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </form>
                            </div>
                            <p class="text-danger quantity-annouce" style="width: 260px"></p>
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
                            <li class="list-group-item">
                                <span class="cart-total"></span>
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
        $(document).ready(function () {


            var quantityCartItems = $(".quantity_check_cart");
            var checkBoxCartItems = $(".cart_item");
            var cartItemsTotalPrice = $(".cart-total");
            var cartItemPrice = $(".cart-item-price")
            var cartItemsPrice = $(".total-price");
            var quanityAlert = $(".quantity-annouce");


            var productItemArr = [];

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            /*Catch event when user change quantity cart items*/
            $.ajax({
                url: "{{ url('cart/checkQuantityCartItem') }}",
                method: "GET",
                dataType: "json",
                success: function (res) {
                    /*Default value when browser reload*/

                    let priceCartItems = 0;
                    let priceCartItem = 0;
                    let totalPriceCartItems = 0;
                    let totalPriceCartItemsArray = [];

                    productItemArr = res;

                    /*Contain list of checkbox which is selected by user*/
                    selectedCartItems = [];


                    /*Select/Remove all items in cart*/
                    $('[name="all_cart_items"]').on('click', function (index) {
                        // debugger;
                        if ($(this).is(':checked')) {
                            $.each($('.cart_item'), function (index) {
                                $(this).prop('checked', true);
                                totalPriceCartItems += parseInt(cartItemPrice.eq(index)
                                        .text()) *
                                    quantityCartItems.eq(index).val();
                                cartItemsTotalPrice.text("Total: $" +
                                    totalPriceCartItems)
                                selectedCartItems.push(productItemArr[index]);
                            });
                        } else {
                            $.each($('.cart_item'), function () {
                                $(this).prop('checked', false);
                                totalPriceCartItems = 0;
                                cartItemsTotalPrice.text("Total: $" +
                                    totalPriceCartItems);
                                selectedCartItems.length = 0;
                            });
                        }
                    });

                    /*Handle calculate checkbox cart items*/
                    checkBoxCartItems.each(function (index) {

                        $(this).on("click", function () {
                            // debugger
                            if ($(this).is(':checked')) {
                                $(this).prop('checked', true);
                                priceCartItem = parseInt(cartItemPrice.eq(index)
                                    .text());
                                quantityCartItem = quantityCartItems.eq(index).val();
                                totalPriceCartItems += priceCartItem * quantityCartItem;
                                cartItemsTotalPrice.text("Total: $" +
                                    totalPriceCartItems)
                            } else {
                                $(this).prop('checked', false);

                                priceCartItem = parseInt(cartItemPrice.eq(index)
                                    .text());
                                quantityCartItem = quantityCartItems.eq(index).val();

                                totalPriceCartItems -= priceCartItem * quantityCartItem;
                                totalPriceCartItems = totalPriceCartItems >= 0 ?
                                    totalPriceCartItems : 0;
                                cartItemsTotalPrice.text("Total: $" +
                                    totalPriceCartItems)
                            }

                        })

                    })

                    var inputQuantityElements = [];

                    /*Handle display and calculate total price of cart items*/
                    cartItemsPrice.each(function (index) {

                        var priceCartItem = quantityCartItems.eq(index).val() *
                            productItemArr[index]["price"];

                        /*Display price cart item*/
                        $(this).text("$" + priceCartItem);

                        /*Display total price cart items*/
                        cartItemsTotalPrice.text("Total: $" + 0)
                    })

                    var totalPriceCartItems_second = 0;
                    var totalPriceCartItems_second_temp = 0

                    /*Check quantity when user input value into quantity of cart item*/
                    quantityCartItems.each(function (index) {


                        // Handle caculate after change input
                        // Get old value input
                        let oldValueInputCartItem = [];
                        quantityCartItems.each(function (index) {
                            oldValueInputCartItem.push(quantityCartItems.eq(index)
                                .val());
                        })
                        totalPriceCartItems_second = 0;
                        var count = 0;


                        quantityCartItems.change(function () {
                            // totalPriceCartItems = 0;
                            // debugger;

                            /*Get quantity cart item when user input value*/
                            var inputQuantityCartItem = quantityCartItems.eq(index)
                                .val();

                            /*Reset total price item every event*/
                            resultPriceCartItem = 0;
                            resultPriceCartItemsTotal = 0;

                            if (productItemArr[index]["quantity"] >=
                                inputQuantityCartItem &&
                                inputQuantityCartItem > 0 &&
                                inputQuantityCartItem !== "") {

                                if (checkBoxCartItems.eq(index).is(':checked')) {

                                    if (index === 0) {
                                        totalPriceCartItems_second = 0;
                                        totalPriceCartItems_second_temp = 0;
                                    }

                                    if (totalPriceCartItems_second_temp ===
                                        totalPriceCartItems_second) {

                                        quantityCartItem = quantityCartItems.eq(index).val();
                                        totalPriceCartItems_second_temp +=
                                            priceCartItem *
                                            quantityCartItem;
                                        cartItemsTotalPrice.text("Total: $" +
                                            totalPriceCartItems_second_temp)
                                    } else {
                                        totalPriceCartItems_second_temp +=
                                            priceCartItem * quantityCartItem;
                                        cartItemsTotalPrice.text("Total: $" +
                                            totalPriceCartItems_second_temp)
                                    }

                                    var priceCartItems = inputQuantityCartItem *
                                        productItemArr[index]["price"];

                                    resultPriceCartItem += priceCartItems;
                                    cartItemsPrice.eq(index).text("$" +
                                        resultPriceCartItem);

                                }
                                quanityAlert.eq(index).text("")

                            } else if (productItemArr[index]["quantity"] <
                                inputQuantityCartItem ||
                                inputQuantityCartItem < 0) {

                                quantityCartItems.eq(index).val(productItemArr[index][
                                    "quantity"
                                    ]);

                                quanityAlert.eq(index).text("There are only " +
                                    productItemArr[index]["quantity"] +
                                    " products!");

                            } else if (inputQuantityCartItem === "") {
                                quanityAlert.eq(index).text("Please fill your input");
                            }
                        });
                    })
                }
            })

            /*Catch event when user want to see checkout page*/
            $(".btn-process").click(function (e) {

                cart_item_quantity = [];

                selectedCartItem = $(".cart_item:checked");

                /*Update quantity*/

                $(".quantity_check_cart").each(function (index) {
                    $(".cart_item").eq(index).data("quantitycartitem", $(this).val());
                })

                selectedCartItem.each(function (index) {

                    if (selectedCartItem.eq(index).data("quantitycartitem") > 0) {

                        myObjec = {
                            userInputQuantity: selectedCartItem.eq(index).data("quantitycartitem"),
                            creatorId: selectedCartItem.eq(index).data("creatorid"),
                            cartId: selectedCartItem.eq(index).data("cartid"),
                            productId: selectedCartItem.eq(index).data("productid")
                        }
                        cart_item_quantity.push(myObjec);

                    }
                });

                $.ajax({
                    method: "POST",
                    url: "{{ url('cart/add-to-order-details') }}",
                    dataType: "json",
                    data: {
                        cart_item_quantity,
                    },
                    success: function (res) {
                        if (res) {
                            window.location.href = "{{route("cart.checkout.index")}}";
                            console.log(res)
                        } else {
                            $(".announce_quantity_result").text("You must choose product have the same seller !!");
                        }
                    }
                })

                if (typeof flat != "undefined") {
                    console.log("hello world")
                }
            })

            $(".btn-back").click(function (e) {

                cart_item_quantity = [];

                selectedCartItem = $(".cart_item:checked");

                /*Update quantity*/

                $(".quantity_check_cart").each(function (index) {
                    $(".cart_item").eq(index).data("quantitycartitem", $(this).val());
                    console.log($(".cart_item").eq(index).data("quantitycartitem"))
                })

                selectedCartItem.each(function (index) {

                    if (selectedCartItem.eq(index).data("quantitycartitem") > 0) {

                        myObjec = {
                            userInputQuantity: selectedCartItem.eq(index).data("quantitycartitem"),
                            creatorId: selectedCartItem.eq(index).data("creatorid"),
                            cartId: selectedCartItem.eq(index).data("cartid"),
                            productId: selectedCartItem.eq(index).data("productid")
                        }
                        cart_item_quantity.push(myObjec);

                    }
                });

                $.ajax({
                    method: "POST",
                    url: "{{ url('cart/add-to-order-details') }}",
                    dataType: "json",
                    data: {
                        cart_item_quantity,
                    },
                    success: function (res) {
                        if (res) {
                            window.location.href = "{{route("users.products.index")}}";
                        }
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
