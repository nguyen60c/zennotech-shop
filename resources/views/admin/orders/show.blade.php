@extends("admin.layouts.app")
@section("title","Orders")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="body flex-grow-1">
            <h1>Orders</h1>


            <div class="container bg-secondary bg-opacity-10 p-4" style="border-radius: 10px;">

                <div style="display: flex;justify-content: flex-start;">
                    <h4 style="margin-right: 20px">Date: {{date('d-m-Y', strtotime($dateOrderDetailsItems))}}</h4>
                    <h4 style="margin-right: 20px">Time: {{date('H:m:s', strtotime($dateOrderDetailsItems))}}</h4>

                    <div>

                        @role("seller")
                        <input class="input_status border-0 transparent-input"
                               type="hidden" readonly
                               value="">

                        <?php
                        $color = "";
                        switch ($orderDetailsItemsStatus) {
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

                            <option {{$orderDetailsItemsStatus == "Processing" ? "selected" : ""}}
                                    value="Processing" style="background: #a0aec0">
                                Processing
                            </option>

                            <option {{$orderDetailsItemsStatus == "Shipping" ? "selected" : ""}}
                                    value="Shipping">
                                Shipping
                            </option>

                            <option {{$orderDetailsItemsStatus == "Cancel" ? "selected" : ""}}
                                    value="Cancel">
                                Cancel
                            </option>

                        </select>
                        @endrole

                        @role("admin")
                        <?php
                        $color = "";
                        switch ($orderDetailsItemsStatus) {
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

                            <option {{$orderDetailsItemsStatus}}
                                    value="Processing" style="background: #a0aec0">
                                Processing
                            </option>

                        </select>
                        @endrole

                    </div>
                </div>

                <div>
                    <h4>Customer: {{$customerName}}</h4>
                    <h4>Address: {{$customerAddress}}</h4>
                    <h4>Phone: {{$customerPhone}}</h4>
                </div>


                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col">Image</th>
                        <th scope="col">Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Total</th>
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

                            <td class="align-middle">{{$item["name"]}}</td>
                            <td class="align-middle"><img src="{{ asset('images/products/' . $item['image']) }}" class="img-thumbnail" width="100" height="100"></td>
                            <td class="align-middle">{{$item["price"]}}</td>
                            <td class="align-middle">{{$item["quantity"]}}</td>
                            
                            <?php $total = 0;
                            $total = $item["total_price"] ?>

                            <td class="align-middle">${{$item["total_price"]}}</td>
                            <td class="align-middle">
                                <a class="btn btn-warning" href="{{route("users.products.details",$item["product_id"])}}">
                                    More infor
                                </a></td>

                        </tr>
                    @endforeach

                    </tbody>
                    <strong>Totals Price: ${{$total}}</strong>
                </table>
            </div>
            <form method="post" action="{{route("admin.orders.update")}}">
                @csrf
                <input type="hidden" name="status" class="input_status"
                       value="{{$orderDetailsItemsStatus}}"/>
                <input type="hidden" name="user_id" class="input_user_id"
                       value="{{$userId}}"/>
                <input type="hidden" name="time" class="input_user_time"
                       value="{{$time}}"/>
                @role("seller")
                <button class="btn btn-primary" type="submit"><strong>Save</strong></button>
                @endrole
                <a href="{{ route('admin.orders.index',$userId) }}" class="btn btn-default">Back</a>

            </form>

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
                            // if (confirm("are you sure to save it?")) {
                            //     if (order_detils_update_array.length > 0) {
                            $(".test_thu").val(order_detils_update_array);
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN':
                                        $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                url: '{{url("orders/updateId")}}',
                                dataType: "json",
                                method: "POST",
                                data: order_detils_update_array,
                                success: function (response) { // What to do if we succeed

                                    {{--window.location.href = "{{route("admin.orders.index", $userId)}}";--}}
                                },
                                error: function (response) {
                                    alert("Updated Failed!");
                                }
                            })
                        })
                    })


                        // });

                    // })

                </script>
@endsection
