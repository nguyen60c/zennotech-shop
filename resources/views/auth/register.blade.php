@extends("layouts.auth-master")
@section('title', 'Register')

@section('content')
    <form method="post" action="{{ route('register.perform') }}">

        <input type="hidden" name="_token" value="{{ csrf_token() }}" />


        <div class="container-fluid">
            <div class="">
                <div class="rounded d-flex justify-content-center">
                    <div class="col-md-4 col-sm-12 shadow-lg bg-light">
                        <div class="text-center">
                            <h3 class="text-primary">Sign Up</h3>
                        </div>
                        <form action="{{ route('register.perform') }}" method="POST">
                            <div class="p-4">
                                {{-- Name --}}
                                <div class="input-group mb-3">
                                    <label>Name: </label>
                                    <input type="text" class="form-control" placeholder="name" name="name"
                                        value="{{ old('name') }}" placeholder="Your name" required="required" autofocus>
                                    @if ($errors->has('name'))
                                        <span class="text-danger text-left">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>

                                {{-- Username --}}
                                <div class="input-group mb-3">
                                    <label>Username: </label>
                                    <input type="text" class="form-control" placeholder="username" name="username"
                                        value="{{ old('username') }}" placeholder="Username" required="required"
                                        autofocus>
                                    @if ($errors->has('username'))
                                        <span class="text-danger text-left">{{ $errors->first('username') }}</span>
                                    @endif
                                </div>

                                {{-- Email --}}
                                <div class="input-group mb-3">
                                    <label>Email: </label>
                                    <input type="text" class="form-control" placeholder="email" name="email"
                                        value="{{ old('email') }}" placeholder="Email@example.com" required="required"
                                        autofocus>
                                    @if ($errors->has('email'))
                                        <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>

                                {{-- Password --}}
                                <div class="input-group mb-3">
                                    <label>Password: </label>
                                    <input type="password" class="form-control" placeholder="password" name="password"
                                        value="{{ old('password') }}" placeholder="Password" required="required"
                                        autofocus>
                                    @if ($errors->has('password'))
                                        <span class="text-danger text-left">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>

                                {{-- Password confirm --}}
                                <div class="input-group mb-3">
                                    <label>Confirm Password: </label>
                                    <input type="password" class="form-control" placeholder="Password"
                                        name="password_confirmation" value="{{ old('password_confirmation') }}"
                                        placeholder="password_confirmation" required="required" autofocus>
                                    @if ($errors->has('password_confirmation'))
                                        <span
                                            class="text-danger text-left">{{ $errors->first('password_confirmation') }}</span>
                                    @endif
                                </div>


                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Remember Me
                                    </label>
                                </div>
                                <div class="row">
                                    <button class="btn btn-primary text-center mt-2" style="margin-bottom: 10px" type="submit">
                                        Register
                                    </button>
                                    <a href="{{ route("login.show") }}" style="margin-bottom: 10px">Back to login</a>
                                    @include('auth.partials.copy')
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('style')

    <style>
        .myForm {
            min-width: 400px;
            position: absolute;
            text-align: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2.5rem
        }

        @media (max-width: 500px) {
            .myForm {
                min-width: 90%;
            }
        }

    </style>

@endsection
