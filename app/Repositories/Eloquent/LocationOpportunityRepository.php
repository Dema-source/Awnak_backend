<?php

namespace App\Repositories\Eloquent;

use App\Models\Location;
use App\Models\Opportunity;
use App\Repositories\Interfaces\LocationOpportunityRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LocationOpportunityRepository implements LocationOpportunityRepositoryInterface
{
    /**
     * LocationOpportunityRepository Constructor.
     *
     * @param Opportunity $opportunityModel
     * @param Location $locationModel
     */
    public function __construct(
        protected Opportunity $opportunityModel,
        protected Location $locationModel
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
        $query = $this->locationModel->with(['opportunities' => function($query) {
            $query->withPivot(['building_name', 'floor_number', 'apartment_number', 'landmark']);
        }, 'city.country'])
        ->whereHas('opportunities');
        
        // Apply filters if provided
        if (isset($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }
        if (isset($filters['country_id'])) {
            $query->whereHas('city', function ($q) use ($filters) {
                $q->where('country_id', $filters['country_id']);
            });
        }
        if (isset($filters['opportunity_id'])) {
            $query->whereHas('opportunities', function ($q) use ($filters) {
                $q->where('opportunities.id', $filters['opportunity_id']);
            });
        }
        
        return $query->paginate($perPage);
    }

    /**
     * Get locations for a specific opportunity.
     *
     * @param int|string $opportunityId
     * @return Collection
     */
    public function getLocationsByOpportunity(int|string $opportunityId): Collection
    {
        return $this->opportunityModel->findOrFail($opportunityId)
            ->locations()
            ->withPivot(['building_name', 'floor_number', 'apartment_number', 'landmark'])
            ->get();
    }

    /**
     * Get all opportunities for a specific location.
     *
     * @param int|string $locationId
     * @return Collection
     */
    public function getOpportunitiesByLocation(int|string $locationId): Collection
    {
        return $this->locationModel->findOrFail($locationId)
            ->opportunities()
            ->withPivot(['building_name', 'floor_number', 'apartment_number', 'landmark'])
            ->get();
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
            $opportunity = $this->opportunityModel->findOrFail($opportunityId);

            // Validate that all location IDs exist
            $existingLocations = $this->locationModel->whereIn('id', $locationIds)->pluck('id')->toArray();
            $invalidLocations = array_diff($locationIds, $existingLocations);

            if (!empty($invalidLocations)) {
                throw new \InvalidArgumentException('Invalid location IDs: ' . implode(', ', $invalidLocations));
            }

            // Prepare pivot data for each location
            $attachData = [];
            foreach ($locationIds as $locationId) {
                $attachData[$locationId] = $data; 
            }

            $opportunity->locations()->attach($attachData);
            return true;
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
            $opportunity = $this->opportunityModel->findOrFail($opportunityId);
            $opportunity->locations()->detach($locationIds);
            return true;
    }

    /**
     * Sync locations for an opportunity (detaches all current and attaches new ones).
     *
     * @param int|string $opportunityId
     * @param array $locationIds
     * @param array $data
     * @return void
     */
    public function syncLocations(int|string $opportunityId, array $locationIds, array $data = []): void
    {
        $opportunity = $this->opportunityModel->findOrFail($opportunityId);

        // Validate that all location IDs exist
        $existingLocations = $this->locationModel->whereIn('id', $locationIds)->pluck('id')->toArray();
        $invalidLocations = array_diff($locationIds, $existingLocations);

        if (!empty($invalidLocations)) {
            throw new \InvalidArgumentException('Invalid location IDs: ' . implode(', ', $invalidLocations));
        }

        // Prepare pivot data for each location
        $syncData = [];
        foreach ($locationIds as $locationId) {
            $syncData[$locationId] = $data;
        }

        $opportunity->locations()->sync($syncData);
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
        return $this->opportunityModel->findOrFail($opportunityId)
            ->locations()
            ->where('locations.id', $locationId)
            ->exists();
    }

    /**
     * Get opportunity with its locations loaded.
     *
     * @param int|string $opportunityId
     * @return Opportunity|null
     */
    public function getOpportunityWithLocations(int|string $opportunityId): ?Opportunity
    {
        return $this->opportunityModel->with(['locations.city.country'])->find($opportunityId);
    }

    /**
     * Get location with its opportunities loaded.
     *
     * @param int|string $locationId
     * @return Location|null
     */
    public function getLocationWithOpportunities(int|string $locationId): ?Location
    {
        return $this->locationModel->with(['opportunities'])->find($locationId);
    }

    /**
     * Get locations count for each opportunity.
     *
     * @param array $opportunityIds
     * @return array
     */
    public function getLocationsCount(array $opportunityIds): array
    {
        return $this->opportunityModel->whereIn('id', $opportunityIds)
            ->withCount('locations')
            ->pluck('locations_count', 'id')
            ->toArray();
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
            $opportunity = $this->opportunityModel->findOrFail($opportunityId);
            
            // Check if the relationship exists
            if (!$this->hasLocation($opportunityId, $locationId)) {
                return false;
            }

            $opportunity->locations()->updateExistingPivot($locationId, $data);
            return true;
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
        $opportunity = $this->opportunityModel->findOrFail($opportunityId);
        $location = $opportunity->locations()->where('locations.id', $locationId)->first();

        if (!$location) {
            return null;
        }

        return $location->pivot->toArray();
    }
}
