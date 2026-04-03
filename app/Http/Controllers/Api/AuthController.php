<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * AuthController Constructor.
     *
     * @param AuthService $service.
     */
    public function __construct(
        protected AuthService $service
    ) {}

    /**
     * Register user in system.
     * 
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->service->register($request->validated());

        return $this->success($user, 'Auth.Registered successfully');
    }

    /**
     * User login.
     * 
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->service->login($request->validated());

        return $this->success($user, 'Auth.Login successfully');
    }

    /**
     * Get currently authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return $this->success($request->user(), 'Auth.me');
    }

    /**
     * User logout.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->service->logoutCurrent($request->user());
        
        return $this->success(null, 'Auth.Logout successfully');
    }
}
