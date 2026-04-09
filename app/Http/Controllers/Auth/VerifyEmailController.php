<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\VerifyEmailService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;


class VerifyEmailController extends Controller
{
    /**
     * Constructor injecting the required VerifyEmailService dependency.
     * 
     * @param VerifyEmailService $service
     */
    public function __construct(
        protected VerifyEmailService $service
    ) {}

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): JsonResponse
    {
        $this->service->handle($request->user());

        return $this->success(['message' => 'Email verified successfully'], 'Check your email');
    }
}
