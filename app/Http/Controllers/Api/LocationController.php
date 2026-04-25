<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Http\Requests\Api\Location\StoreLocationRequest;
use App\Http\Requests\Api\Location\UpdateLocationRequest;
use App\Http\Requests\Api\Location\WithinRadiusRequest;
use App\Services\LocationService;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    /**
     * LocationController Constructor.
     *
     * @param LocationService $service.
     */
    public function __construct(
        protected LocationService $service
    ) {}

    /**
     * Display a paginated listing of Locations.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate(
            LocationResource::collection($data),
            'Location list fetched successfully'
        );
    }

    /**
     * Store a newly created Location in storage.
     *
     * @param StoreLocationRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreLocationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $item = $this->service->create($data);

        return $this->success(new LocationResource($item), 'Location created successfully');
    }

    /**
     * Display the specified Location.
     *
     * @param int $id The location ID.
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new LocationResource($item), 'Location fetched successfully');
    }

    /**
     * Update the specified Location in storage.
     *
     * @param UpdateLocationRequest $request The validated form request.
     * @param int $id The location ID.
     * @return JsonResponse
     */
    public function update(UpdateLocationRequest $request, int $id): JsonResponse
    {
        $location = $this->service->findById($id);
        
        // Check if user is organization admin and if location belongs to their organization
        if (Auth::user()->hasRole('organization_admin')) {
            $this->authorizeLocationAccess($location);
        }

        $item = $this->service->update($id, $request->validated());

        return $this->success(new LocationResource($item), 'Location updated successfully');
    }

    /**
     * Remove the specified Location from storage.
     *
     * @param int $id The location ID.
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $location = $this->service->findById($id);
        
        // Check if user is organization admin and if location belongs to their organization
        if (Auth::user()->hasRole('organization_admin')) {
            $this->authorizeLocationAccess($location);
        }

        $this->service->delete($id);

        return $this->success(null, 'Location deleted successfully');
    }

    /**
     * Authorize organization admin to access location.
     *
     * @param Location $location
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function authorizeLocationAccess(Location $location): void
    {
        $user = Auth::user();
        $organizationId = $user->organization_profile?->id;

        if (!$organizationId) {
            abort(403, 'You are not associated with an organization');
        }

        // Check if location is associated with any of the organization's opportunities
        $hasAccess = $location->opportunities()
            ->where('organization_profile_id', $organizationId)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You can only manage locations associated with your organization\'s opportunities');
        }
    }

    /**
     * Get locations within a specific radius of coordinates.
     *
     * @param WithinRadiusRequest $request The validated form request.
     * @return JsonResponse
     */
    public function withinRadius(WithinRadiusRequest $request): JsonResponse
    {
        $data = $request->validated();

        $items = $this->service->getLocationsWithinRadius(
            $data['latitude'],
            $data['longitude'],
            $data['radius']
        );

        return $this->success(
            LocationResource::collection($items),
            'Locations within radius fetched successfully'
        );
    }

    /**
     * Search locations by city name, country name, or coordinates.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function searchByAddress(Request $request): JsonResponse
    {
        $data = $request->input('search', '');
        $limit = (int) $request->input('limit', 10);

        $items = $this->service->searchByAddress($data, $limit);

        return $this->success(
            LocationResource::collection($items),
            'Locations search results fetched successfully'
        );
    }

    /**
     * Get locations with opportunity relationship.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function withOpportunity(Request $request): JsonResponse
    {
        $filters = $request->only(['city_id', 'country_id', 'opportunity_id']);

        $items = $this->service->getWithOpportunity($filters);

        return $this->success(
            LocationResource::collection($items),
            'Locations with opportunities fetched successfully'
        );
    }

    /**
     * Get locations by city.
     *
     * @param int $cityId The city ID.
     * @return JsonResponse
     */
    public function getByCity(int $cityId): JsonResponse
    {
        $items = $this->service->getByCity($cityId);

        return $this->success(
            LocationResource::collection($items),
            'Locations by city fetched successfully'
        );
    }

    /**
     * Get locations by country.
     *
     * @param int $countryId The country ID.
     * @return JsonResponse
     */
    public function getByCountry(int $countryId): JsonResponse
    {
        $items = $this->service->getByCountry($countryId);

        return $this->success(
            LocationResource::collection($items),
            'Locations by country fetched successfully'
        );
    }
}
