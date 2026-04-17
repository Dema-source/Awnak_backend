<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Badge\StoreBadgeRequest;
use App\Http\Requests\Api\Badge\UpdateBadgeRequest;
use App\Http\Resources\BadgeResource;
use App\Models\Volunteer;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /**
     * BadgeController Constructor.
     *
     * @param BadgeService $service.
     */
    public function __construct(
        protected BadgeService $service
    ) {}

    /**
     * Display a paginated listing of Badges.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Badge list fetched successfully');
    }

    /**
     * Store a newly created Badge in storage.
     *
     * @param StoreBadgeRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreBadgeRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success(new BadgeResource($item), 'Badge created successfully');
    }

    /**
     * Display the specified Badge.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new BadgeResource($item), 'Badge fetched successfully');
    }

    /**
     * Update the specified Badge in storage.
     * 
     * @param UpdateBadgeRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateBadgeRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success(new BadgeResource($item), 'Badge updated successfully');
    }

    /**
     * Remove the specified Badge from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Badge deleted successfully');
    }
}