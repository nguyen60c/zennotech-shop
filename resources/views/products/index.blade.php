@extends("layouts.app-master")
@section('title', 'Home')
@section('content')
    <br>

    <h1>Store</h1>

    <h3 class="text-annouce text-info"></h3>

    @if ($products->count() == 0)
        <tr>
            <td colspan="5">No products to display.</td>
        </tr>
    @endif

    <?php $count = 0; ?>

    @foreach ($products as $product)
        @if ($count % 3 == 0)
            <div class="row">
                @endif

                <div class="col-md-4 mt-5" style="border-radius: 10px">
                    <div class="card mb-4" style="align-items: center;
                                    padding: 18px;border: none !important;">
                        <a
                            href="{{route("users.products.details",$product->id)}}"
                            style="font-weight: 700">
                            <img class="card-img-top" style="width: 150px; height: 150px"
                                 src="{{ asset('images/products/' . $product->image) }}" alt="{{ $product->image }}">
                        </a>
                        <div class="card-body" style="width: 250px;">
                            <h4 class="card-text" style="margin-bottom: 5px;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                        overflow: hidden;">
                                {{ $product->name }}</h4>
                            <h5 class="text-danger">${{ number_format($product->price) }}</h5>
                            {{ csrf_field() }}
                            <input type="hidden" class="product_id" value="{{$product->id}}">

                            <div class="card-footer" style="background-color: white;">
                                <div class="row">
                                    @auth
                                        <button class="btn btn-secondary btn-sm add-to-cart"
                                                class="tooltip-test" title="add to cart">
                                            <i class="fa fa-shopping-cart"></i> add to cart
                                        </button>
                                    @endauth

                                    @guest
                                        <a class="btn btn-secondary btn-sm add-to-cart"
                                           href="{{route("login.show")}}"
                                           class="tooltip-test" title="add to cart">
                                            <i class="fa fa-shopping-cart"></i> add to cart
                                        </a>
                                    @endguest
                                </div>

                                <div class="row" style="padding-top: 5px">
                                    <a class="btn btn-warning btn-sm"
                                       href="{{route("users.products.details",$product->id)}}"
                                       style="font-weight: 700">
                                        More infor
                                    </a>
                                </div>
                            </div>
                            {{--                            </form>--}}
                        </div>
                    </div>
                </div>

                @if ($count % 3 == 2)
            </div>
        @endif

        <?php $count++; ?>
    @endforeach

    {{$products->links("vendor.pagination.paginate-customer")}}

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
                                console.log(res)
                                $(".badge-cart_item").text(res[1]);
                                $(".text-annouce").text("Successful adding to cart")
                            }
                        }
                    })

                });
            })


        })

    </script>

@endsection
