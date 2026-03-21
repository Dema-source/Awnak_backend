<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TaskHour\StoreTaskHourRequest;
use App\Http\Requests\Api\TaskHour\UpdateTaskHourRequest;
use App\Services\TaskHourService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskHourController extends Controller
{
    /**
     * TaskHourController Constructor.
     *
     * @param TaskHourService $service.
     */
    public function __construct(
        protected TaskHourService $service
    ) {}

    /**
     * Display a paginated listing of TaskHours.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'TaskHour list fetched successfully');
    }

    /**
     * Store a newly created TaskHour in storage.
     *
     * @param StoreTaskHourRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreTaskHourRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success($item, 'TaskHour created successfully');
    }

    /**
     * Display the specified TaskHour.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success($item, 'TaskHour fetched successfully');
    }

    /**
     * Update the specified TaskHour in storage.
     * 
     * @param UpdateTaskHourRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateTaskHourRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'TaskHour updated successfully');
    }

    /**
     * Remove the specified TaskHour from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'TaskHour deleted successfully');
    }
}