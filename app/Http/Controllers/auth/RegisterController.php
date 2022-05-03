<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{

    /**
     * Display register form
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(){
        return view("auth.register");
    }

    /**
     * Handle account registration request
     * 
     * @param RegisterRequest $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request){
        $user = User::create($request->validated());

        auth()->login($user);

        return redirect("/")
        ->with('success', "Account successfully registered.");
    }
}
