<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Services\Auth\ForgetPasswordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Constructor injecting the required ForgetPasswordService dependency.
     * 
     * @param ForgetPasswordService $service
     */
    public function __construct(
        protected ForgetPasswordService $service
    ) {}

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(ForgetPasswordRequest $request): JsonResponse
    {
        $status = $this->service->handle($request->validated());

        return $this->success(['status' => __($status)], 'Check your email');
    }
}
