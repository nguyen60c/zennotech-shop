@extends('layouts.app-cart')
@section('title', 'Cart')
@section('content')
    <div class="container" style="">
        @include('layouts.partials.message')
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <button class="btn btn-link nav-home" style="padding: 0">Home</button>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>
        <div class="container-wrapper">
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
                    @if (count($cartItems) > 0)
                        <h4><span class="total_qty_items">{{ count($cartItems) }}</span> Product(s) In Your Cart
                        </h4><br>
                        <h4 class="text-annouce text-danger"></h4>
                        <input type="checkbox" name="all_cart_items">
                        Select all

                        <br>
                    @else
                        <h4>No Product(s) In Your Cart</h4><br>
                        <a href="/" class="btn btn-dark">Continue Shopping</a>
                    @endif

                    @if (count($cartItems) > 0)
                        <?php $prevCreatorId = $cartItems[0]['creator_id'] ?>
                        <?php $flat = 0 ?>
                        @foreach ($cartItems as $key => $item)

                            @if($prevCreatorId === $item['creator_id'])

                                @if($flat === 0)
                                    <span>{{$item['seller']}}</span>
                                    <?php $flat = 1; ?>
                                @endif

                                @if($item["quantity_item"] > 0)
                                    <div class="cart_item-{{$item["cart_id"]}}">
                                        <input type="checkbox"
                                               data-name="{{ $item['name'] }}" data-productId="{{ $item['id'] }}"
                                               data-creatorId="{{ $item['creator_id'] }}"
                                               data-cartId="{{ $item['cart_id'] }}"
                                               data-quantityCartItem="{{ $item['quantity_item'] }}"
                                               onchange="selectedCrd({{$item["cart_id"]}})"
                                               onkeyup="selectedCrd({{$item["cart_id"]}})"
                                               class="cart_item cart_item_checkbox-{{$item["cart_id"]}}">

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <img src="{{ asset('images/products/' . $item['image']) }}"
                                                     class="img-thumbnail"
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
                                                    <input type="hidden"
                                                           value="{{$item['quantity_item'] === 0 ? 0 : $item['quantity_item']}}"
                                                           class="qty qty-{{$item["cart_id"]}}">
                                                    <input type="hidden" value="{{$item['price']}}"
                                                           class="price price-{{$item["cart_id"]}}">

                                                    <span class="product-quantity">
                                {{ $item['quantity']}}
                                </span>
                                                    <br>
                                                    <b>Total: </b>
                                                    <span
                                                        class="total-price-{{$item["cart_id"]}}">${{$item["price"] * $item["quantity_item"]}}</span>
                                                    <br>
                                                    <a class="btn btn-warning"
                                                       href="{{route("users.products.details",$item["id"])}}"
                                                       style="font-weight: 700">
                                                        More infor
                                                    </a>
                                                    <br>
                                                    <span class="stock">

                                        </span>
                                                </p>
                                                <div class="row" style="margin-left: 15px">
                                                    <div class="form-group row">
                                                        <span class="text-danger"></span>
                                                        <form id="frm-{{$item["cart_id"]}}">

                                                            <input type="hidden" name="cart_id"
                                                                   value="{{$item["cart_id"]}}">
                                                            <input type="hidden" class="creator-{{$item["cart_id"]}}"
                                                                   name="creator_id"
                                                                   value="{{$item["creator_id"]}}">
                                                            <input type="hidden" class="product-{{$item["cart_id"]}}"
                                                                   name="product_id"
                                                                   value="{{$item["id"]}}">
                                                            <input type="number"
                                                                   class="form-control form-control-sm user_input_qty user_input_qty-{{$item["cart_id"]}}"
                                                                   value="{{ $item['quantity_item'] }}" min="1"
                                                                   id="quantity"
                                                                   name="quantity"
                                                                   style="width: 70px; margin-right: 10px;"
                                                                   data-id="{{$item["cart_id"]}}"
                                                                   onchange="updateQty({{$item["cart_id"]}})"
                                                                   onkeyup="updateQty({{$item["cart_id"]}})"/>
                                                        </form>
                                                    </div>

                                                    <div>
                                                        <button
                                                            class="btn btn-dark btn-sm btn-delete-{{$item["cart_id"]}}"
                                                            type="submit" style="margin-right: 10px;"
                                                            onclick="delCartItem({{$item["cart_id"]}})">
                                                            <i
                                                                class="fa fa-trash"></i></button>
                                                        <input type="hidden" name="_method" value="delete"/>
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    </div>
                                                </div>
                                                <p class="text-danger quantity-annouce-{{$item["cart_id"]}}"
                                                   style="width: 260px"></p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="cart_item-{{$item["cart_id"]}}">
                                        <input type="text"
                                               value="Out of stock"
                                               readonly>

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <img src="{{ asset('images/products/' . $item['image']) }}"
                                                     class="img-thumbnail"
                                                     width="200"
                                                     height="200">
                                            </div>
                                            <div class="col-lg-5">
                                                <p>
                                                    <b>
                                                        <span>{{ $item['name'] }}</span>
                                                    </b>
                                                    <br>
                                                    <b>Price: $</b>
                                                    <span>{{ $item['price'] }}</span>
                                                    <br>
                                                    <b>Quantity: </b>

                                                    <span class="product-quantity">
                                {{ $item['quantity']}}
                                </span>
                                                    <br>
                                                    <b>Total: </b>
                                                    <span>${{$item["price"] * $item["quantity_item"]}}</span>
                                                    <br>
                                                    <a class="btn btn-warning"
                                                       href="{{route("users.products.details",$item["id"])}}"
                                                       style="font-weight: 700">
                                                        More infor
                                                    </a>
                                                    <br>
                                                    <span class="stock">

                                        </span>
                                                </p>
                                                <div class="row" style="margin-left: 15px">
                                                    <div class="form-group row">
                                                        <span class="text-danger"></span>

                                                    </div>

                                                    <div>
                                                        <button
                                                            class="btn btn-dark btn-sm btn-delete-{{$item["cart_id"]}}"
                                                            type="submit" style="margin-right: 10px;"
                                                            onclick="delCartItem({{$item["cart_id"]}})">
                                                            <i
                                                                class="fa fa-trash"></i></button>
                                                        <input type="hidden" name="_method" value="delete"/>
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    </div>
                                                </div>
                                                <p class="text-danger quantity-annouce-{{$item["cart_id"]}}"
                                                   style="width: 260px"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <span>{{$item['seller']}}</span>
                                <?php
                                $prevCreatorId = $item['creator_id'] ?>
                                @if($item["quantity_item"] > 0)
                                    <div class="cart_item-{{$item["cart_id"]}}">
                                        <input type="checkbox"
                                               data-name="{{ $item['name'] }}" data-productId="{{ $item['id'] }}"
                                               data-creatorId="{{ $item['creator_id'] }}"
                                               data-cartId="{{ $item['cart_id'] }}"
                                               data-quantityCartItem="{{ $item['quantity_item'] }}"
                                               onchange="selectedCrd({{$item["cart_id"]}})"
                                               onkeyup="selectedCrd({{$item["cart_id"]}})"
                                               class="cart_item cart_item_checkbox-{{$item["cart_id"]}}">

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <img src="{{ asset('images/products/' . $item['image']) }}"
                                                     class="img-thumbnail"
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
                                                    <input type="hidden"
                                                           value="{{$item['quantity_item'] === 0 ? 0 : $item['quantity_item']}}"
                                                           class="qty qty-{{$item["cart_id"]}}">
                                                    <input type="hidden" value="{{$item['price']}}"
                                                           class="price price-{{$item["cart_id"]}}">

                                                    <span class="product-quantity">
                                {{ $item['quantity']}}
                                </span>
                                                    <br>
                                                    <b>Total: </b>
                                                    <span
                                                        class="total-price-{{$item["cart_id"]}}">${{$item["price"] * $item["quantity_item"]}}</span>
                                                    <br>
                                                    <a class="btn btn-warning"
                                                       href="{{route("users.products.details",$item["id"])}}"
                                                       style="font-weight: 700">
                                                        More infor
                                                    </a>
                                                    <br>
                                                    <span class="stock">

                                        </span>
                                                </p>
                                                <div class="row" style="margin-left: 15px">
                                                    <div class="form-group row">
                                                        <span class="text-danger"></span>
                                                        <form id="frm-{{$item["cart_id"]}}">

                                                            <input type="hidden" name="cart_id"
                                                                   value="{{$item["cart_id"]}}">
                                                            <input type="hidden" class="creator-{{$item["cart_id"]}}"
                                                                   name="creator_id"
                                                                   value="{{$item["creator_id"]}}">
                                                            <input type="hidden" class="product-{{$item["cart_id"]}}"
                                                                   name="product_id"
                                                                   value="{{$item["id"]}}">
                                                            <input type="number"
                                                                   class="form-control form-control-sm user_input_qty user_input_qty-{{$item["cart_id"]}}"
                                                                   value="{{ $item['quantity_item'] }}" min="1"
                                                                   id="quantity"
                                                                   name="quantity"
                                                                   style="width: 70px; margin-right: 10px;"
                                                                   data-id="{{$item["cart_id"]}}"
                                                                   onchange="updateQty({{$item["cart_id"]}})"
                                                                   onkeyup="updateQty({{$item["cart_id"]}})"/>
                                                        </form>
                                                    </div>

                                                    <div>
                                                        <button
                                                            class="btn btn-dark btn-sm btn-delete-{{$item["cart_id"]}}"
                                                            type="submit" style="margin-right: 10px;"
                                                            onclick="delCartItem({{$item["cart_id"]}})">
                                                            <i
                                                                class="fa fa-trash"></i></button>
                                                        <input type="hidden" name="_method" value="delete"/>
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    </div>
                                                </div>
                                                <p class="text-danger quantity-annouce-{{$item["cart_id"]}}"
                                                   style="width: 260px"></p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="cart_item-{{$item["cart_id"]}}">
                                        <input type="text"
                                               value="Out of stock"
                                               readonly>

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <img src="{{ asset('images/products/' . $item['image']) }}"
                                                     class="img-thumbnail"
                                                     width="200"
                                                     height="200">
                                            </div>
                                            <div class="col-lg-5">
                                                <p>
                                                    <b>
                                                        <span>{{ $item['name'] }}</span>
                                                    </b>
                                                    <br>
                                                    <b>Price: $</b>
                                                    <span>{{ $item['price'] }}</span>
                                                    <br>
                                                    <b>Quantity: </b>

                                                    <span class="product-quantity">
                                {{ $item['quantity']}}
                                </span>
                                                    <br>
                                                    <b>Total: </b>
                                                    <span>${{$item["price"] * $item["quantity_item"]}}</span>
                                                    <br>
                                                    <a class="btn btn-warning"
                                                       href="{{route("users.products.details",$item["id"])}}"
                                                       style="font-weight: 700">
                                                        More infor
                                                    </a>
                                                    <br>
                                                    <span class="stock">

                                        </span>
                                                </p>
                                                <div class="row" style="margin-left: 15px">
                                                    <div class="form-group row">
                                                        <span class="text-danger"></span>

                                                    </div>

                                                    <div>
                                                        <button
                                                            class="btn btn-dark btn-sm btn-delete-{{$item["cart_id"]}}"
                                                            type="submit" style="margin-right: 10px;"
                                                            onclick="delCartItem({{$item["cart_id"]}})">
                                                            <i
                                                                class="fa fa-trash"></i></button>
                                                        <input type="hidden" name="_method" value="delete"/>
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    </div>
                                                </div>
                                                <p class="text-danger quantity-annouce-{{$item["cart_id"]}}"
                                                   style="width: 260px"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif



                        @endforeach
                        @if (count($cartItems) > 0)
                            <input type="hidden" id="user_id" value="{{$userId}}">
                            <button class="btn btn-secondary btn-md" onclick="selectedDel()">Delete</button>
                        @endif
                    @endif
                </div>
                @if (count($cartItems) > 0)
                    <div class="col-lg-5">
                        <div class="card">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Total:</strong>
                                    <span class="cart-total">$0</span>
                                </li>
                                <li class="list-group-item"><strong>Selected items:</strong>
                                    <span class="selected-items">0</span>
                                </li>
                            </ul>
                        </div>
                        <br>
                        <button class="btn btn-secondary btn-back">Continue Shopping</button>
                        <button class="btn btn-success btn-process">Proceed To Checkout</button>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        /*Prevent key tab on keyboard*/
        $(document).ready(function () {
            $(window).on("keydown", function (e) {
                if (e.keyCode === 9) {
                    e.preventDefault();
                }
            });
        });


        let totalPriceItems = 0;
        let cartItemTotalPriceTemp = 0;

        $(".product-quantity").each(function (index) {
            let quantity = parseInt($(this).text());
            if (quantity === 0) {
                $(".stock").eq(index).append("<a class=\"btn btn-secondary\"\n" +
                    "                                           href=\"#\"\n" +
                    "                                           style=\"font-weight: 700\">\n" +
                    "                                            Out of stock\n" +
                    "                                        </a>")
            }
        });

        function delCartItem(id) {

            $(".cart_item-" + id).remove();

            let cartItemsLeft = $(".cart_item").length;

            if (cartItemsLeft === 0) {
                $(".container-wrapper")
                    .html("<h4>No Product(s) In Your Cart</h4><br>\n" +
                        "                    <a href=\"/\" class=\"btn btn-dark\">Continue Shopping</a>");
            }

            $(".user_input_qty").each(function (index) {


                if ($(".cart_item").eq(index).prop("checked")) {
                    let userInputQty = $(this).val();
                    let itemPrice = $(".price").eq(index).val();

                    let totalPriceItems = userInputQty * itemPrice;

                    $(".cart-total").text(totalPriceItems);
                }
            })
            cartItemsLeft = $(".cart_item").length;
            $(".total_qty_items").text(cartItemsLeft)
            let totalSelectedItems = $(".cart_item:checked").length;
            $(".selected-items").text(totalSelectedItems);

            totalPriceItems = 0;
            $(".user_input_qty").each(function (index) {
                let isSelected = $(".cart_item").eq(index).prop("checked");

                if (isSelected) {
                    let quantityItem = $(".qty").eq(index).val();
                    let PriceItem = $(".price").eq(index).val();

                    console.log(quantityItem)
                    console.log(PriceItem)
                    totalPriceItems += quantityItem * PriceItem;
                    $(".cart-total").text("$" + totalPriceItems);
                }

            })

            $.ajax({
                url: "{{route("cart.ajax.delCartItem")}}",
                method: "POST",
                data: {
                    id
                },
                success: function (res) {
                }
            })
        }

        let userId = $("#user_id").val();
        let cartIdArray = [];

        function selectedDel() {
            if ($('[name="all_cart_items"]').is(":checked")) {

                emptyCrdItems();

                $.ajax({
                    url: "{{route("cart.ajax.clear")}}",
                    method: "DELETE",
                    data: {
                        id: userId
                    },
                    success: function (res) {
                        console.log(res)
                    }
                })
            } else {
                if ($(".cart_item:checked").length > 0) {
                    $(".cart_item:checked").each(function (index) {
                        let cartId = $(this).attr("class").split("-")[1];
                        cartIdArray.push(cartId);
                    })
                }
                totalPriceItems = 0
                $(".cart-total").text("$0");
                $(".selected-items").text("$0")
                $(".total_qty_items").text(parseInt($(".total_qty_items").text()) - cartIdArray.length);

                console.log(cartIdArray)

                $.each(cartIdArray, function (key, val) {
                    $(".cart_item-" + val).remove();
                })

                $.ajax({
                    url: "{{route("cart.ajax.selectedDel")}}",
                    method: "DELETE",
                    data: {
                        array: cartIdArray
                    },
                    success: function (res) {
                        console.log(res)
                    }
                })
            }
        }


        function updateQty(id) {

            setTimeout(function () {
                $(".user_input_qty-" + id).prop('readonly', true)
            }, 100)

            let userInputQty = $(".user_input_qty-" + id).val();
            let qtyEl = $(".user_input_qty-" + id);

            if (userInputQty < 0) {
                $(".user_input_qty-" + id).val("1");
            }

            if (Number.isNaN(parseInt(qtyEl.val()))) {
                $(".user_input_qty-" + id).val("1");
            }

            qtyEl.on("input", function () {
                if (/^0/.test(this.value)) {
                    this.value = this.value.replace(/^0/, "")
                }
            })

            $.ajax({
                url: "{{route("cart.ajax.updateQty")}}",
                method: "POST",
                data: $("#frm-" + id).serialize(),
                success: function (res) {
                    console.log(res)
                    let number = res["qty"];
                    if (res["msg"] === "") {
                        // debugger
                        let cartItemTotalPrice = res["inputQty"] * res["price"];
                        $(".total-price-" + id).text("$" + cartItemTotalPrice)
                        $(".quantity-annouce-" + id).text("");
                        $(".qty-" + id).val(res["inputQty"]);
                        $(".product-quantity").val(res["qty"]);
                        $(".price-" + id).val(res["price"]);
                        $(".cart_item_checkbox-" + id).data("quantitycartitem", res["inputQty"]);

                        totalPriceItems = 0;
                        $(".user_input_qty").each(function (index) {
                            let isSelected = $(".cart_item").eq(index).prop("checked");

                            if (isSelected) {
                                let quantityItem = $(".qty").eq(index).val();
                                let PriceItem = $(".price").eq(index).val();

                                console.log(quantityItem)
                                console.log(PriceItem)
                                totalPriceItems += quantityItem * PriceItem;
                                $(".cart-total").text("$" + totalPriceItems);
                            }

                        })
                    } else if (res["msg"] === "Your quantity is invalid") {
                        $(".quantity-annouce-" + id).text("Your input is invalid value!. Please try again.")
                    } else if (res["msg"] === "Your quantity is out of bound") {
                        if (res["inputQty"] <= 0) {
                            $(".user_input_qty-" + id).val(1);
                            $(".quantity-annouce-" + id).text("Quantity can not be zero.")
                        } else if (res["inputQty"] > res["qty"]) {

                            $(".quantity-annouce-" + id).text("There are only " + res["qty"] + " in store");

                            $(".total-price-" + id).text("$" + (number * res["price"]));
                            $(".user_input_qty-" + id).val(number);

                            $(".qty-" + id).val(number)

                            totalPriceItems = 0;
                            $(".user_input_qty").each(function (index) {
                                let isSelected = $(".cart_item").eq(index).prop("checked");

                                if (isSelected) {
                                    let quantityItem = $(".qty").eq(index).val();
                                    let PriceItem = $(".price").eq(index).val();

                                    console.log(quantityItem)
                                    console.log(PriceItem)
                                    totalPriceItems += quantityItem * PriceItem;
                                    $(".cart-total").text("$" + totalPriceItems);
                                }

                            })
                        }
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
            // debugger;
            console.log($(".cart_item").length)
            if ($(this).is(':checked')) {
                totalPriceItems = 0;
                $.each($('.cart_item'), function (index) {
                    $(this).prop('checked', true);

                    let userInputQty = $(".qty").eq(index).val();
                    let cartItemPrice = $(".price").eq(index).val();

                    totalPriceItems += userInputQty * cartItemPrice;
                    $(".cart-total").text("$" + totalPriceItems);

                    let totalSelectedItems = $(".cart_item:checked").length;
                    $(".selected-items").text(totalSelectedItems);
                });
            } else {
                $.each($('.cart_item'), function () {
                    $(this).prop('checked', false);
                    totalPriceItems = 0;
                    $(".cart-total").text("$" + 0);

                    let totalSelectedItems = $(".cart_item:checked").length;
                    $(".selected-items").text(totalSelectedItems);
                });
            }
        });

        /*Reset total price items*/
        totalPriceItems = 0;

        /*Select and calculate selected cart items*/
        function selectedCrd(id) {

            // debugger
            let isSelected = $(".cart_item_checkbox-" + id).prop("checked");

            let userInputQty = $(".qty-" + id).val();
            let cartItemPrice = $(".price-" + id).val();

            if (isSelected) {
                totalPriceItems += userInputQty * cartItemPrice;
                $(".cart-total").text("$" + totalPriceItems);

            } else {
                totalPriceItems -= userInputQty * cartItemPrice;
                $(".cart-total").text("$" + totalPriceItems);
                $('[name="all_cart_items"]').prop("checked", false);
            }

            let checkedItems = $(".cart_item:checked").length;
            let items = $(".cart_item").length;

            if (checkedItems - items === 0) {
                $('[name="all_cart_items"]').prop("checked", true);
            }

            let totalSelectedItems = $(".cart_item:checked").length;
            $(".selected-items").text(totalSelectedItems);

        }


        $(".btn-process").click(function () {

            let request = [];

            $(".cart_item:checked").each(function (index) {
                // console.log(index)
                let clSelectedCartItem = $(this).attr("class");
                let cartId = clSelectedCartItem.split("-")[1];
                let productId = $(".product-" + cartId).val();
                let qtyItem = $(".user_input_qty-" + cartId).val();
                let creatorId = $(".creator-" + cartId).val();

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

            if (request.length > 0) {
                window.location.href = "{{route("cart.checkout.index")}}";
                $(".announce_quantity_result").text("");
                $.ajax({
                    url: "{{route("cart.ordersDetails.ajax.add")}}",
                    method: "POST",
                    dataType: "json",
                    data: {
                        request
                    },
                    success: function (res) {
                        console.log(res)

                    }
                })
            } else if (request.length >= 0) {
                $(".announce_quantity_result").text("You have not chosen any products to order yet !!");
            }
        })

        $(".btn-back").click(function () {

            let request = [];

            $(".cart_item").each(function (index) {
                let clSelectedCartItem = $(this).attr("class");
                let cartId = clSelectedCartItem.split("-")[1];
                let productId = $(".product-" + cartId).val();
                let qtyItem = $(".user_input_qty-" + cartId).val();
                let creatorId = $(".creator-" + cartId).val();

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

            window.location.href = "{{route("users.products.index")}}";
            $.ajax({
                url: "{{route("cart.ordersDetails.ajax.add")}}",
                method: "POST",
                dataType: "json",
                data: {
                    request
                },
                success: function (res) {

                }
            })
        })

        $(".nav-home").click(function () {

            let request = [];

            $(".cart_item").each(function (index) {
                let clSelectedCartItem = $(this).attr("class");
                let cartId = clSelectedCartItem.split("-")[1];
                let productId = $(".product-" + cartId).val();
                let qtyItem = $(".user_input_qty-" + cartId).val();
                let creatorId = $(".creator-" + cartId).val();

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

            window.location.href = "{{route("users.products.index")}}";
            $.ajax({
                url: "{{route("cart.ordersDetails.ajax.add")}}",
                method: "POST",
                dataType: "json",
                data: {
                    request
                },
                success: function (res) {

                }
            })
        })

        function emptyCrdItems() {
            $(".container-wrapper")
                .html("<h4>No Product(s) In Your Cart</h4><br>\n" +
                    "                    <a href=\"/\" class=\"btn btn-dark\">Continue Shopping</a>");
        }


    </script>
@endsection
