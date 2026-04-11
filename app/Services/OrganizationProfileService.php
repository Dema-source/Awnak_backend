<?php

namespace App\Services;

use App\Repositories\Interfaces\OrganizationProfileRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
        if (!empty($data['license_number'])) {
            $data['license_number'] = 'ORG-' . strtoupper(trim($data['license_number']));
        }
        if (!empty($data['website']) && !preg_match('/^https?:\/\//', $data['website'])) {
            $data['website'] = 'https://' . $data['website'];
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
     * Get all opportunities for a specific organization.
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrganizationOpportunities(int $id): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getOrganizationOpportunities($id);
    }
}
