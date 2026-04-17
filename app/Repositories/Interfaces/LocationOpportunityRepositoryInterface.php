<?php

namespace App\Repositories\Interfaces;

use App\Models\Location;
use App\Models\Opportunity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface LocationOpportunityRepositoryInterface
 *
 * Defines the contract for Location-Opportunity relationship operations.
 */
interface LocationOpportunityRepositoryInterface
{
    /**
     * Get all locations linked to opportunities with related opportunity.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get locations for a specific opportunity.
     *
     * @param int|string $opportunityId
     * @return Collection
     */
    public function getLocationsByOpportunity(int|string $opportunityId): Collection;

    /**
     * Get opportunities for a specific location.
     *
     * @param int|string $locationId
     * @return Collection
     */
    public function getOpportunitiesByLocation(int|string $locationId): Collection;

    /**
     * Attach locations to an opportunity (handles both single and multiple).
     *
     * @param int|array $locationIds
     * @param int|string $opportunityId
     * @param array $data
     * @return bool
     */
    public function attachLocations($locationIds, int|string $opportunityId, array $data = []): bool;

    /**
     * Detach locations from an opportunity (handles both single and multiple).
     *
     * @param int|array $locationIds
     * @param int|string $opportunityId
     * @return bool
     */
    public function detachLocations($locationIds, int|string $opportunityId): bool;

    /**
     * Sync locations for an opportunity (detaches all current and attaches new ones).
     *
     * @param int|string $opportunityId
     * @param array $locationIds
     * @param array $data
     * @return void
     */
    public function syncLocations(int|string $opportunityId, array $locationIds, array $data = []): void;

    /**
     * Check if an opportunity has a specific location.
     *
     * @param int|string $opportunityId
     * @param int|string $locationId
     * @return bool
     */
    public function hasLocation(int|string $opportunityId,int|string $locationId): bool;

    /**
     * Get opportunity with its locations loaded.
     *
     * @param int|string $opportunityId
     * @return Opportunity|null
     */
    public function getOpportunityWithLocations(int|string $opportunityId): ?Opportunity;

    /**
     * Get location with its opportunities loaded.
     *
     * @param int|string $locationId
     * @return Location|null
     */
    public function getLocationWithOpportunities(int|string $locationId): ?Location;

    /**
     * Get locations count for each opportunity.
     *
     * @param array $opportunityIds
     * @return array
     */
    public function getLocationsCount(array $opportunityIds): array;

    /**
     * Update pivot data for a specific opportunity-location relationship.
     *
     * @param int|string $opportunityId
     * @param int|string $locationId
     * @param array $data
     * @return bool
     */
    public function updateOpportunityLocationPivot(int|string $opportunityId, int|string $locationId, array $data): bool;

    /**
     * Get pivot data for a specific opportunity-location relationship.
     *
     * @param int|string $opportunityId
     * @param int|string $locationId
     * @return array|null
     */
    public function getOpportunityLocationPivot(int|string $opportunityId, int|string $locationId): ?array;
}
