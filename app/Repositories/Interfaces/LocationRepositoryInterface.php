<?php

namespace App\Repositories\Interfaces;

use App\Models\Location;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface LocationRepositoryInterface
 *
 * Defines the contract for CRUD operations and location-specific queries.
 */
interface LocationRepositoryInterface
{
    /**
     * Retrieve a paginated list of records with optional provided conditions.
     *
     * @param array $filters [Key => value] filters.
     * @param int $perPage size of items in each page.
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a record by its ID.
     *
     * @param int|string $id The primary key value.
     * @return Location
     */
    public function findById(int|string $id): Location;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return Location
     */
    public function create(array $data): Location;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Location
     */
    public function update(int|string $id, array $data): Location;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Get locations by city.
     *
     * @param int|string $cityId
     * @return Collection
     */
    public function getByCity(int|string $cityId): Collection;

    /**
     * Get locations by country.
     *
     * @param int|string $countryId
     * @return Collection
     */
    public function getByCountry(int|string $countryId): Collection;

    /**
     * Get location with city and country relationships.
     *
     * @param int|string $id
     * @return Location|null
     */
    public function getWithRelations(int|string $id): ?Location;

    /**
     * Get locations within radius of given coordinates.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $radiusKm
     * @return Collection
     */
    public function getLocationsWithinRadius(float $latitude, float $longitude, int $radiusKm): Collection;

    /**
     * Search locations by city name, country name, or coordinates.
     *
     * @param string $data
     * @param int $limit
     * @return Collection
     */
    public function searchByAddress(string $data, int $limit = 10): Collection;

    /**
     * Get locations with opportunity relationship.
     *
     * @param array $filters
     * @return Collection
     */
    public function getWithOpportunity(array $filters = []): Collection;
}