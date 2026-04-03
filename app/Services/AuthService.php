<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class AuthService
{
    /**
     * AuthService Constructor.
     *
     * @param \App\Repositories\Interfaces\UserRepositoryInterface $repository
     */
    public function __construct(
        protected UserRepositoryInterface $repository,
    ) {}

    /**
     * Register a new user and issue an API token.
     *
     * @param array<string, mixed> $data Validated registration data.
     * @return array
     */
    public function register(array $data): array
    {
        $user = $this->repository->create($data);

        $token = $user->createToken('api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Authenticate user credentials.
     *
     * @param array<string, mixed> $data Validated login credentials.
     * @return array
     */
    public function login(array $data): array
    {
        $user = $this->repository->findByEmail($data['email']);
        $token = $user->createToken('api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Revoke the currently active access token for the given user.
     *
     * @param User $user Authenticated user.
     * @return void
     */
    public function logoutCurrent(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
