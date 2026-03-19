<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Register user in system.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return self::success($user, 'Registered successfully', 201);
    }

    /**
     * User login.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];
        if (!Auth::attempt($credentials)) {
            return self::error('invalid email or password', 401);
        }
        $user = Auth::user();
        $token = $user->createToken('auth_Token')->plainTextToken;
        return self::success([
            'user' => $user,
            'token' => $token,
        ], 'Login successfully', 200);
    }

    /**
     * User logout.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return self::success(null, 'Logout successfully');
    }
}
