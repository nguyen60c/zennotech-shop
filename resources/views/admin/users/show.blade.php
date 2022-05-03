@extends("admin.layouts.app")
@section("title","Show User")

@section('content')
    <div class="main">
        @include("admin.layouts.partials.menu-navbar-toggle")
        <div class="bg-light p-4 rounded">
            <h1>Show user</h1>
            <div class="container mt-4">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input value="{{ $user->name }}"
                           type="text"
                           class="form-control"
                           name="name"
                           readonly
                           placeholder="Name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input value="{{ $user->email }}"
                           type="email"
                           class="form-control"
                           name="email"
                           readonly
                           placeholder="Email address" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input value="{{ $user->username }}"
                           type="text"
                           class="form-control"
                           name="username"
                           readonly
                           placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <input value="{{ $user->roles[0]["name"] }}"
                           type="text"
                           class="form-control"
                           name="username"
                           readonly
                           placeholder="Username" required>
                </div>
            </div>

        </div>
        <div class="mt-4">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-info">Edit</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-default">Back</a>
        </div>
    </div>

@endsection
