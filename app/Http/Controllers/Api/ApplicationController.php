<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Application\StoreApplicationRequest;
use App\Http\Requests\Api\Application\UpdateApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Opportunity;
use App\Services\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * ApplicationController Constructor.
     *
     * @param ApplicationService $service.
     */
    public function __construct(
        protected ApplicationService $service
    ) {}

    /**
     * Display a paginated listing of Applications.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Application list fetched successfully');
    }

    /**
     * Store a newly created Application in storage.
     *
     * @param Opportunity $opportunity The Opportunity model injected by Laravel..
     * @return JsonResponse
     */
    public function store(Opportunity $opportunity): JsonResponse
    {
        $data = ['opportunity_id' => $opportunity->id] + ['volunteer_id' => Auth::user()->profile->volunteer->id];

        $item = $this->service->create($data);

        return $this->success(new ApplicationResource($item), 'Application created successfully');
    }

    /**
     * Display the specified Application.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new ApplicationResource($item), 'Application fetched successfully');
    }

    /**
     * Update STATUS for specified Application in storage.
     * 
     * @param UpdateApplicationRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateApplicationRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success(new ApplicationResource($item), 'Application updated successfully');
    }

    /**
     * Remove the specified Application from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Application deleted successfully');
    }
}
