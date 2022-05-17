<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="index.blade.php">
            <span class="align-middle">{{auth()->user()->name}}</span>
        </a>

        <ul class="sidebar-nav">

            @hasanyrole("admin|seller")
            <li class="sidebar-item active">
                <a class="sidebar-link" href="{{route("admin.dashboard")}}">
                    <i class="fa-solid fa-chalkboard"></i> <span
                        class="align-middle">Dashboard</span>
                </a>
            </li>
            @endhasanyrole

            @role("admin")

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

            @endrole

            @hasanyrole("admin|seller")

            @role("seller")
            <li class="sidebar-item active">
                <a class="sidebar-link" href="{{route("admin.products.index")}}">
                    <i class="fa-solid fa-cubes"></i>
                    <span class="align-middle">Products</span>
                </a>
            </li>
            @endrole


            <li class="sidebar-item active">
                <a class="sidebar-link" href="{{route("admin.orders.history")}}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span class="align-middle">Recently Orders</span>
                </a>
            </li>

            <li class="sidebar-item active">
                <a class="sidebar-link" href="{{route("users.products.index")}}">
                    <i class="fa-solid fa-person-military-pointing"></i>
                    <span class="align-middle">Be a customer</span>
                </a>
            </li>

            @endhasanyrole

            <li class="sidebar-item active">
                <a class="sidebar-link" href="{{route("logout.perform")}}">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span class="align-middle">Logout</span>
                </a>
            </li>


        </ul>
    </div>
</nav>
