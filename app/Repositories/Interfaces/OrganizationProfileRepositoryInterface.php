<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\OrganizationProfile;
use Illuminate\Database\Eloquent\Collection;

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
     * List all active organizations.
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listActive(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Activate an organization.
     * 
     * @param OrganizationProfile $organization
     * @return bool
     */
    public function activate(OrganizationProfile $organization): bool;

    /**
     * Deactivate an organization.
     * 
     * @param OrganizationProfile $organization
     * @return bool
     */
    public function deactivate(OrganizationProfile $organization): bool;

    /**
     * Find organization profile by user ID.
     *
     * @param int $userId
     * @return OrganizationProfile|null
     */
    public function findByUserId(int $userId): ?OrganizationProfile;

    /**
     * Get all opportunities for a specific organization.
     * 
     * @param int $organizationId
     * @return Collection
     */
    public function getOrganizationOpportunities(int $organizationId): Collection;

    /**
     * Get a paginated list of records with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a record by its ID with relationships loaded.
     *
     * @param int|string $id The primary key value.
     * @param array $relations Relations to load.
     * @return OrganizationProfile
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): OrganizationProfile;

    /**
     * Get profile statistics.
     *
     * @return array
     */
    public function getStatistics(): array;

    /**
     * Get organizations by type.
     *
     * @param string $type
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByType(string $type, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get organizations with opportunities.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getWithOpportunities(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Check if user has organization profile.
     *
     * @param int $userId
     * @return bool
     */
    public function userHasProfile(int $userId): bool;

    /**
     * Get organizations created in date range.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByDateRange(string $fromDate, string $toDate, array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
