<?php

namespace App\Repositories\Interfaces;

use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CountryRepositoryInterface
{
    /**
     * Get all countries with optional filtering and pagination.
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
     * @return Country
     */
    public function findById(int|string $id): Country;

    /**
     * Create a new country.
     *
     * @param array $data
     * @return Country
     */
    public function create(array $data): Country;

    /**
     * Update an existing country.
     *
     * @param int|string $id
     * @param array $data
     * @return Country
     */
    public function update(int|string $id, array $data): Country;

    /**
     * Delete a country.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Get active countries only.
     *
     * @return Collection
     */
    public function getActive(): Collection;

    /**
     * Get countries by region.
     *
     * @param string $region
     * @return Collection
     */
    public function getByRegion(string $region): Collection;

    /**
     * Search countries by name.
     *
     * @param string $searchTerm
     * @param int $limit
     * @return Collection
     */
    public function searchByName(string $searchTerm, int $limit = 10): Collection;

    /**
     * Get country with its cities.
     *
     * @param int|string $id
     * @return Country|null
     */
    public function getWithCities(int|string $id): ?Country;

    /**
     * Get countries with cities count.
     *
     * @return Collection
     */
    public function getCountriesWithCitiesCount(): Collection;
}
