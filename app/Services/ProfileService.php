<?php

namespace App\Services;

use App\Repositories\Interfaces\ProfileRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service layer for handling business logic related to the "ProfileRepositoryInterface" repository.
 */
class ProfileService
{
    /**
     * ProfileService Constructor.
     *
     * @param \App\Repositories\Interfaces\ProfileRepositoryInterface $repository
     */
    public function __construct(
        protected ProfileRepositoryInterface $repository
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
     * Find a profile by user ID.
     *
     * @param int|string $userId The user ID.
     * @return mixed
     */
    public function findByUserId(int|string $userId): mixed
    {
        return $this->repository->findByUserId($userId);
    }

    /**
     * Get profiles with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllWithRelations($relations, $filters, $perPage);
    }

    /**
     * Get profile by ID with relationships.
     *
     * @param int|string $id The profile ID.
     * @param array $relations Relations to load.
     * @return mixed
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): mixed
    {
        return $this->repository->findByIdWithRelations($id, $relations);
    }

    /**
     * Get profiles by gender.
     *
     * @param string $gender The gender to filter by.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByGender(string $gender, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByGender($gender, $perPage);
    }

    /**
     * Get profiles by age range.
     *
     * @param int $minAge Minimum age.
     * @param int $maxAge Maximum age.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByAgeRange(int $minAge, int $maxAge, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByAgeRange($minAge, $maxAge, $perPage);
    }

    /**
     * Search profiles by bio or interests.
     *
     * @param string $searchTerm Search term.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function searchByBioOrInterests(string $searchTerm, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->searchByBioOrInterests($searchTerm, $perPage);
    }

    /**
     * Get profiles with specific skills.
     *
     * @param array $skillIds Array of skill IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getBySkills(array $skillIds, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getBySkills($skillIds, $perPage);
    }

    /**
     * Get profile statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return $this->repository->getStatistics();
    }
}