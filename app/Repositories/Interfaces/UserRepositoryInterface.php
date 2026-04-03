<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;

/**
 * Interface UserRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface UserRepositoryInterface
{
    /**
     * Retrieve a paginated list of records with optional provided conditions.
     *
     * @param array $filters [Key => value] filters.
     * @param int $perPage size of items in each page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a record by its ID.
     *
     * @param int|string $id The primary key value.
     * @return User 
     */
    public function findById(int|string $id): User;

    /**
     * Find user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): User;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return User
     */
    public function create(array $data): User;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return User
     */
    public function update(int|string $id, array $data): User;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Hash the given plain password.
     *
     * @param string $plainPassword
     * @return string
     */
    public function hashPassword(string $plainPassword): string;
}
