<header class="p-3 bg-dark text-white">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap">
                    <use xlink:href="#bootstrap"/>
                </svg>
            </a>

            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <li><a href="{{ route('users.products.index') }}"
                       class="nav-link px-2 text-white">Home</a></li>
                @auth
                    <li><a href="{{ route('users.order.index') }}"
                           class="nav-link px-2 text-white">Your Order</a></li>
                @endauth

                @hasanyrole("admin|seller")

                <li><a href="{{ route('admin.dashboard') }}"
                       class="nav-link px-2 text-white">Dashboard</a></li>

                @endhasanyrole
            </ul>

            <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" method="post"
                  action="{{route("users.products.search")}}">
                <input type="hidden" name="_token" value="{{csrf_token()}}"/>
                <input type="search" class="form-control form-control-dark"
                       placeholder="Search..." name="searching" aria-label="Search">
            </form>

            @auth
                <div class="text-end">

                    <a href="{{route("cart.index")}}" type="button" class="btn bg-white position-relative">
                        <i class="fa-solid fa-cart-shopping"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-cart_item">
    {{count($cartItems) - 1}}
  </span></a>
                        </a>


                </div>

                {{auth()->user()->name}}&nbsp;
                <div class="text-end" style="margin-left: 10px">
                    <a href="{{ route('logout.perform') }}"
                       class="btn btn-outline-light me-2">Logout</a>
                </div>
            @endauth

            @guest
                <div class="text-end">
                    <a href="{{ route('login.perform') }}" class="btn btn-outline-light me-2">Login</a>
                    <a href="{{ route('register.perform') }}" class="btn btn-warning">Sign-up</a>
                </div>
            @endguest
        </div>
    </div>
</header>
