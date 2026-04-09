<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\ResendVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Constructor injecting the required ResendVerificationService dependency.
     * 
     * @param ResendVerificationService $service
     */
    public function __construct(
        protected ResendVerificationService $service
    ) {}

    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): JsonResponse
    {
        $sent = $this->service->handle($request->user());

        if (! $sent) {
            return $this->success(null, 'Already verified');
        }

        return $this->success(['status' => 'verification-link-sent']);
    }
}
