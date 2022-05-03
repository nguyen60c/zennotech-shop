@extends("admin.layouts.app")
@section("title","Roles")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="bg-light p-4 rounded">
            <h1>{{ ucfirst($role->name) }} Role</h1>
            <div class="lead">

            </div>

            <div class="container mt-4">

                <h3>Assigned permissions</h3>

                <table class="table table-striped">
                    <thead>
                    <th scope="col" width="20%">Name</th>
                    <th scope="col" width="1%">Guard</th>
                    </thead>
                    @if(count($rolePermissions) > 0)
                        @foreach($rolePermissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->guard_name }}</td>
                            </tr>
                        @endforeach
                    @else
                        <p class="text-danger border-4"
                           style="font-weight: 700">You have not added permission yet</p>
                    @endif
                </table>
            </div>

        </div>
        <div class="mt-4">
            @if(count($rolePermissions) > 0)
            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-info">Edit</a>
            @endif
            <a href="{{ route('roles.index') }}" class="btn btn-default">Back</a>
        </div>
    </div>

@endsection
