@extends("admin.layouts.app")
@section("title","Create Products")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="bg-light p-4 rounded">
            <h2>Add new product</h2>
            <div class="lead">
                Add new product.
            </div>

            <div class="container mt-4">

                <form method="POST" action="{{ route('admin.products.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Name</label>
                        <input value="{{ old('name') }}"
                               type="text"
                               class="form-control"
                               name="name"
                               placeholder="Name" required>

                        @if ($errors->has('name'))
                            <span class="text-danger text-left">{{ $errors->first('name') }}</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input value="{{ old('description') }}"
                               type="text"
                               class="form-control"
                               name="description"
                               placeholder="Description" required>

                        @if ($errors->has('description'))
                            <span class="text-danger text-left">{{ $errors->first('description') }}</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="details" class="form-label">Details</label>
                        <input value="{{ old('details') }}"
                               type="text"
                               class="form-control"
                               name="description"
                               placeholder="Details" required>

                        @if ($errors->has('details'))
                            <span class="text-danger text-left">
                                {{ $errors->first('details') }}</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input value="{{ old('price') }}"
                               type="number"
                               min="1"
                               class="form-control"
                               name="description"
                               placeholder="Price" required>

                        @if ($errors->has('price'))
                            <span class="text-danger text-left">
                                {{ $errors->first('price') }}</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input value="{{ old('quantity') }}"
                               type="number"
                               min="1"
                               class="form-control"
                               name="quantity"
                               placeholder="Quantity" required>

                        @if ($errors->has('quantity'))
                            <span class="text-danger text-left">
                                {{ $errors->first('quantity') }}</span>
                        @endif
                    </div>


                    <button type="submit" class="btn btn-primary">Save product</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-default">Back</a>
                </form>
            </div>

        </div>
    </div>

@endsection
