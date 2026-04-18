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

    /**
     * Get skills with relationships loaded.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function indexWithRelations(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'relations' => 'sometimes|array',
            'relations.*' => 'string|in:profiles,opportunities'
        ]);

        $relations = $validated['relations'] ?? [];
        $filters = $request->except(['page', 'per_page', 'relations']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAllWithRelations($relations, $filters, $perPage);

        return $this->paginate($data, 'Skills with relations fetched successfully');
    }

    /**
     * Display the specified Skill with relationships.
     *
     * @param Request $request The HTTP request.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function showWithRelations(Request $request, int|string $id): JsonResponse
    {
        $validated = $request->validate([
            'relations' => 'sometimes|array',
            'relations.*' => 'string|in:profiles,opportunities'
        ]);

        $relations = $validated['relations'] ?? [];
        $item = $this->service->findByIdWithRelations($id, $relations);

        return $this->success($item, 'Skill with relations fetched successfully');
    }

    /**
     * Search skills by name.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => 'required|string|min:2|max:255'
        ]);

        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->searchByName($validated['search'], $perPage);

        return $this->paginate($data, 'Skills search results fetched successfully');
    }

    /**
     * Get skills with profiles count.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getWithProfilesCount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'min_profiles' => 'sometimes|integer|min:1'
        ]);

        $minProfiles = $validated['min_profiles'] ?? 1;
        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getWithProfilesCount($minProfiles, $perPage);

        return $this->paginate($data, 'Skills with profiles count fetched successfully');
    }

    /**
     * Get skills with opportunities count.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getWithOpportunitiesCount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'min_opportunities' => 'sometimes|integer|min:1'
        ]);

        $minOpportunities = $validated['min_opportunities'] ?? 1;
        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getWithOpportunitiesCount($minOpportunities, $perPage);

        return $this->paginate($data, 'Skills with opportunities count fetched successfully');
    }

    /**
     * Get popular skills.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getPopular(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => 'sometimes|integer|min:1|max:50'
        ]);

        $limit = $validated['limit'] ?? 10;
        $data = $this->service->getPopularSkills($limit);

        return $this->paginate($data, 'Popular skills fetched successfully');
    }

    /**
     * Get skills by multiple IDs.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getByIds(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'skill_ids' => 'required|array|min:1',
            'skill_ids.*' => 'required|integer|exists:skills,id'
        ]);

        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getByIds($validated['skill_ids'], $perPage);

        return $this->paginate($data, 'Skills by IDs fetched successfully');
    }

    /**
     * Get skills not associated with specific profile.
     *
     * @param Request $request The HTTP request.
     * @param int|string $profileId The profile ID.
     * @return JsonResponse
     */
    public function getNotInProfile(Request $request, int|string $profileId): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getNotInProfile($profileId, $perPage);

        return $this->paginate($data, 'Skills not in profile fetched successfully');
    }

    /**
     * Get skills not associated with specific opportunity.
     *
     * @param Request $request The HTTP request.
     * @param int|string $opportunityId The opportunity ID.
     * @return JsonResponse
     */
    public function getNotInOpportunity(Request $request, int|string $opportunityId): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getNotInOpportunity($opportunityId, $perPage);

        return $this->paginate($data, 'Skills not in opportunity fetched successfully');
    }

    /**
     * Get skill statistics.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $statistics = $this->service->getStatistics();

        return $this->success($statistics, 'Skill statistics fetched successfully');
    }

    /**
     * Get recent skills.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getRecent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'days' => 'sometimes|integer|min:1|max:365'
        ]);

        $days = $validated['days'] ?? 30;
        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getRecentSkills($days, $perPage);

        return $this->paginate($data, 'Recent skills fetched successfully');
    }
}