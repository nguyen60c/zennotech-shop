<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="index.blade.php">
            <span class="align-middle">{{auth()->user()->name}}</span>
        </a>

        <ul class="sidebar-nav">

            <li class="sidebar-item active">
                <a class="sidebar-link" href="{{route("admin.dashboard")}}">
                    <i class="fa-solid fa-chalkboard"></i> <span
                        class="align-middle">Dashboard</span>
                </a>
            </li>

            <li class="sidebar-item active">
                <a class="sidebar-link" href="{{route("admin.users.index")}}">
                    <i class="fa-solid fa-users"></i> <span class="align-middle">Users</span>
                </a>
            </li>

            <li class="sidebar-item active">
                <a class="sidebar-link" href="{{route("permissions.index")}}">
                    <i class="fa-solid fa-user-shield"></i>
                    <span class="align-middle">Permissions</span>
                </a>
            </li>

            <li class="sidebar-item active">
                <a class="sidebar-link" href="{{route("roles.index")}}">
                    <i class="fa-solid fa-user-lock"></i>
                    <span class="align-middle">Roles</span>
                </a>
            </li>

            <li class="sidebar-item active">
                {{--                <a class="sidebar-link" href="{{route("orders.index")}}">--}}
                <i class="fa-solid fa-border-all"></i>
                <span class="align-middle">Customer Orders</span>
                {{--                </a>--}}
            </li>

            <li class="sidebar-item active">
                <a class="sidebar-link" href="{{route("admin.products.index")}}">
                    <i class="fa-solid fa-cubes"></i>
                    <span class="align-middle">Products</span>
                </a>
            </li>

            <li class="sidebar-item active">
                <a class="sidebar-link" href="{{route("logout.perform")}}">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span class="align-middle">Logout</span>
                </a>
            </li>


        </ul>
    </div>
</nav>
