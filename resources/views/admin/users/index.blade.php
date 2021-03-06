@extends("admin.layouts.app")
@section("title","Create Products")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="bg-light p-4 rounded">
            <h1>Users</h1>
            <div class="lead">
                Manage your users here.
                            <a href="{{ route('admin.users.create') }}"
                               class="btn btn-primary btn-sm float-right">Add new user</a>
            </div>

            <div class="mt-2">
                @include('admin.layouts.partials.messages')
            </div>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col" width="1%">#</th>
                    <th scope="col" width="15%">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col" width="10%">Username</th>
                    <th scope="col" width="10%">Roles</th>
                    <th scope="col" width="1%" colspan="4" class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                <div style="display: none">
                    {{$key = 0}}
                </div>
                @foreach($users as $user)

                    <tr>
                        <th scope="row">{{ ++$key }}</th>
                        <td>{{ $user->name ?? "NoName" }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->username }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                                <?php $user_cur = $role->name; ?>
                            @endforeach
                        </td>

                        <td>
                            <a href="{{ route('admin.orders.index', $user->id) }}"
                               class="btn btn-info btn-sm">Orders</a>
                        </td>

                        <td><a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-warning btn-sm">Show</a>
                        </td>
                        <td><a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-info btn-sm">Edit</a>
                        </td>
                        <td>
                            {!! Form::open(['method' => 'DELETE',
                            'route' => ['admin.users.destroy', $user->id],
                            'style'=>'display:inline']) !!}
                            {!! Form::submit('Delete',
                            ['class' => 'btn btn-danger btn-sm']) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="d-flex">
                {!! $users->links() !!}
            </div>

        </div>
    </div>

@endsection
