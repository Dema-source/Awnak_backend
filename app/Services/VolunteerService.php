<?php

namespace App\Services;

use App\Repositories\Interfaces\VolunteerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service layer for handling business logic related to the "VolunteerRepositoryInterface" repository.
 */
class VolunteerService
{
    /**
     * VolunteerService Constructor.
     *
     * @param \App\Repositories\Interfaces\VolunteerRepositoryInterface $repository
     */
    public function __construct(
        protected VolunteerRepositoryInterface $repository
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
     * Get a paginated list of records with relationships loaded.
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
     * Find a record by its ID with relationships loaded.
     *
     * @param int|string $id The primary key value.
     * @param array $relations Relations to load.
     * @return mixed
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): mixed
    {
        return $this->repository->findByIdWithRelations($id, $relations);
    }

    /**
     * Get active volunteers.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getActive(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getActive($perPage);
    }

    /**
     * Get inactive volunteers.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getInactive(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getInactive($perPage);
    }

    /**
     * Get pending volunteers.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getPending(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPending($perPage);
    }

    /**
     * Get blocked volunteers.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getBlocked(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getBlocked($perPage);
    }

    /**
     * Activate a volunteer.
     *
     * @param int|string $id
     * @return mixed
     */
    public function activate(int|string $id): mixed
    {
        return $this->repository->activate($id);
    }

    /**
     * Deactivate a volunteer.
     *
     * @param int|string $id
     * @return mixed
     */
    public function deactivate(int|string $id): mixed
    {
        return $this->repository->deactivate($id);
    }

    /**
     * Block a volunteer.
     *
     * @param int|string $id
     * @return mixed
     */
    public function block(int|string $id): mixed
    {
        return $this->repository->block($id);
    }

    /**
     * Find volunteer by profile ID.
     *
     * @param int $profileId
     * @return mixed
     */
    public function findByProfileId(int $profileId): mixed
    {
        return $this->repository->findByProfileId($profileId);
    }

    /**
     * Find volunteer by user ID.
     *
     * @param int $userId
     * @return mixed
     */
    public function findByUserId(int $userId): mixed
    {
        return $this->repository->findByUserId($userId);
    }

    /**
     * Check if user has a volunteer.
     *
     * @param int $userId
     * @return bool
     */
    public function userHasVolunteer(int $userId): bool
    {
        return $this->repository->userHasVolunteer($userId);
    }

    /**
     * Get volunteer statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return $this->repository->getStatistics();
    }
}
