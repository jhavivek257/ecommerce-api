<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //user registeration
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->validated();

            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            return response()->json(['message' => 'User created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went really wrong!' . $e->getMessage()], 500);
        }
    }

    //user login
    public function login(LoginRequest $request)
    {
        try {
            $data = $request->validated();

            if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $accessToken = Auth::user()->createToken('authToken')->accessToken;

            return response()->json([
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'message' => 'User logged in successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went really wrong!' . $e->getMessage()], 500);
        }
    }

    //user logout
    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();

            return response()->json(['message' => 'User logged out successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went really wrong!' . $e->getMessage()], 500);
        }
    }
}
