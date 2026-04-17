<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Skill\StoreSkillRequest;
use App\Http\Requests\Api\Skill\UpdateSkillRequest;
use App\Services\SkillService;
use App\Http\Resources\SkillResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * SkillController Constructor.
     *
     * @param SkillService $service.
     */
    public function __construct(
        protected SkillService $service
    ) {}

    /**
     * Display a paginated listing of Skills.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Skill list fetched successfully');
    }

    /**
     * Store a newly created Skill in storage.
     *
     * @param StoreSkillRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreSkillRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success(['skill' => new SkillResource($item)], 'Skill created successfully');
    }

    /**
     * Display the specified Skill.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success($item, 'Skill fetched successfully');
    }

    /**
     * Update the specified Skill in storage.
     * 
     * @param UpdateSkillRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateSkillRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success(['skill' => new SkillResource($item)], 'Skill updated successfully');
    }

    /**
     * Remove the specified Skill from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Skill deleted successfully');
    }
}