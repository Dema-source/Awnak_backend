<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrganizationProfile\StoreOrganizationProfileRequest;
use App\Http\Requests\Api\OrganizationProfile\UpdateOrganizationProfileRequest;
use App\Services\OrganizationProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationProfileController extends Controller
{
    /**
     * OrganizationProfileController Constructor.
     *
     * @param OrganizationProfileService $service.
     */
    public function __construct(
        protected OrganizationProfileService $service
    ) {}

    /**
     * Display a paginated listing of OrganizationProfiles.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'OrganizationProfile list fetched successfully');
    }

    /**
     * Store a newly created OrganizationProfile in storage.
     *
     * @param StoreOrganizationProfileRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreOrganizationProfileRequest $request): JsonResponse
    {
        $data = $request->validated() + ['user_id' => Auth::user()->id];

        $item = $this->service->create($data);

        return $this->success($item, 'OrganizationProfile created successfully');
    }

    /**
     * Display the specified OrganizationProfile.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success($item, 'OrganizationProfile fetched successfully');
    }

    /**
     * Update the specified OrganizationProfile in storage.
     * 
     * @param UpdateOrganizationProfileRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateOrganizationProfileRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'OrganizationProfile updated successfully');
    }

    /**
     * Remove the specified OrganizationProfile from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'OrganizationProfile deleted successfully');
    }
}
