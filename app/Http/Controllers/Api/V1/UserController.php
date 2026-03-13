<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * regiister user in system.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->name,
            'address' => $request->email,
        ]);
        return self::success($user, 'Registered successfully', 201);
    }

    /**
     * User login.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if (!Auth::attempt($request->only('email', 'password'))) {
            return self::error('invalid email or password', 401);
        }
        $user = User::where('email', $request->email)->FirstOrFail();
        $token = $user->createToken('auth_Token')->plainTextToken;
        return self::success([
            'user' => $user,
            'token' => $token,
        ], 'Login successfully', 201);
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
