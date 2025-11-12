<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            if (Auth::attempt($request->validated())) {
                $user = Auth::user();
                $token = $user->createToken('auth_token')->plainTextToken;

                return ApiResponse::success([
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $user->only('id', 'name', 'email', 'shop_id'),
                ], 'Login successful');
            }

            // Invalid credentials
            return ApiResponse::error('Invalid credentials', null, 401);
        } catch (\Exception $e) {
            Log::error('Login failed: '.$e->getMessage(), [
                'email' => $request->input('email'),
            ]);

            return ApiResponse::error(
                'An unexpected error occurred during login.',
                $e->getMessage(),
                500
            );
        }
    }
}
