@extends("admin.layouts.app")
@section("title","Products")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="bg-light p-4 rounded">
            <h2>Update product</h2>
            <div class="lead">
                Edit product.
            </div>

            <div class="container mt-4">

                <form method="POST" action="{{ route('admin.products.update', $product->id) }}">
                    @method('patch')
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Name</label>
                        <input value="{{ $product->name }}"
                               type="text"
                               class="form-control"
                               name="name"
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
                               placeholder="quantity" required>

                        @if ($errors->has('quantity'))
                            <span class="text-danger text-left">
                                {{ $errors->first('quantity') }}
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-default">Back</a>
                </form>
            </div>

        </div>

    </div>

@endsection
