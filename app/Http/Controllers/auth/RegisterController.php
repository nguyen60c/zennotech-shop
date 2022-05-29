<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    private $user;
    public function __construct()
    {
        $this->user = new User();
    }

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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function register(RegisterRequest $request){
        $user = User::create($request->validated());
        auth()->login($user);
        $user->assignRole("user");

        return redirect("/")
        ->with('success', "Account successfully registered.");
    }

    public function checkPnbExist(Request $request){
        $phoneNumber = $request['request'];
        $isExisted = $this->user->isPhoneNumberTaken($phoneNumber);
        return $isExisted;
    }
}
