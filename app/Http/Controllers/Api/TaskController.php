<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Task\StoreTaskRequest;
use App\Http\Requests\Api\Task\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * TaskController Constructor.
     *
     * @param TaskService $service.
     */
    public function __construct(
        protected TaskService $service
    ) {}

    /**
     * Display a paginated listing of Tasks.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Task list fetched successfully');
    }

    /**
     * Store a newly created Task in storage.
     *
     * @param StoreTaskRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success($item, 'Task created successfully');
    }

    /**
     * Display the specified Task.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success($item, 'Task fetched successfully');
    }

    /**
     * Update the specified Task in storage.
     * 
     * @param UpdateTaskRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateTaskRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'Task updated successfully');
    }

    /**
     * Remove the specified Task from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Task deleted successfully');
    }
}