<?php

namespace App\Services;

use App\Repositories\Interfaces\CountryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CountryService
{
    protected CountryRepositoryInterface $repository;

    public function __construct(CountryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all countries with optional filtering and pagination.
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
     * Find a country by its ID.
     *
     * @param int|string $id
     * @return \App\Models\Country|null
     */
    public function findById(int|string $id): ?\App\Models\Country
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new country.
     *
     * @param array $data
     * @return \App\Models\Country
     */
    public function create(array $data): \App\Models\Country
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing country.
     *
     * @param int|string $id
     * @param array $data
     * @return \App\Models\Country
     */
    public function update(int|string $id, array $data): \App\Models\Country
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a country.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get active countries only.
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return $this->repository->getActive();
    }

    /**
     * Get countries by region.
     *
     * @param string $region
     * @return Collection
     */
    public function getByRegion(string $region): Collection
    {
        return $this->repository->getByRegion($region);
    }

    /**
     * Search countries by name.
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
     * Get country with its cities.
     *
     * @param int|string $id
     * @return \App\Models\Country|null
     */
    public function getWithCities(int|string $id): ?\App\Models\Country
    {
        return $this->repository->getWithCities($id);
    }

    /**
     * Get countries with cities count.
     *
     * @return Collection
     */
    public function getCountriesWithCitiesCount(): Collection
    {
        return $this->repository->getCountriesWithCitiesCount();
    }
}
