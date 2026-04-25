<?php

namespace App\Repositories\Eloquent;

use App\Models\Location;
use App\Repositories\Interfaces\LocationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LocationRepository implements LocationRepositoryInterface
{
    /**
     * LocationRepository constructor.
     *
     * @param Location $model
     */
    public function __construct(
        protected Location $model
    ) {}

    /**
     * Get a paginated list of records applying optional filters.
     *
     * @param array $filters Key/value filters to apply to the query.
     * @param int $perPage Number of items per page.
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
     * @return Location
     */
    public function findById(int|string $id): Location
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new location.
     *
     * @param array $data
     * @return Location
     */
    public function create(array $data): Location
    {
        return $this->model->create($data);
    }

    /**
     * Update a location.
     *
     * @param int|string $id
     * @param array $data
     * @return Location
     */
    public function update(int|string $id, array $data): Location
    {
        $item = $this->model->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    /**
     * Delete a location.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $item = $this->model->find($id);

        return (bool) $item->delete();

    }

    /**
     * Get locations by city.
     *
     * @param int|string $cityId
     * @return Collection
     */
    public function getByCity(int|string $cityId): Collection
    {
        return $this->model->byCity($cityId)->with('city.country')->get();
    }

    /**
     * Get locations by country.
     *
     * @param int|string $countryId
     * @return Collection
     */
    public function getByCountry(int|string $countryId): Collection
    {
        return $this->model->byCountry($countryId)->with('city.country')->get();
    }

    /**
     * Get location with city and country relationships.
     *
     * @param int|string $id
     * @return Location|null
     */
    public function getWithRelations(int|string $id): ?Location
    {
        return $this->model->withCityAndCountry()->find($id);
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
        return $this->model->withinRadius($latitude, $longitude, $radiusKm)
            ->with('city.country')
            ->get();
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
        return $this->model->where(function ($query) use ($data) {
                $query->whereHas('city', function ($cityQuery) use ($data) {
                    $cityQuery->where('name', 'like', '%' . $data . '%');
                })
                ->orWhereHas('city.country', function ($countryQuery) use ($data) {
                    $countryQuery->where('name', 'like', '%' . $data . '%');
                })
                ->orWhere('latitude', 'like', '%' . $data . '%')
                ->orWhere('longitude', 'like', '%' . $data . '%');
            })
            ->with('city.country')
            ->limit($limit)
            ->get();
    }

    /**
     * Get locations with opportunity relationship.
     *
     * @param array $filters
     * @return Collection
     */
    public function getWithOpportunity(array $filters = []): Collection
    {
        $query = $this->model->withCityAndCountry()->withOpportunities();

        if (isset($filters['city_id'])) {
            $query->byCity($filters['city_id']);
        }

        if (isset($filters['country_id'])) {
            $query->byCountry($filters['country_id']);
        }

        if (isset($filters['opportunity_id'])) {
            $query->byOpportunity($filters['opportunity_id']);
        }

        return $query->get();
    }
}
