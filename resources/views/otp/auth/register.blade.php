@extends("layouts.otp-firebase")

@section("content")

    <section class="validOtpForm">
        <div class="container">
            <div class="row d-flex justify-content-center" style="margin-top: 40px">
                <div class="col-md-6 max-auto">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h4>
                                Account verification
                            </h4>
                        </div>

                        <div class="card-body">
                            <form>
                                @csrf
                                <div class="form-group">
                                    <label for="phone_no">Phone number</label>

                                    <input type="text" class="form-control"
                                           name="phone_no" id="number" placeholder="(Code) ********">
                                    <a href="#" id="getcode" class="btn btn-dark btn-sm">
                                        Get code
                                    </a>

                                    <div class="form-group mt-4">
                                        <input type="text" name="" id="codeToVerify" name="getcode" class="form-control" placeholder="Enter code">
                                    </div>

                                    <a href="#" class="btn btn-dark btn-sm btn-block" id="verifyPhNum">
                                        Verify Phone no
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push("scripts")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/9.8.1/firebase-app.min.js" integrity="sha512-cmzWvlOKv91z3SiiY0nQYzX8DfTPt4izvn6CTLUfDekimetmTbntAnPrrS87RIB5nUdmmggMW+9ih64YN2Dawg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{asset("assets/js/firebase")}}"

@endpush
