<?php

namespace App\Repositories\Eloquent\Auth;

use App\Models\User;
use App\Repositories\Interfaces\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**  
 * Eloquent-based implementation of the AuthRepositoryInterface.  
 *  
 * This repository encapsulates all authentication-related data access logic  
 * using Laravel's Eloquent ORM. It handles user creation, lookup, token  
 * management, password reset flows, and email verification.  
 *  
 * Responsibilities include:  
 * - Creating new users with hashed passwords and optional default attributes  
 * - Finding users by email for login and verification purposes  
 * - Generating and managing API tokens (Laravel Sanctum)  
 * - Sending password reset links via Laravel's built-in Password Broker  
 * - Resetting passwords securely using time-limited tokens  
 * - Marking emails as verified and triggering verification notifications  
 *  
 * @implements AuthRepositoryInterface  
 */
class AuthRepository implements AuthRepositoryInterface
{
    /**  
     * Dependency injection of the User Eloquent model.  
     *  
     * @param User $model  
     */
    public function __construct(
        protected User $model
    ) {}

    /**
     * Creates a new user with validated and secured data.
     * 
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => $data['status'] ?? 'active',
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
    }

    /**
     * Finds a user by their email address. 
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): User|null
    {
        return User::where('email', $email)->first();
    }

    /**
     * Generates a new API token for the given user.  
     * 
     * @param User $user
     * @param string $name
     * @return string
     */
    public function createToken(User $user, string $name = 'api_token'): string
    {
        return $user->createToken($name)->plainTextToken;
    }

    /**
     * Deletes the currently active API token for the given user.  
     * 
     * @param User $user
     * @return void
     */
    public function deleteCurrentToken(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    /**
     * Sends a password reset link to the user's registered email address. 
     *
     * @param string $email
     * @return string
     */
    public function sendResetLink(string $email): string
    {
        return Password::sendResetLink(
            ['email' => $email]
        );
    }

    /**
     * Resets the user's password using valid reset credentials.
     *
     * @param array $data
     * @return string
     */
    public function resetPassword(array $data): string
    {
        return Password::reset(
            $data,
            function ($user) use ($data) {
                $user->forceFill([
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );
    }

    /**
     * Marks the user's email as verified in the database.
     * Does NOT send verification email — used after successful verification flow.
     * 
     * @param User $user
     * @return bool
     */
    public function makeEmailAsVerified(User $user): bool
    {

        return  $user->markEmailAsVerified();
    }

    /**
     * Sends an email verification message to the user's unverified email address.
     * Includes a signed, expirable verification URL/link.
     * 
     * @param User $user
     * @return void
     */
    public function sendEmailVerification(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }
}
