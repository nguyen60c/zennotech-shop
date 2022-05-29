@extends("layouts.auth-master")
@section('title', 'Login')

@section('content')
    <form method="post" action="{{ route('login.perform') }}">

        <input type="hidden" name="_token" value="{{ csrf_token() }}" />


        <div class="container-fluid">
            <div class="">
                <div class="rounded d-flex justify-content-center">
                    <div class="col-md-4 col-sm-12 shadow-lg bg-light">
                        <div class="text-center">
                            @include("layouts.partials.message")
                            <h3 class="text-primary">Sign In</h3>
                        </div>
                        <form action="{{ route('register.perform') }}" method="POST">
                            <div class="p-4">
                                {{-- Name --}}
                                <div class="input-group mb-3">
                                    <label>Username: </label>
                                    <input type="text" class="form-control" name="username" value="{{ old('username') }}"
                                        placeholder="Username" required="required" autofocus>
                                    @if ($errors->has('username'))
                                        <span class="text-danger text-left">{{ $errors->first('username') }}</span>
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

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Remember Me
                                    </label>
                                </div>
                                <div class="row">
                                    <button class="btn btn-primary text-center mt-2" type="submit">
                                        Login
                                    </button>
                                    <p class="text-center" style="margin-top: 3px;margin-bottom: 1px">
                                        <a href="{{ route('login.opt.show') }}" class="text-center text-primary">Login by phoneNumber</a>
                                    </p>
                                    <p class="text-center mt-5" style="margin-bottom: 5px">Don't have an account?
                                        <a href="{{ route('register.show') }}" class="text-center text-primary">Sign
                                            Up</a>
                                    </p>
                                    <p class="text-center text-primary">Forgot your password?</p>
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
