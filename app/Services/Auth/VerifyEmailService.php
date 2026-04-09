<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Interfaces\Auth\AuthRepositoryInterface;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Service layer for handling logout business logic related to the "AuthRepositoryInterface" repository.
 */
class VerifyEmailService
{
    /**
     * LogoutService Constructor.
     *
     * @param \App\Repositories\Interfaces\Auth\AuthRepositoryInterface $repository
     */
    public function __construct(
        protected AuthRepositoryInterface $repository
    ) {}

    /**
     * Handle the email verification process for a given user.
     * 
     * @param mixed $user
     * @return bool
     */
    public function handle($user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return true;
        }
        if ($this->repository->makeEmailAsVerified($user)) {
            event(new Verified($user));
        }
        return true;
    }
}
