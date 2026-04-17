<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Requests\Api\City\StoreCityRequest;
use App\Http\Requests\Api\City\UpdateCityRequest;
use App\Http\Requests\Api\City\WithinRadiusRequest;
use App\Services\CityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * CityController Constructor.
     *
     * @param CityService $service
     */
    public function __construct(
        protected CityService $service
    ) {}

    /**
     * Display a paginated listing of Cities.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'City list fetched successfully');
    }

    /**
     * Store a newly created City in storage.
     *
     * @param Request $request The HTTP request containing city data.
     * @return JsonResponse
     */
    public function store(StoreCityRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success(new CityResource($item), 'City created successfully');
    }

    /**
     * Display the specified City.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new CityResource($item), 'City fetched successfully');
    }

    /**
     * Update the specified City in storage.
     * 
     * @param Request $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateCityRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success(new CityResource($item), 'City updated successfully');
    }

    /**
     * Remove the specified City from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
         $this->service->delete($id);

        return $this->success(null, 'City deleted successfully');
    }

    /**
     * Get active cities only.
     *
     * @return JsonResponse
     */
    public function active(): JsonResponse
    {
        $cities = $this->service->getActive();

        return $this->success(CityResource::collection($cities), 'Active cities fetched successfully');
    }

    /**
     * Get cities by country.
     *
     * @param int|string $countryId
     * @return JsonResponse
     */
    public function byCountry(int|string $countryId): JsonResponse
    {
        $cities = $this->service->getByCountry($countryId);

        return $this->success(CityResource::collection($cities), 'Cities by country fetched successfully');
    }

    /**
     * Search cities by name.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|max:100',
            'limit' => 'sometimes|integer|min:1|max:50'
        ]);

        $limit = $validated['limit'] ?? 10;
        $cities = $this->service->searchByName($validated['q'], $limit);

        return $this->success(CityResource::collection($cities), 'Cities search completed successfully');
    }

    /**
     * Get city with its country.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function withCountry(int|string $id): JsonResponse
    { 
        $city = $this->service->getWithCountry($id);

        return $this->success(new CityResource($city), 'City with country fetched successfully');
    }

    /**
     * Get cities with country and locations count.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function withCounts(Request $request): JsonResponse
    {
        $filters = $request->only(['is_active', 'country_id']);
        $cities = $this->service->getCitiesWithCounts($filters);

        return $this->success(CityResource::collection($cities), 'Cities with counts fetched successfully');
    }

    /**
     * Get cities within radius of given coordinates.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function withinRadius(WithinRadiusRequest $request): JsonResponse
    {
        $cities = $this->service->getCitiesWithinRadius(
            (float) $request->validated()['latitude'],
            (float) $request->validated()['longitude'],
            (int) $request->validated()['radius']
        );

        return $this->success(CityResource::collection($cities), 'Cities within radius fetched successfully');
    }
}
