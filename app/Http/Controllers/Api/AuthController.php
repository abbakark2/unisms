<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function Login(LoginRequest $request)
    {
        $request->validated($request->all());
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                "error" => "Invalid user credentials"
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;

        return response()->json([
            "message" => "Login successful",
            "user" => $user,
            "token-type" => "Bearer",
            "token" => $token
        ], 200);
    }

    public function Logout(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(["error" => "Unauthorize user"], 422);

        $user->tokens()->delete();

        return response()->json([
            "user id" => $user->id,
            "message" => "logout successfully",
            "isLoggedIn" => false
        ]);
    }
}
