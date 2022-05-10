<?php

namespace App\Http\Controllers\api;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\auth\LoginRequest;
use App\Http\Requests\auth\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
//        $fields = $request->validate([
//            "username" => "required|string",
//            "password" => "required|string"
//        ]);

        /*Check email*/
        $user = User::where("username", $request->username)
            ->orWhere("email",$request->username)->first();

        /*Check password*/
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                "user" => Hash::make($request->password),
                "message" => "Bad creds"
            ], 401);
        }

        $token = $user->createToken("user-token")->plainTextToken;

        $response = [
            "user" => $user,
            "token" => $token
        ];

        return response($response, 201);
    }

    public function register(RegisterRequest $request)
    {

        $user = User::create([
            "username" => $request->username,
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,
        ]);

        $user->assignRole("user");

        $token = $user->createToken("user-token")->plainTextToken;

        $response = [
            "user" => $user,
            "token" => $token
        ];

        return response($response, 201);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            "message" => "Logout"
        ];
    }
}
