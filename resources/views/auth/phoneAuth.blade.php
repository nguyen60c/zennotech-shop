@extends('layouts.otp-firebase')


@section('content')


    <section class="validOTPForm">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h4 class="text-center">
                                Account verification
                            </h4>
                        </div>


                        <div class="card-body">
                            <form class="frm-otp" method="post" action="{{route('login.otp.perform')}}">
                                @csrf
                                <div class="form-group">
                                    <label for="phone_no">Phone Number</label>
                                    <div style="display: flex">
                                        <select id="area">
                                            <option value="+84">+84</option>
                                        </select>
                                        <input type="text" class="form-control" name="phone_no" id="number"
                                               placeholder="(Code) *******">
                                    </div>

                                    <span class="text-danger otp-alert"></span>
                                    <span class="text-success otp-success"></span>
                                </div>
                                <div id="recaptcha-container"></div>
                                <button id="getcode" class="btn btn-dark btn-sm">Get Code</button>
                                <div class="recaptcha-container"></div>
                                <div class="form-group mt-4">
                                    <input type="text" name="" id="codeToVerify" name="getcode" class="form-control"
                                           placeholder="Enter Code">
                                </div>
                                <input class="token_id" type="hidden" name="tokenId" value="">

                                <a href="#" class="btn btn-dark btn-sm btn-block" id="verifPhNum">Verify Phone No</a>
                                <button class="button-submit" style="display: none">submit</button>

                            </form>
                            <a href="{{route('login.show')}}">Back To Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
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

            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                'size': 'invisible',
                'callback': function (response) {
                    // reCAPTCHA solved, allow signInWithPhoneNumber.
                    console.log('recaptcha resolved');
                }
            });
            onSignInSubmit();
        });


        function onSignInSubmit() {
            $('#verifPhNum').on('click', function () {
                let phoneNo = '';
                var code = $('#codeToVerify').val();

                if (code === '') {
                    $('.otp-success').text('')
                    $('.otp-alert').text('input your code sms')
                } else {
                    $(this).attr('disabled', 'disabled');
                    $(this).text('Processing..');
                }

                confirmationResult.confirm(code).then(function (result) {
                    alert('Succecss');
                    var user = result.user;
                    console.log(user);

                    firebase.auth().currentUser.getIdToken( /* forceRefresh */ true).then(function (idToken) {

                        $('.token_id').val(idToken);
                        $('.frm-otp').submit();

                    }).catch(function (error) {
                        // Handle error
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
                let result = isVietnamesePhoneNumber($('#number').val());
                let area = $('#area option:selected').val();
                let temp = '';

                if (!!result) {
                    $('.otp-alert').text('');
                    $.ajax({
                        url: '{{route('login.otp.check')}}',
                        method: "POST",
                        data: {
                            phone_number: $('#number').val(),
                            area: area
                        },
                        success: function (res) {
                            console.log(res);
                            if (res) {
                                $('.otp-alert').text('');
                                var phoneNo = area + $('#number').val();
                                var appVerifier = window.recaptchaVerifier;
                                firebase.auth().signInWithPhoneNumber(phoneNo, appVerifier)
                                    .then(function (confirmationResult) {
                                        window.confirmationResult = confirmationResult;
                                        coderesult = confirmationResult;
                                        console.log(coderesult);
                                        $('.otp-alert').text('')

                                    }).catch(function (error) {
                                    console.log(error.message);
                                });
                            } else {
                                $('.otp-alert').text('Your phone number does not exist');
                            }
                        }
                    })
                } else {
                    $('.otp-alert').text('Invalid format phone number !!')
                }

                console.log(temp)
            });

            function isVietnamesePhoneNumber(number) {
                return /(03|05|07|08|09|01[2|6|8|9])+([0-9]{8})\b/.test(number);
            }

        }


    </script>


@endpush
