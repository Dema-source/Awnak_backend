<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VolunteerCertificate\StoreVolunteerCertificateRequest;
use App\Http\Requests\Api\VolunteerCertificate\UpdateVolunteerCertificateRequest;
use App\Models\Task;
use App\Services\VolunteerCertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VolunteerCertificateController extends Controller
{
    /**
     * VolunteerCertificateController Constructor.
     *
     * @param VolunteerCertificateService $service.
     */
    public function __construct(
        protected VolunteerCertificateService $service
    ) {}

    /**
     * Display a paginated listing of VolunteerCertificates.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Volunteer Certificate list fetched successfully');
    }

    /**
     * Store a newly created VolunteerCertificate in storage.
     *
     * @param StoreVolunteerCertificateRequest $request The validated form request.
     * @param Task $task
     * @return JsonResponse
     */
    public function store(StoreVolunteerCertificateRequest $request, Task $task): JsonResponse
    {
        $data = $request->validated() + ['task_id' => $task->id] + ['volunteer_id' => $task->volunteer->id];

        $item = $this->service->create($data);

        return $this->success($item, 'Volunteer Certificate created successfully');
    }

    /**
     * Display the specified VolunteerCertificate.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success($item, 'Volunteer Certificate fetched successfully');
    }

    /**
     * Update the specified VolunteerCertificate in storage.
     * 
     * @param UpdateVolunteerCertificateRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateVolunteerCertificateRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'Volunteer Certificate updated successfully');
    }

    /**
     * Remove the specified VolunteerCertificate from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Volunteer Certificate deleted successfully');
    }
}
