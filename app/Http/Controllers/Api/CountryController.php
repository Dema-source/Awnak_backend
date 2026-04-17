<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Http\Requests\Api\Country\StoreCountryRequest;
use App\Http\Requests\Api\Country\UpdateCountryRequest;
use App\Services\CountryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    /**
     * CountryController Constructor.
     *
     * @param CountryService $service
     */
    public function __construct(
        protected CountryService $service
    ) {}

    /**
     * Display a paginated listing of Countries.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Country list fetched successfully');
    }

    /**
     * Store a newly created Country in storage.
     *
     * @param StoreCountryRequest $request The HTTP request containing country data.
     * @return JsonResponse
     */
    public function store(StoreCountryRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success(new CountryResource($item), 'Country created successfully');
    }

    /**
     * Display the specified Country.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new CountryResource($item), 'Country fetched successfully');
    }

    /**
     * Update the specified Country in storage.
     * 
     * @param Request $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateCountryRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success(new CountryResource($item), 'Country updated successfully');
    }

    /**
     * Remove the specified Country from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Country deleted successfully');
    }

    /**
     * Get active countries only.
     *
     * @return JsonResponse
     */
    public function active(): JsonResponse
    {
        $countries = $this->service->getActive();

        return $this->success(CountryResource::collection($countries), 'Active countries fetched successfully');
    }

    /**
     * Get countries by region.
     *
     * @param string $region
     * @return JsonResponse
     */
    public function byRegion(string $region): JsonResponse
    {
        $countries = $this->service->getByRegion($region);

        return $this->success(CountryResource::collection($countries), 'Countries by region fetched successfully');
    }

    /**
     * Search countries by name.
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
        $countries = $this->service->searchByName($validated['q'], $limit);

        return $this->success(CountryResource::collection($countries), 'Countries search completed successfully');
    }

    /**
     * Get country with its cities.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function withCities(int|string $id): JsonResponse
    {
        $country = $this->service->getWithCities($id);

        return $this->success(new CountryResource($country), 'Country with cities fetched successfully');
    }

    /**
     * Get countries with cities count.
     *
     * @return JsonResponse
     */
    public function withCitiesCount(): JsonResponse
    {
        $countries = $this->service->getCountriesWithCitiesCount();

        return $this->success(CountryResource::collection($countries), 'Countries with cities count fetched successfully');
    }
}
