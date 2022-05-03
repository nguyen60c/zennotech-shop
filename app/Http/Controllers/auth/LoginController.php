<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function show(){
        return view("auth.login");
    }

    /**
     * Handle account login request
     */
    public function login(LoginRequest $request){
        $credentials = $request->getCreadentials();

        if(!Auth::validate($credentials)){
            return redirect()->to("login")
            ->withErrors(trans('auth.failed'));
        }

        $user = Auth::getProvider()
        ->retrieveByCredentials($credentials);

        /*Switch page for each role*/
        if($user->hasRole("user")){
            Auth::login($user);

            return $this->authenticated($request, $user);
        }

        Auth::login($user);

        return redirect()->route("admin.dashboard");
    }


    /**
     * Handle response after user authenticated
     */
    protected function authenticated(Request $request, $user){
        return redirect()->intended();
    }

}
