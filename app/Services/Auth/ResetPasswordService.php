<?php

namespace App\Services\Auth;

use App\Repositories\Interfaces\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;

/**
 * Service layer for handling Forget Password business logic related to the "AuthRepositoryInterface" repository.
 */
class ResetPasswordService
{
    /**
     * ForgetPasswordService Constructor.
     *
     * @param \App\Repositories\Interfaces\Auth\AuthRepositoryInterface $repository
     */
    public function __construct(
        protected AuthRepositoryInterface $repository
    ) {}

    /**
     * Handle the password reset process.
     * 
     * @param array $data
     * @return string
     */
    public function handle(array $data): string
    {

        $status = $this->repository->resetPassword($data);

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
        return $status;
    }
}
