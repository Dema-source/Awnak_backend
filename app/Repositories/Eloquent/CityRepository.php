<?php

namespace App\Repositories\Eloquent;

use App\Models\City;
use App\Repositories\Interfaces\CityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CityRepository implements CityRepositoryInterface
{
    protected City $model;

    public function __construct(City $model)
    {
        $this->model = $model;
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
        return $this->model->filter($filters)->latest()->paginate($perPage);
    }

    /**
     * Retrieve a single record by ID or throw an exception if not found.
     *
     * @param int|string $id
     * @return City
     */
    public function findById(int|string $id): City
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new city.
     *
     * @param array $data
     * @return City
     */
    public function create(array $data): City
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing city.
     *
     * @param int|string $id
     * @param array $data
     * @return City
     */
    public function update(int|string $id, array $data): City
    {
        $city = $this->findById($id);
        
        $city->update($data);
        
        return $city->fresh();
    }

    /**
     * Delete a city.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $city = $this->findById($id);

        return $city->delete();
    }

    /**
     * Get active cities only.
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return $this->model->active()->with('country')->orderBy('name')->get();
    }

    /**
     * Get cities by country.
     *
     * @param int|string $countryId
     * @return Collection
     */
    public function getByCountry(int|string $countryId): Collection
    {
        return $this->model->where('country_id', $countryId)
            ->active()
            ->orderBy('name')
            ->get();
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
        return $this->model->where('name', 'like', '%' . $searchTerm . '%')
            ->active()
            ->with('country')
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Get city with its country.
     *
     * @param int|string $id
     * @return City|null
     */
    public function getWithCountry(int|string $id): ?City
    {
        return $this->model->with('country')->find($id);
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
        $query = $this->model->withCount('locations')->with('country');

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        return $query->orderBy('name')->paginate($perPage);
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
        // Using Haversine formula
        $cities = DB::table('cities')
            ->selectRaw('*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radiusKm)
            ->where('is_active', true)
            ->orderBy('distance')
            ->get();

        return $this->model->hydrate($cities->toArray())->load('country');
    }
}
