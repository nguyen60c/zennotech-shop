@extends("layouts.auth-master")
@section('title', 'Register')

@section('content')
    <form method="post" action="{{ route('register.perform') }}">

        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>


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
                                           value="{{ old('name') }}" placeholder="Your name" required="required"
                                           autofocus>
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
                                    <input type="email" class="form-control" placeholder="email" name="email"
                                           value="{{ old('email') }}" placeholder="Email@example.com"
                                           required="required"
                                           autofocus>
                                    @if ($errors->has('email'))
                                        <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                                <div class="input-group mb-3">
                                    <label>PhoneNumber: </label>
                                    @csrf
                                    <div style="display: flex">
                                        <select id="area">
                                            <option value="+84">+84</option>
                                        </select>
                                        <input type="text" class="form-control" placeholder="phoneNumber" name="phoneNumber"
                                               value="{{ old('phoneNumber') }}" placeholder="0*****" required="required" id="phoneNumber"
                                               autofocus>
                                        <button type="button" id="getcode" class="btn btn-dark btn-sm">Get code</button>
                                    </div>

                                    <div style="display: flex; margin-top: 5px">
                                        <input type="text" id="codeToVerify" name="getcode" class="form-control"
                                               placeholder="Enter Code">
                                        <input type="hidden" class="uid" name="uid" value="">
                                        <a href="#" class="btn btn-dark btn-sm" id="verifPhNum">Verify Phone
                                            No</a>
                                    </div>
                                    <div id="recaptcha"></div>
                                    @if ($errors->has('phoneNumber'))
                                        <span class="text-danger text-left">{{ $errors->first('phoneNumber') }}</span>
                                    @endif
                                    @if ($errors->has('uid'))
                                        <span class="text-danger text-left">{{ $errors->first('uid') }}</span>
                                    @endif
                                    <span class="otp-alert text-danger"></span>
                                    <span class="otp-success text-success"></span>
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
                                    <button class="btn btn-primary text-center mt-2" style="margin-bottom: 10px"
                                            type="submit">
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

@section('script')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/8.0.1/firebase.js"></script>

    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {

            const firebaseConfig = {
                apiKey: "AIzaSyCq1Qw5T8NgUCjks31nhXqHAzL6N7VtUlc",
                authDomain: "shopping-cart-40c64.firebaseapp.com",
                projectId: "shopping-cart-40c64",
                storageBucket: "shopping-cart-40c64.appspot.com",
                messagingSenderId: "1055609268311",
                appId: "1:1055609268311:web:5e19a759932e1a6b095b12",
                measurementId: "G-NV559H49JQ"
            };

            // Initialize Firebase
            firebase.initializeApp(firebaseConfig);

            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha', {
                'size': 'invisible',
                'callback': function (response) {
                    // reCAPTCHA solved, allow signInWithPhoneNumber.
                    console.log('recaptcha resolved');
                }
            });

            recaptchaVerifier.render().then(function(widgetId) {
                window.recaptchaWidgetId = widgetId;
            });
            onSignInSubmit();
        },2000);


        function onSignInSubmit() {
            $('#verifPhNum').on('click', function () {
                debugger
                let phoneNo = '';
                var code = $('#codeToVerify').val();

                if(code === ''){
                    $('.otp-success').text('')
                    $('.otp-alert').text('input your code sms')
                }else{
                    $(this).attr('disabled', 'disabled');
                    $(this).text('Processing..');
                }

                confirmationResult.confirm(code).then(function (result) {
                    debugger
                    alert('Succecss');
                    var user = result.user;

                    $(".uid").val(user.uid);

                    firebase.auth().currentUser.getIdToken( /* forceRefresh */ true).then(function (idToken) {
                        $('.otp-alert').text('')
                        $('#verifPhNum').text('sent')
                        $('.otp-success').text('Verified phone number successful').css('color','')

                    }).catch(function (error) {
                        $('.otp-alert').text(error.message)
                    });

                    // ...
                }.bind($(this))).catch(function (error) {

                    // User couldn't sign in (bad verification code?)
                    // ...
                    $('.otp-success').text('')
                    $('.otp-alert').text('Your code is invalid')
                    $(this).removeAttr('disabled');
                    $(this).text('Invalid Code');
                    setTimeout(() => {
                        $(this).text('Verify Phone No');
                    }, 2000);
                }.bind($(this)));



            });


            $('#getcode').on('click', function (e) {

                e.preventDefault();
                let result = isVietnamesePhoneNumber($('#phoneNumber').val());

                if (!!result) {
                    $('.otp-alert').text('');
                    $.ajax({
                        url: '{{route('register.otp.checkExist')}}',
                        method: "POST",
                        data: {
                            request: $('#phoneNumber').val()
                        },
                        success: function (res) {
                            if (res) {
                                $('.otp-alert').text('');
                                let area = $('#area option:selected').val();
                                var phoneNo = area + $('#phoneNumber').val();
                                console.log(phoneNo)
                                var appVerifier = window.recaptchaVerifier;
                                firebase.auth().signInWithPhoneNumber(phoneNo, appVerifier)
                                    .then(function (confirmationResult) {
                                        window.confirmationResult = confirmationResult;
                                        coderesult = confirmationResult;
                                        $('.otp-success').text('You can use this phone number!!')

                                    }).catch(function (error) {
                                    console.log(error.message);
                                });
                            } else {
                                $('.otp-alert').text('Your phone number has been taken');
                            }
                        }
                    })
                } else {
                    $('.otp-alert').text('Invalid phone number !!')
                }
            });

            function isVietnamesePhoneNumber(number) {
                return /(03|05|07|08|09|01[2|6|8|9])+([0-9]{8})\b/.test(number);
            }

        }

    </script>
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
