<?php

namespace App\Services;

use App\Repositories\Interfaces\LocationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service layer for handling business logic related to location management.
 */
class LocationService
{
    protected LocationRepositoryInterface $repository;

    public function __construct(LocationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

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
     * @return \App\Models\Location|null
     */
    public function findById(int|string $id): ?\App\Models\Location
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new record using the provided data.
     *
     * @param array $data
     * @return \App\Models\Location
     */
    public function create(array $data): \App\Models\Location
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing record by ID with the given data.
     *
     * @param int|string $id
     * @param array $data
     * @return \App\Models\Location
     */
    public function update(int|string $id, array $data): \App\Models\Location
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
     * Get locations by city.
     *
     * @param int|string $cityId
     * @return Collection
     */
    public function getByCity(int|string $cityId): Collection
    {
        return $this->repository->getByCity($cityId);
    }

    /**
     * Get locations by country.
     *
     * @param int|string $countryId
     * @return Collection
     */
    public function getByCountry(int|string $countryId): Collection
    {
        return $this->repository->getByCountry($countryId);
    }

    /**
     * Get location with city, country and opportunity relationships.
     *
     * @param int|string $id
     * @return \App\Models\Location|null
     */
    public function getWithRelations(int|string $id): ?\App\Models\Location
    {
        return $this->repository->getWithRelations($id);
    }

    /**
     * Get locations within radius of given coordinates.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $radiusKm
     * @return Collection
     */
    public function getLocationsWithinRadius(float $latitude, float $longitude, int $radiusKm): Collection
    {
        return $this->repository->getLocationsWithinRadius($latitude, $longitude, $radiusKm);
    }

    /**
     * Search locations by city name, country name, or coordinates.
     *
     * @param string $data
     * @param int $limit
     * @return Collection
     */
    public function searchByAddress(string $data, int $limit = 10): Collection
    {
        return $this->repository->searchByAddress($data, $limit);
    }

    /**
     * Get locations with opportunity relationship.
     *
     * @param array $filters
     * @return Collection
     */
    public function getWithOpportunity(array $filters = []): Collection
    {
        return $this->repository->getWithOpportunity($filters);
    }
}