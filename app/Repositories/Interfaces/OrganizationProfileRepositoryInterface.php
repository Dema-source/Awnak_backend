<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\OrganizationProfile;

/**
 * Interface OrganizationProfileRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface OrganizationProfileRepositoryInterface
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
     * @return OrganizationProfile 
     */
    public function findById(int|string $id): OrganizationProfile;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return OrganizationProfile
     */
    public function create(array $data): OrganizationProfile;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return OrganizationProfile
     */
    public function update(int|string $id, array $data): OrganizationProfile;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * List all not-active organizations.
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listNotActive(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Activate an organization.
     * 
     * @param OrganizationProfile $organization
     * @return bool
     */
    public function activate(OrganizationProfile $organization):bool;

    /**
     * Get all opportunities for a specific organization.
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrganizationOpportunities(int $userId): \Illuminate\Database\Eloquent\Collection;
}
