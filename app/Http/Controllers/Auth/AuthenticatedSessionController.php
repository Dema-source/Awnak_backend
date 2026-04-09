<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\LoginService;
use App\Services\Auth\LogoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends Controller
{
    /**
     * Constructor injecting the required LoginService and LogoutService dependency.
     * 
     * @param LoginService $loginservice
     * @param LogoutService $logoutservice
     */
    public function __construct(
        protected LoginService $loginservice,
        protected LogoutService $logoutservice,
    ) {}

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $data = $this->loginservice->handle($request->validated());

        return $this->success($data, 'Login successfully');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        $this->logoutservice->handle($request->user());

        return $this->success(null, 'Logout successfully');
    }
}
