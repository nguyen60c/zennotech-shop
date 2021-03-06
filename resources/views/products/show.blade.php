@extends("layouts.app-master")
@section('title', 'Home')
@section('content')
    <br>

    <h1>Result</h1>

    @if ($products->count() == 0)
        <tr>
            <td colspan="5">
                <h4 class="text-danger">Your product is not available in our store. Sorry My Bae.</h4></td>
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
                        <img class="card-img-top" style="width: 150px; height: 150px"
                             src="{{ asset('images/products/' . $product->image) }}" alt="{{ $product->image }}">
                        <div class="card-body" style="width: 250px;">
                            <h4 class="card-text" style="margin-bottom: 5px;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                        overflow: hidden;">
                                {{ $product->name }}</h4>
                            <h5 class="text-danger">${{ number_format($product->price) }}</h5>
                            <form action="{{ route('cart.store',$product->id) }}" method="post">
                                {{ csrf_field() }}

                                <div class="card-footer" style="background-color: white;">
                                    <div class="row">
                                        <button class="btn btn-secondary btn-sm"
                                                class="tooltip-test" title="add to cart">
                                            <i class="fa fa-shopping-cart"></i> add to cart
                                        </button>
                                    </div>

                                    <div class="row" style="padding-top: 5px">
                                        <a class="btn btn-warning btn-sm" href="{{route("users.products.details",$product->id)}}" style="font-weight: 700">
                                            More infor
                                        </a>
                                    </div>
                                </div>
                            </form>
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
