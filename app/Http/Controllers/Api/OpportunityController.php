<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Opportunity\StoreOpportunityRequest;
use App\Http\Requests\Api\Opportunity\UpdateOpportunityRequest;
use App\Services\OpportunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpportunityController extends Controller
{
    /**
     * OpportunityController Constructor.
     *
     * @param OpportunityService $service.
     */
    public function __construct(
        protected OpportunityService $service
    ) {}

    /**
     * Display a paginated listing of Opportunitys.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Opportunity list fetched successfully');
    }

    /**
     * Store a newly created Opportunity in storage.
     *
     * @param StoreOpportunityRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreOpportunityRequest $request): JsonResponse
    {
        $data = $request->validated();

        $item = $this->service->create($data);

        return $this->success($item, 'Opportunity created successfully');
    }

    /**
     * Display the specified Opportunity.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success($item, 'Opportunity fetched successfully');
    }

    /**
     * Update the specified Opportunity in storage.
     * 
     * @param UpdateOpportunityRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateOpportunityRequest $request, int|string $id): JsonResponse
    {
        $opportunity = $this->service->findById($id);

        $user = Auth::user();

        // Check if user is super_administrator or system_admin or the owner of the opportunity
        if (
            !$user->hasRole('super_administrator') && !$user->hasRole('system_admin') &&
            (!$user->organization_profile || $user->organization_profile->id !== $opportunity->organization_profile_id)
        ) {
            return $this->error('Unauthorized to update this opportunity', 403);
        }

        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'Opportunity updated successfully');
    }

    /**
     * Remove the specified Opportunity from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $opportunity = $this->service->findById($id);

        $user = Auth::user();

        // Check if user is super_administrator or system_admin or the owner of the opportunity
        if (
            !$user->hasRole('super_administrator') && !$user->hasRole('system_admin') &&
            (!$user->organization_profile || $user->organization_profile->id !== $opportunity->organization_profile_id)
        ) {
            return $this->error('Unauthorized to delete this opportunity', 403);
        }
        $this->service->delete($id);

        return $this->success(null, 'Opportunity deleted successfully');
    }

    /**
     * Store a newly created Opportunity in storage by specific Organization.
     * 
     * @param StoreOpportunityRequest $request
     * @return JsonResponse
     */
    public function storeOpportunity(StoreOpportunityRequest $request): JsonResponse
    {
        $data = $request->validated() + ['organization_profile_id' => Auth::user()->organization_profile->id];

        $item = $this->service->create($data);

        return $this->success($item, 'Opportunity created successfully');
    }
}
