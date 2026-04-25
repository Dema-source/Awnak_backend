<?php

namespace App\Repositories\Eloquent;

use App\Models\Country;
use App\Repositories\Interfaces\CountryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CountryRepository implements CountryRepositoryInterface
{
    protected Country $model;

    public function __construct(Country $model)
    {
        $this->model = $model;
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
        return $this->model->filter($filters)->latest()->paginate($perPage);
    }

    /**
     * Retrieve a single record by ID or throw an exception if not found.
     *
     * @param int|string $id
     * @return Country
     */
    public function findById(int|string $id): Country
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new country.
     *
     * @param array $data
     * @return Country
     */
    public function create(array $data): Country
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing country.
     *
     * @param int|string $id
     * @param array $data
     * @return Country
     */
    public function update(int|string $id, array $data): Country
    {
        $country = $this->findById($id);

        $country->update($data);

        return $country->fresh();
    }

    /**
     * Delete a country.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $country = $this->findById($id);

        return $country->delete();
    }

    /**
     * Get active countries only.
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return $this->model->active()->orderBy('name')->get();
    }

    /**
     * Get countries by region.
     *
     * @param string $region
     * @return Collection
     */
    public function getByRegion(string $region): Collection
    {
        return $this->model->where('region', $region)->active()->orderBy('name')->get();
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
        return $this->model->where('name', 'like', '%' . $searchTerm . '%')
            ->active()
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Get country with its cities.
     *
     * @param int|string $id
     * @return Country|null
     */
    public function getWithCities(int|string $id): ?Country
    {
        return $this->model->with('cities')->find($id);
    }

    /**
     * Get countries with cities count.
     *
     * @return Collection
     */
    public function getCountriesWithCitiesCount(): Collection
    {
        return $this->model->withCount('cities')
            ->active()
            ->orderBy('name')
            ->get();
    }
}
