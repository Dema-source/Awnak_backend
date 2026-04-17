<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LocationOpportunity\AttachOpportunityLocationsRequest;
use App\Http\Requests\Api\LocationOpportunity\DetachOpportunityLocationsRequest;
use App\Http\Requests\Api\LocationOpportunity\SyncOpportunityLocationsRequest;
use App\Http\Requests\Api\LocationOpportunity\UpdateOpportunityLocationPivotRequest;
use App\Http\Resources\OpportunityResource;
use App\Http\Resources\LocationResource;
use App\Services\LocationOpportunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationOpportunityController extends Controller
{
    /**
     * LocationOpportunityController constructor.
     *
     * @param LocationOpportunityService $service
     */
    public function __construct(
        protected LocationOpportunityService $service
    ) {}

    /**
     * Display a paginated listing of locations linked to opportunities with relations.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAllWithRelations($filters, $perPage);

        return $this->paginate($data, 'Location opportunities list fetched successfully');
    }

    /**
     * Store (attach) locations to an opportunity.
     *
     * @param AttachOpportunityLocationsRequest $request The validated form request.
     * @param int|string $opportunityId The opportunity to add locations to.
     * @return JsonResponse
     */
    public function store(AttachOpportunityLocationsRequest $request, int|string $opportunityId): JsonResponse
    {
        $user = Auth::user();
        $opportunity = \App\Models\Opportunity::find($opportunityId);

        // Allow super_administrator, system_admin, or opportunity owner
        if (
            !$user->hasRole('super_administrator') && !$user->hasRole('system_admin') &&
            (!$user->organization_profile || $user->organization_profile->id !== $opportunity->organization_profile_id)
        ) {
            return $this->error('Unauthorized to add locations to this opportunity', 403);
        }
        $validated = $request->validated();

        $data = [
            'building_name' => $validated['building_name'] ?? null,
            'floor_number' => $validated['floor_number'] ?? null,
            'apartment_number' => $validated['apartment_number'] ?? null,
            'landmark' => $validated['landmark'] ?? null,
        ];

        $this->service->attachLocations(
            $validated['location_ids'],
            $opportunityId,
            $data
        );

        // Return updated opportunity with locations
        $opportunity = \App\Models\Opportunity::with(['locations.city.country'])->find($opportunityId);

        return $this->success(
            [
                'opportunity' => new OpportunityResource($opportunity),
                'locations' => LocationResource::collection($opportunity->locations)
            ],
            'Locations attached to opportunity successfully'
        );
    }

    /**
     * Display specified opportunity's locations.
     *
     * @param int|string $opportunityId The opportunity ID.
     * @return JsonResponse
     */
    public function show(int|string $opportunityId): JsonResponse
    {
        $items = $this->service->getLocationsByOpportunity($opportunityId);

        return $this->success(
            LocationResource::collection($items),
            'Opportunity locations fetched successfully'
        );
    }

    /**
     * Update (sync) locations for an opportunity.
     *
     * @param SyncOpportunityLocationsRequest $request The validated form request.
     * @param int|string $opportunityId The opportunity to sync locations for.
     * @return JsonResponse
     */
    public function update(SyncOpportunityLocationsRequest $request, int|string $opportunityId): JsonResponse
    {
        $user = Auth::user();
        $opportunity = \App\Models\Opportunity::find($opportunityId);

        // Allow super_administrator, system_admin, or opportunity owner
        if (
            !$user->hasRole('super_administrator') && !$user->hasRole('system_admin') &&
            (!$user->organization_profile || $user->organization_profile->id !== $opportunity->organization_profile_id)
        ) {
            return $this->error('Unauthorized to sync locations for this opportunity', 403);
        }

        $validated = $request->validated();

        $pivotData = [
            'building_name' => $validated['building_name'] ?? null,
            'floor_number' => $validated['floor_number'] ?? null,
            'apartment_number' => $validated['apartment_number'] ?? null,
            'landmark' => $validated['landmark'] ?? null,
        ];

        $this->service->syncLocations(
            $opportunityId,
            $validated['location_ids'],
            $pivotData
        );

        // Return updated opportunity with locations
        $opportunity = \App\Models\Opportunity::with(['locations.city.country'])->find($opportunityId);

        return $this->success(
            [
                'opportunity' => new OpportunityResource($opportunity),
                'locations' => LocationResource::collection($opportunity->locations)
            ],
            'Locations synced for opportunity successfully'
        );
    }

    /**
     * Remove (detach) locations from an opportunity.
     *
     * @param DetachOpportunityLocationsRequest $request The validated form request.
     * @param int|string $opportunityId The opportunity to remove locations from.
     * @return JsonResponse
     */
    public function destroy(DetachOpportunityLocationsRequest $request, int|string $opportunityId): JsonResponse
    {
        $user = Auth::user();
        $opportunity = \App\Models\Opportunity::find($opportunityId);

        // Allow super_administrator, system_admin, or opportunity owner
        if (
            !$user->hasRole('super_administrator') && !$user->hasRole('system_admin') &&
            (!$user->organization_profile || $user->organization_profile->id !== $opportunity->organization_profile_id)
        ) {
            return $this->error('Unauthorized to detach locations from this opportunity', 403);
        }

        $locationIds = $request->input('location_ids', []);

        // If no specific locations provided, detach all locations
        if (empty($locationIds)) {
            $opportunity = \App\Models\Opportunity::find($opportunityId);
            $allLocationIds = $opportunity->locations()->pluck('locations.id')->toArray();
            $this->service->detachLocations($allLocationIds, $opportunityId);
        } else {
            $this->service->detachLocations($locationIds, $opportunityId);
        }

        // Return updated opportunity with remaining locations
        $opportunity = \App\Models\Opportunity::with(['locations.city.country'])->find($opportunityId);

        return $this->success(
            [
                'opportunity' => new OpportunityResource($opportunity),
                'locations' => LocationResource::collection($opportunity->locations)
            ],
            'Locations detached from opportunity successfully'
        );
    }

    /**
     * Get opportunities for a specific location.
     *
     * @param int|string $locationId The location ID.
     * @return JsonResponse
     */
    public function getOpportunitiesByLocation(int|string $locationId): JsonResponse
    {
        $opportunities = $this->service->getOpportunitiesByLocation($locationId);

        return $this->success(
            OpportunityResource::collection($opportunities),
            'Opportunities for location fetched successfully'
        );
    }

    /**
     * Get opportunity with its locations.
     *
     * @param int|string $opportunityId The opportunity ID.
     * @return JsonResponse
     */
    public function showOpportunity(int|string $opportunityId): JsonResponse
    {
        $opportunityWithLocations = $this->service->getOpportunityWithLocations($opportunityId);

        return $this->success(new OpportunityResource($opportunityWithLocations), 'Opportunity with locations fetched successfully');
    }

    /**
     * Update pivot data for a specific opportunity-location relationship.
     *
     * @param int|string $opportunityId
     * @param int|string $locationId
     * @param UpdateOpportunityLocationPivotRequest $request
     * @return JsonResponse
     */
    public function updatePivot(int|string $opportunityId, int|string $locationId, UpdateOpportunityLocationPivotRequest $request): JsonResponse
    {
        $user = Auth::user();
        $opportunity = \App\Models\Opportunity::find($opportunityId);

        // Allow super_administrator, system_admin, or opportunity owner
        if (
            !$user->hasRole('super_administrator') && !$user->hasRole('system_admin') &&
            (!$user->organization_profile || $user->organization_profile->id !== $opportunity->organization_profile_id)
        ) {
            return $this->error('Unauthorized to sync locations for this opportunity', 403);
        }

        $this->service->updateOpportunityLocationPivot(
            $opportunityId,
            $locationId,
            $request->validated()
        );

        // Get the updated pivot data to return
        $pivotData = $this->service->getOpportunityLocationPivot($opportunityId, $locationId);

        return $this->success($pivotData, 'Location details updated successfully');
    }

    /**
     * Get pivot data for a specific opportunity-location relationship.
     *
     * @param int|string $opportunityId
     * @param int|string $locationId
     * @return JsonResponse
     */
    public function getPivot(int|string $opportunityId, int|string $locationId): JsonResponse
    {
        $data = $this->service->getOpportunityLocationPivot($opportunityId, $locationId);

        return $this->success($data, 'Location details fetched successfully');
    }
}
