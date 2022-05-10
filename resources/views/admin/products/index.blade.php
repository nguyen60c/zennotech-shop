@extends("admin.layouts.app")
@section("title","Products")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="bg-light p-4 rounded">
            <h2>Products</h2>
            <div class="lead">
                Manage your products here.
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm float-right">Add product</a>
            </div>

            <div class="mt-2">
                @include('admin.layouts.partials.messages')
            </div>

            <table class="table table-bordered">
                <tr>
                    <th width="1%">No</th>
                    <th>Name</th>
                    <th>Created by</th>
                    <th>Time</th>
                    <th width="3%" colspan="3">Actions</th>
                </tr>

                {{--To sign order number in list--}}
                <?php $page = $_GET["page"];

                if($page == 1){
                    $page = 0;
                } else{
                    $page = 8;
                }

                ?>
                @foreach ($products as $key => $product)
                    <tr>
                        <td>{{ ++$key + $page }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{$product->creator_name}}</td>
                        <td>{{$product->created_at}}</td>
                        <td>
                            <a class="btn btn-info btn-sm" href="{{ route('admin.products.show', $product->id) }}">Show</a>
                        </td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="{{ route('admin.products.edit', $product->id) }}">Edit</a>
                        </td>
                        <td>
                            {!! Form::open(['method' => 'DELETE','route' => ['admin.products.destroy', $product->id],'style'=>'display:inline']) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                            {!! Form::close() !!}

                        </td>
                    </tr>
                @endforeach
            </table>

            <div class="d-flex">
                {!! $products->links() !!}
            </div>

        </div>
    </div>

@endsection
