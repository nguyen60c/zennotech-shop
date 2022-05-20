<?php

namespace App\Http\Controllers\opt\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function index(){
        return view("otp.auth.register");
    }
}
