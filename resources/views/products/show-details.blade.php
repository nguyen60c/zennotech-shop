@extends("layouts.app-master")
@section('title', 'Home')
@section('content')

    <h1 style="margin-top: 100px;margin-bottom: 40px">{{$productItem["name"]}}</h1>
    <div style="display: flex">
        <div style="margin-right: 30px">

        <img class="card-img-top" style="width: 150px; height: 150px"
             src="{{ asset('images/products/' . $productItem["image"]) }}" alt="{{ $productItem["image"] }}">

        </div>

        <div>
            <div>
                <span>Price: </span>
            <h5 class="text-danger">${{ number_format($productItem["price"]) }}</h5>
                <input type="hidden" class="product_id" value="{{$productItem["id"]}}"/>
            <span>Details: </span>
            <h5 class="text-secondary">{{ $productItem["details"] }}</h5>
            <span>Description: </span>
            <h5 class="text-secondary">{{ $productItem["description"] }}</h5>
            <span>Quantity: </span>
            <h5 class="text-secondary">{{$productItem["quantity"]}}</h5>
            <span>By: </span>
            <h5 class="text-secondary">{{ $creator  }}</h5>
            </div>

        </div>
    </div>
    <?php $Page = $previousPage !== "" ? "?page=" . $previousPage : "" ?>
    <a class="btn btn-primary" href="http://127.0.0.1:8000/{{$Page}}">Back</a>
        @auth
            <button type="button" class="btn btn-warning btn-sm add-to-cart"
                    id="liveToastBtn"
                    class="tooltip-test" title="add to cart">
                <i class="fa fa-shopping-cart"></i> add to cart
            </button>
            <h4 class="text-annouce" style="display: inline-block"></h4>
        @endauth

        @guest
            <a class="btn btn-warning btn-sm add-to-cart"
               href="{{route("login.show")}}"
               class="tooltip-test" title="add to cart">
                <i class="fa fa-shopping-cart"></i> add to cart
            </a>
        @endguest

@endsection


@section("script")

    <script>

        $(document).ready(function () {

            $(".add-to-cart").each(function (index) {
                $(this).click(function () {
                    var productId = $(".product_id").eq(index).val();

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: "{{url("cart/cart-item")}}",
                        method: "POST",
                        dataType: "json",
                        data: {
                            productId
                        },
                        success: function (res) {
                            console.log(res)
                            if (res[0] === 0) {
                                $(".text-annouce").text(res[2]);
                            }
                        }
                    })

                });
            })


        })

    </script>

@endsection
