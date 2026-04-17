<?php

namespace App\Repositories\Interfaces;

use App\Models\City;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CityRepositoryInterface
{
    /**
     * Get all cities with optional filtering and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a record by its ID.
     *
     * @param int|string $id The primary key value.
     * @return City
     */
    public function findById(int|string $id): City;

    /**
     * Create a new city.
     *
     * @param array $data
     * @return City
     */
    public function create(array $data): City;

    /**
     * Update an existing city.
     *
     * @param int|string $id
     * @param array $data
     * @return City
     */
    public function update(int|string $id, array $data): City;

    /**
     * Delete a city.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Get active cities only.
     *
     * @return Collection
     */
    public function getActive(): Collection;

    /**
     * Get cities by country.
     *
     * @param int|string $countryId
     * @return Collection
     */
    public function getByCountry(int|string $countryId): Collection;

    /**
     * Search cities by name.
     *
     * @param string $searchTerm
     * @param int $limit
     * @return Collection
     */
    public function searchByName(string $searchTerm, int $limit = 10): Collection;

    /**
     * Get city with its country.
     *
     * @param int|string $id
     * @return City|null
     */
    public function getWithCountry(int|string $id): ?City;

    /**
     * Get cities with location counts and country relationship.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getCitiesWithCounts(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get cities by coordinates within radius.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $radiusKm
     * @return Collection
     */
    public function getCitiesWithinRadius(float $latitude, float $longitude, int $radiusKm): Collection;
}
