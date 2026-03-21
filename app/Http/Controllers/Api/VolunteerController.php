<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Volunteer\StoreVolunteerRequest;
use App\Http\Requests\Api\Volunteer\UpdateVolunteerRequest;
use App\Services\VolunteerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    /**
     * VolunteerController Constructor.
     *
     * @param VolunteerService $service.
     */
    public function __construct(
        protected VolunteerService $service
    ) {}

    /**
     * Display a paginated listing of Volunteers.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Volunteer list fetched successfully');
    }

    /**
     * Store a newly created Volunteer in storage.
     *
     * @param StoreVolunteerRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreVolunteerRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success($item, 'Volunteer created successfully');
    }

    /**
     * Display the specified Volunteer.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success($item, 'Volunteer fetched successfully');
    }

    /**
     * Update the specified Volunteer in storage.
     * 
     * @param UpdateVolunteerRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateVolunteerRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'Volunteer updated successfully');
    }

    /**
     * Remove the specified Volunteer from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Volunteer deleted successfully');
    }
}