<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $user = User::create($request->validated());
        $auth_token = $user->createToken($user->username."_AUTH_TOKEN")->plainTextToken;

        return response()->json([
            "status" => 200,
            "message" => "Alhamdulillah! Succesfully registered.",
            "data" => [
                "auth_token" => $auth_token,
                "username" => $user->username
            ]
        ]);
    }

    public function login(LoginRequest $request){
        $user = User::where("email", $request->only("email"))->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                "status" => 401,
                "message" => "Opss! Email or Password is not correct",
            ]);
        }else{
            $auth_token = $user->createToken($user->username."_AUTH_TOKEN")->plainTextToken;

            return response()->json([
                "status" => 200,
                "message" => "Alhamdulillah! Succesfully signed in.",
                "data" => [
                    "auth_token" => $auth_token,
                    "username" => $user->username
                ]
            ]);
        }
    }

    public function logout(Request $request){
        return $request->user()->currentAccessToken()->delete();
    }
}