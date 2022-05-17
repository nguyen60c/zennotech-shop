@extends("admin.layouts.app")
@section("title","Orders")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="body flex-grow-1" style="padding-right: 5rem; margin-top: 40px">
            <div class="container p-4" style="border-radius: 10px;">
                <h4>Date: {{date('d-m-Y', strtotime($dateOrderDetailsItems))}}</h4>
                <h4>Time: {{date('h-m-s', strtotime($dateOrderDetailsItems))}}</h4>
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

                    @foreach($orderDetailsItem as $item)

                        <tr style="margin: auto;">

                            <td class="align-middle">{{$item["name"]}}</td>
                            <td class="align-middle">{{$item["quantity"]}}</td>
                            <td class="align-middle">${{$item["total_price"]}}</td>

                        </tr>
                    @endforeach

                    </tbody>
                </table>

                <form method="post" action="{{route("admin.orders.update")}}">
                    @csrf
                    <input type="hidden" name="status" class="input_status"
                           value="{{$orderDetailsItemsStatus}}"/>
                    <input type="hidden" name="user_id" class="input_user_id"
                           value="{{$userId}}"/>
                    <input type="hidden" name="time" class="input_user_time"
                           value="{{$time}}"/>
                    <button class="btn btn-primary" type="submit"><strong>Save</strong></button>
                    <a href="{{ route('admin.orders.history') }}" class="btn btn-default">Back</a>

                </form>
            </div>
        </div>

        @if(session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="card"
                 style="width: 16rem; position: fixed;bottom: 1rem;right: 1rem;padding: 5px; background-color: #d1e7dd">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @endsection

        @section("style")

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

            </script>
@endsection
