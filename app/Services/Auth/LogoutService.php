<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Interfaces\Auth\AuthRepositoryInterface;

/**
 * Service layer for handling logout business logic related to the "AuthRepositoryInterface" repository.
 */
class LogoutService
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
     * Handles user logout by invalidating the current API token.
     * 
     * @param User $user
     * @return void
     */
    public function handle(User $user): void
    {
        $this->repository->deleteCurrentToken($user);
    }
}
