<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request){
        $user = $request->user();
        $user->image = User::where("id", $user->id)->first()->imageUrl();
        return response()->json([
            "status" => 200,
            "data" => $user
        ]);
    }

    public function search(Request $request){
        $result = User::where("name", "like", "%".$request->keyword."%")
            ->orWhere("username", "like", "%".$request->keyword."%")
            ->latest()
            ->limit(7)
            ->pluck("name", "username");

        return response()->json([
            "status" => 200,
            "data" => $result
        ]);
    }
}