<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service layer for handling business logic related to the "UserRepositoryInterface" repository.
 */
class UserService
{
    /**
     * UserService Constructor.
     *
     * @param \App\Repositories\Interfaces\UserRepositoryInterface $repository
     */
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    /**
     * Retrieve a paginated list of records applying optional dynamic filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }

    /**
     * Find a record by its ID.
     *
     * @param int|string $id
     * @return mixed
     */
    public function findById(int|string $id): mixed
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new record using the provided data.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing record by ID with the given data.
     *
     * @param int|string $id
     * @param array $data
     * @return mixed
     */
    public function update(int|string $id, array $data): mixed
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Search users by name, email with optional filters.
     *
     * @param string|null $search
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(?string $search = null, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->search($search, $filters, $perPage);
    }

    /**
     * Get users filtered by various criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function filter(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->filter($filters, $perPage);
    }

    /**
     * Inactive a user by setting status to 'inactive'.
     *
     * @param int|string $id The user ID.
     * @return mixed
     */
    public function inactive(int|string $id): mixed
    {
        return $this->repository->inactive($id);
    }
}