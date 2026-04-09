<?php

namespace App\Services\Auth;

use App\Repositories\Interfaces\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

/**
 * Service layer for handling Forget Password business logic related to the "AuthRepositoryInterface" repository.
 */
class ForgetPasswordService
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
     * Initiates the password reset flow for the given email address.
     *
     * @param array $data 
     * @return string 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handle(array $data): string
    {
        $email = $data['email'];

        $status = $this->repository->sendResetLink($email);

        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
        return $status;
    }
}
