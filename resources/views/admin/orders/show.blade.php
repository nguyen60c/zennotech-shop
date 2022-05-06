@extends("admin.layouts.app")
@section("title","Orders")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="body flex-grow-1">
            <h1>Orders</h1>
            <form method="post" action="{{route("admin.orders.update")}}">
                @csrf
                <div class="bg-light p-4 rounded mt-5">
                    <div class="container ">
                        <div class="row">
                            <div class="col">
                                <img class="product-img"
                                     src="{{asset("images/products/".$data["image"])}}
                                    " style="width: 200px;height: 200px">
                            </div>

                            <div class="col-9">
                                <div><strong>Name: </strong>
                                    <input class="product-name border-0 transparent-input"
                                           type="text"
                                           readonly value="{{$data["name"]}}">
                                </div>
                                <div><strong>Details: </strong>
                                    <input class="product-original border-0 transparent-input"
                                           type="text"
                                           readonly
                                           value="{{$data["details"]}}"></div>
                                <div>
                                    <strong>Price: </strong>
                                    <input class="product-price border-0 transparent-input"
                                           type="text"
                                           readonly value="{{$data["price"]}}"></div>
                                <div>
                                    <strong>Description: </strong>
                                    <input class="product-desc border-0 transparent-input"
                                           type="text" readonly value="{{$data["description"]}}"></div>


                                <div>
                                    <strong>Quantity: </strong>
                                    <input class="product-desc border-0 transparent-input"
                                           type="text" readonly
                                           value="{{$data["quantity"]}}">
                                </div>

                                <div>
                                    <strong>Date: </strong>
                                    <input class="product-desc border-0 transparent-input"
                                           type="text" readonly
                                           value="{{ date('h:i:s A', strtotime($data["created_at"])) }}">
                                </div>

                                <div>
                                    <strong>Ordered by: </strong>
                                    <input class="product-desc border-0 transparent-input"
                                           type="text" readonly
                                           value="{{$data["username"]}}">
                                </div>

                                <div>
                                    <strong>Status: </strong>
                                    <input class="input_status border-0 transparent-input"
                                           type="hidden" readonly
                                           value="">

                                    <?php
                                    $color = "";
                                    switch ($data["status"]) {
                                        case "Processing":
                                            $color = "bg-primary";
                                            break;
                                        case "Shipping":
                                            $color = "bg-warning";
                                            break;
                                        case "Cancel":
                                            $color = "bg-danger";
                                            break;
                                    }
                                    ?>
                                    <select name="status"
                                            class="form-control text-light select_status {{$color}}"
                                            style="font-weight: 700; width: 150px !important;">

                                        <option {{$data["status"] == "Processing" ? "selected" : ""}}
                                                value="Processing" style="background: #a0aec0">
                                            Processing
                                        </option>

                                        <option {{$data["status"] == "Shipping" ? "selected" : ""}}
                                                value="Shipping">
                                            Shipping
                                        </option>

                                        <option {{$data["status"] == "Cancel" ? "selected" : ""}}
                                                value="Cancel">
                                            Cancel
                                        </option>

                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="order_details_id"
                           value="{{$data["order_details_id"]}}"/>
                    <input type="hidden" name="status" class="input_status"
                           value="{{$data["status"]}}"/>
                    <input type="hidden" name="user_id" class="input_user_id"
                           value="{{$data["user_id"]}}"/>
                    <button class="btn btn-primary" type="submit"><strong>Save</strong></button>
                    <a href="{{ route('admin.orders.index',$data["user_id"]) }}" class="btn btn-default">Back</a>
                </div>
            </form>
        </div>

        @endsection

        @section("script")
            <script>

                $(document).ready(function () {

                    let selected_status = $(".select_status");
                    let input_status = $(".input_status");
                    let input_user_id = $(".input_user_id");
                    let input_order_details_id = $(".input_order_details_id");
                    let save_button = $(".save-btn")

                    var url = window.location.href;
                    var id = url.split("/");
                    var user_id = id[4];
                    var order_details_id = 0;
                    var status_order_details = "";
                    var order_detils_update_array = [];

                    $(selected_status).each(function (index) {
                        $(this).change(function () {
                            var optionSelected = $(this).find("option:selected");
                            var valueSelected = optionSelected.val();
                            input_status.val(valueSelected)

                            switch (valueSelected) {
                                case "Processing":
                                    $(this).removeClass("bg-warning");
                                    $(this).removeClass("bg-danger");
                                    $(this).addClass("bg-primary");
                                    break;
                                case "Shipping":
                                    $(this).removeClass("bg-primary");
                                    $(this).removeClass("bg-danger");
                                    $(this).addClass("bg-warning");
                                    break;
                                case "Cancel":
                                    $(this).removeClass("bg-primary");
                                    $(this).removeClass("bg-warning");
                                    $(this).addClass("bg-danger");
                                    break;
                            }
                        });
                    });

                    $(save_button).click(function (e) {
                        if (confirm("are you sure to save it?")) {
                            if (order_detils_update_array.length > 0) {
                                $(".test_thu").val(order_detils_update_array);
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN':
                                            $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                $.ajax({
                                    url: 'http://127.0.0.1:8000/orders/updateId',
                                    type: "json",
                                    method: "POST",
                                    data: order_detils_update_array,
                                    success: function (response) { // What to do if we succeed
                                        // alert("Updated Successful!");
                                        console.log("ok");
                                    },
                                    error: function (response) {
                                        alert("Updated Failed!");
                                    }
                                });

                            } else {
                                alert("You have not changed anything!")
                            }
                        }
                    });

                })

            </script>
@endsection
