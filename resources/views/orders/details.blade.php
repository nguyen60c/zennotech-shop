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
    <a class="btn btn-primary" href="{{$previousUrl}}">Back</a>

@endsection


@section("script")


@endsection
