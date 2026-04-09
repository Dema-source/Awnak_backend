<?php

namespace App\Services\Auth;

use App\Repositories\Interfaces\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Service layer for handling logout business logic related to the "AuthRepositoryInterface" repository.
 */
class LoginService
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
     * Authenticates a user credential and issues a Sanctum API token.
     * 
     * @param array $data
     * @return array
     */
    public function handle(array $data): array
    {
        $user = $this->repository->findByEmail($data['email']);

        if (!$user || ! Hash::check($data['password'], $user->password))
            throw ValidationException::withMessages(['email', 'Invalid credentials']);

        $token = $this->repository->createToken($user, 'api');

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
