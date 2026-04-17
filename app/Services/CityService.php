<?php

namespace App\Services;

use App\Repositories\Interfaces\CityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CityService
{
    protected CityRepositoryInterface $repository;

    public function __construct(CityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all cities with optional filtering and pagination.
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
     * Find a city by its ID.
     *
     * @param int|string $id
     * @return \App\Models\City|null
     */
    public function findById(int|string $id): ?\App\Models\City
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new city.
     *
     * @param array $data
     * @return \App\Models\City
     */
    public function create(array $data): \App\Models\City
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing city.
     *
     * @param int|string $id
     * @param array $data
     * @return \App\Models\City
     */
    public function update(int|string $id, array $data): \App\Models\City
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a city.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get active cities only.
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return $this->repository->getActive();
    }

    /**
     * Get cities by country.
     *
     * @param int|string $countryId
     * @return Collection
     */
    public function getByCountry(int|string $countryId): Collection
    {
        return $this->repository->getByCountry($countryId);
    }

    /**
     * Search cities by name.
     *
     * @param string $searchTerm
     * @param int $limit
     * @return Collection
     */
    public function searchByName(string $searchTerm, int $limit = 10): Collection
    {
        return $this->repository->searchByName($searchTerm, $limit);
    }

    /**
     * Get city with its country.
     *
     * @param int|string $id
     * @return \App\Models\City|null
     */
    public function getWithCountry(int|string $id): ?\App\Models\City
    {
        return $this->repository->getWithCountry($id);
    }

    /**
     * Get cities with location counts and country relationship.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getCitiesWithCounts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getCitiesWithCounts($filters, $perPage);
    }

    /**
     * Get cities within radius of given coordinates.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $radiusKm
     * @return Collection
     */
    public function getCitiesWithinRadius(float $latitude, float $longitude, int $radiusKm): Collection
    {
        return $this->repository->getCitiesWithinRadius($latitude, $longitude, $radiusKm);
    }
}
