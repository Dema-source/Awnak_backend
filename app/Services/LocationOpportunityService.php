<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Opportunity;
use App\Repositories\Interfaces\LocationOpportunityRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LocationOpportunityService
{
    /**
     * LocationOpportunityService constructor.
     *
     * @param LocationOpportunityRepositoryInterface $repository
     */
    public function __construct(
        private LocationOpportunityRepositoryInterface $repository
    ) {}

    /**
     * Get all locations linked to opportunities with related opportunity.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllWithRelations($filters, $perPage);
    }

    /**
     * Get all locations for a specific opportunity.
     *
     * @param int|string $opportunityId
     * @return Collection
     */
    public function getLocationsByOpportunity(int|string $opportunityId): Collection
    {
        return $this->repository->getLocationsByOpportunity($opportunityId);
    }

    /**
     * Get all opportunities for a specific location.
     *
     * @param int|string $locationId
     * @return Collection
     */
    public function getOpportunitiesByLocation(int|string $locationId): Collection
    {
        return $this->repository->getOpportunitiesByLocation($locationId);
    }

    /**
     * Attach locations to an opportunity (handles both single and multiple).
     *
     * @param int|array $locationIds
     * @param int|string $opportunityId
     * @param array $data
     * @return bool
     */
    public function attachLocations($locationIds, int|string $opportunityId, array $data = []): bool
    {
        return $this->repository->attachLocations($locationIds, $opportunityId, $data);
    }

    /**
     * Detach locations from an opportunity (handles both single and multiple).
     *
     * @param int|array $locationIds
     * @param int|string $opportunityId
     * @return bool
     */
    public function detachLocations($locationIds, int|string $opportunityId): bool
    {
        return $this->repository->detachLocations($locationIds, $opportunityId);
    }

    /**
     * Sync locations for an opportunity (replace all existing relationships).
     *
     * @param int|string $opportunityId
     * @param array $locationIds
     * @param array $data
     * @return void
     */
    public function syncLocations(int|string $opportunityId, array $locationIds, array $data = []): void
    {
        $this->repository->syncLocations($opportunityId, $locationIds, $data);
    }

    /**
     * Check if an opportunity has a specific location.
     *
     * @param int|string $opportunityId
     * @param int|string $locationId
     * @return bool
     */
    public function hasLocation(int|string $opportunityId, int|string $locationId): bool
    {
        return $this->repository->hasLocation($opportunityId, $locationId);
    }

    /**
     * Get opportunity with its locations loaded.
     *
     * @param int|string $opportunityId
     * @return Opportunity|null
     */
    public function getOpportunityWithLocations(int|string $opportunityId): ?Opportunity
    {
        return $this->repository->getOpportunityWithLocations($opportunityId);
    }

    /**
     * Get location with its opportunities loaded.
     *
     * @param int|string $locationId
     * @return Location|null
     */
    public function getLocationWithOpportunities(int|string $locationId): ?Location
    {
        return $this->repository->getLocationWithOpportunities($locationId);
    }

    /**
     * Update pivot data for a specific opportunity-location relationship.
     *
     * @param int|string $opportunityId
     * @param int|string $locationId
     * @param array $data
     * @return bool
     */
    public function updateOpportunityLocationPivot(int|string $opportunityId, int|string $locationId, array $data): bool
    {
        return $this->repository->updateOpportunityLocationPivot($opportunityId, $locationId, $data);
    }

    /**
     * Get pivot data for a specific opportunity-location relationship.
     *
     * @param int|string $opportunityId
     * @param int|string $locationId
     * @return array|null
     */
    public function getOpportunityLocationPivot(int|string $opportunityId, int|string $locationId): ?array
    {
        return $this->repository->getOpportunityLocationPivot($opportunityId, $locationId);
    }

    /**
     * Get locations count for each opportunity.
     *
     * @param array $opportunityIds
     * @return array
     */
    public function getLocationsCount(array $opportunityIds): array
    {
        return $this->repository->getLocationsCount($opportunityIds);
    }
}
