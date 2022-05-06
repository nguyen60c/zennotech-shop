@extends("admin.layouts.app")
@section("title","Products")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="bg-light p-4 rounded">
            <h2>Product</h2>

            <div class="container mt-4">

                <form method="post" action="{{ route('admin.products.update', $product->id) }}">
                    @method('patch')
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Name</label>
                        <input value="{{ $product->name }}"
                               type="text"
                               class="form-control"
                               name="name"
                               readonly
                               placeholder="name" required>

                        @if ($errors->has('name'))
                            <span class="text-danger text-left">{{ $errors->first('name') }}</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input value="{{ $product->description }}"
                               type="text"
                               class="form-control"
                               name="description"
                               readonly
                               placeholder="Description" required>

                        @if ($errors->has('description'))
                            <span class="text-danger text-left">{{ $errors->first('description') }}</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Details</label>
                        <input value="{{ $product->details }}"
                               type="text"
                               class="form-control"
                               name="description"
                               readonly
                               placeholder="Description" required>

                        @if ($errors->has('details'))
                            <span class="text-danger text-left">
                                {{ $errors->first('details') }}
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Price</label>
                        <input value="{{ $product->price }}"
                               type="number"
                               class="form-control"
                               min="1"
                               name="price"
                               readonly
                               placeholder="price" required>

                        @if ($errors->has('quantity'))
                            <span class="text-danger text-left">
                                {{ $errors->first('quantity') }}
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input value="{{ $product->quantity }}"
                               type="number"
                               min="1"
                               max="100"
                               class="form-control"
                               name="quantity"
                               readonly
                               placeholder="quantity" required>

                        @if ($errors->has('quantity'))
                            <span class="text-danger text-left">
                                {{ $errors->first('quantity') }}
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Photo</label>
                        <img src="{{asset("images/products/".$product->image)}}"
                             style="width: 200px;height: 200px;">
                    </div>

                    <a href="{{ route('admin.products.index') }}" class="btn btn-default">Back</a>
                </form>
            </div>

        </div>
        </div>


@endsection


