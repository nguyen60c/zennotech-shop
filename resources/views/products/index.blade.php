@extends("layouts.app-master")
@section('title', 'Home')
@section('content')
    <br>

    <h1>Store</h1>

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
                        <img class="card-img-top" style="width: 200px; height: 200px"
                             src="{{ asset('images/products/' . $product->image) }}" alt="{{ $product->image_path }}">
                        <div class="card-body" style="width: 250px;">
                            <h4 class="card-text" style="margin-bottom: 5px;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                        overflow: hidden;">
                                {{ $product->name }}</h4>
                            <h5 class="text-danger">${{ number_format($product->price) }}</h5>
                            {{--                            <form action="{{ route('cart.store') }}" method="post">--}}
                            <form>
                                {{ csrf_field() }}
                                <input type="hidden" value="{{ $product->id }}" id="id" name="id">
                                <input type="hidden" value="{{ $product->name }}" id="name" name="name">
                                <input type="hidden" value="{{ $product->price }}" id="price" name="price">
                                <input type="hidden" value="{{ $product->image }}"
                                       id="img" name="img">
                                <input type="hidden" value="{{ $product->slug }}" id="slug" name="slug">
                                <input type="hidden" value="1" id="quantity" name="quantity">

                                <div class="card-footer" style="background-color: white;">
                                    <div class="row">
                                        <button class="btn btn-secondary btn-sm"
                                                class="tooltip-test" title="add to cart">
                                            <i class="fa fa-shopping-cart"></i> add to cart
                                        </button>
                                    </div>

                                    <div class="row" style="padding-top: 5px">
                                        <a class="btn btn-warning btn-sm" href="#" style="font-weight: 700">
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

@endsection


@section('script')

    <script>

    </script>

@endsection
