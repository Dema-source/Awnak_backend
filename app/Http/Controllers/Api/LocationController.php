<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Location\StoreLocationRequest;
use App\Http\Requests\Api\Location\UpdateLocationRequest;
use App\Models\Opportunity;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        return $this->paginate($data, 'Location list fetched successfully');
    }

    /**
     * Store a newly created Location in storage.
     *
     * @param StoreLocationRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreLocationRequest $request, Opportunity $opportunity): JsonResponse
    {
        $data = $request->validated() + ['opportunity_id' =>$opportunity->id];

        $item = $this->service->create($data);

        return $this->success($item, 'Location created successfully');
    }

    /**
     * Display the specified Location.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success($item, 'Location fetched successfully');
    }

    /**
     * Update the specified Location in storage.
     * 
     * @param UpdateLocationRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateLocationRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'Location updated successfully');
    }

    /**
     * Remove the specified Location from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Location deleted successfully');
    }
}
