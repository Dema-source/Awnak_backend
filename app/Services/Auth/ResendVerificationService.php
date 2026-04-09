<?php

namespace App\Services\Auth;

use App\Repositories\Interfaces\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

/**
 * Service layer for handling Forget Password business logic related to the "AuthRepositoryInterface" repository.
 */
class ResendVerificationService
{
    /**
     * ResendVerificationService Constructor.
     *
     * @param \App\Repositories\Interfaces\Auth\AuthRepositoryInterface $repository
     */
    public function __construct(
        protected AuthRepositoryInterface $repository
    ) {}

    /**
     * Handle the email verification resend process.
     * 
     * @param mixed $user
     * @return bool
     */
    public function handle($user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }
        
        $this->repository->sendEmailVerification($user);
        return true;
    }
}
