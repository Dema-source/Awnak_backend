<?php

namespace App\Services\Auth;

use App\Repositories\Interfaces\Auth\AuthRepositoryInterface;
use Illuminate\Auth\Events\Registered;

/**
 * Service layer for handling register business logic related to the "AuthRepositoryInterface" repository.
 */
class RegisterService
{
    /**
     * UserService Constructor.
     *
     * @param \App\Repositories\Interfaces\Auth\AuthRepositoryInterface $repository
     */
    public function __construct(
        protected AuthRepositoryInterface $repository
    ) {}

    /**
     * Handle the user registration process.
     * 
     * @param array $data
     * @return array{token: string, user: \App\Models\User}
     */
    public function handle(array $data)
    {
        $user = $this->repository->createUser($data);

        event(new Registered($user));
        
        $token = $user->createToken('api_token')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
