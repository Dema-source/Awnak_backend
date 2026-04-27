<?php

namespace App\Services;

use App\Repositories\Interfaces\OrganizationProfileRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service layer for handling business logic related to the "OrganizationProfileRepositoryInterface" repository.
 */
class OrganizationProfileService
{
    /**
     * OrganizationProfileService Constructor.
     *
     * @param \App\Repositories\Interfaces\OrganizationProfileRepositoryInterface $repository
     */
    public function __construct(
        protected OrganizationProfileRepositoryInterface $repository
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
        // Format license number
        if (!empty($data['license_number'])) {
            $data['license_number'] = 'ORG-' . strtoupper(trim($data['license_number']));
        }
        
        // Format website URL
        if (!empty($data['website']) && !preg_match('/^https?:\/\//', $data['website'])) {
            $data['website'] = 'https://' . $data['website'];
        }
        
        // Set default status for non-admin users
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }
        
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
        // Format license number if provided
        if (!empty($data['license_number'])) {
            $data['license_number'] = 'ORG-' . strtoupper(trim($data['license_number']));
        }
        
        // Format website URL if provided
        if (!empty($data['website']) && !preg_match('/^https?:\/\//', $data['website'])) {
            $data['website'] = 'https://' . $data['website'];
        }
        
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
     * List all not-active organizations.
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listNotActive(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->listNotActive($filters, $perPage);
    }

    /**
     * List all active organizations.
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listActive(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->listActive($filters, $perPage);
    }

    /**
     * Activate an organization.
     * 
     * @param int|string $id
     * @return bool
     */
    public function activate(int|string $id): bool
    {
        return $this->repository->activate($this->findById($id));
    }

    /**
     * Deactivate an organization.
     * 
     * @param int|string $id
     * @return bool
     */
    public function deactivate(int|string $id): bool
    {
        return $this->repository->deactivate($this->findById($id));
    }

    /**
     * Find organization profile by user ID.
     *
     * @param int $userId
     * @return mixed
     */
    public function findByUserId(int $userId): mixed
    {
        return $this->repository->findByUserId($userId);
    }

    /**
     * Get all opportunities for a specific organization.
     * 
     * @param int $organizationId
     * @return Collection
     */
    public function getOrganizationOpportunities(int $organizationId): Collection
    {
        return $this->repository->getOrganizationOpportunities($organizationId);
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
     * Get organization statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return $this->repository->getStatistics();
    }

    /**
     * Get organizations by type.
     *
     * @param string $type
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByType(string $type, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByType($type, $filters, $perPage);
    }

    /**
     * Get organizations with opportunities.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getWithOpportunities(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getWithOpportunities($filters, $perPage);
    }

    /**
     * Check if user has organization profile.
     *
     * @param int $userId
     * @return bool
     */
    public function userHasProfile(int $userId): bool
    {
        return $this->repository->userHasProfile($userId);
    }

    /**
     * Get organizations created in date range.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByDateRange(string $fromDate, string $toDate, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByDateRange($fromDate, $toDate, $filters, $perPage);
    }
}
