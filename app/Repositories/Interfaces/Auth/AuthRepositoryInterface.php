<?php

namespace App\Repositories\Interfaces\Auth;

use App\Models\User;

/**
 * Interface AuthRepositoryInterface
 *
 * Defines a standard contract for authentication-related data operations.
 */
interface AuthRepositoryInterface
{
    /**
     * Creates a new user in the system using the provided data.
     * 
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User;

    /**
     * Finds and returns a user by their unique email address.
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): User|null;

    /**
     * Generates and persists an API token for the given user.
     * 
     * @param User $user
     * @param string $name
     * @return string
     */
    public function createToken(User $user, string $name = 'api_token'): string;

    /**
     * Invalidates and deletes the currently active API token associated with the user.
     * Used during logout or token rotation.
     * 
     * @param User $user
     * @return void
     */
    public function deleteCurrentToken(User $user): void;

    /**
     * Sends a password reset link to the user's registered email address.
     * Generates and stores a time-limited reset token, then dispatches the email.
     *  
     * @param string $email
     * @return string
     */
    public function sendResetLink(string $email): string;

    /**
     * Resets the user's password using valid reset credentials.
     * 
     * @param array $data
     * @return string
     */
    public function resetPassword(array $data): string;

    /**
     * Marks the user's email as verified in the database.
     * Does NOT send verification email — used after successful verification flow.
     * 
     * @param User $user
     * @return bool
     */
    public function makeEmailAsVerified(User $user): bool;

    /**
     * Sends an email verification message to the user's unverified email address.
     * Includes a signed, expirable verification URL/link.
     * 
     * @param User $user
     * @return void
     */
    public function sendEmailVerification(User $user): void;
}
