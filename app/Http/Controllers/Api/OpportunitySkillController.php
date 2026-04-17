<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OpportunitySkill\StoreOpportunitySkillRequest;
use App\Http\Requests\Api\OpportunitySkill\UpdateOpportunitySkillRequest;
use App\Http\Requests\Api\OpportunitySkill\DestroyOpportunitySkillRequest;
use App\Http\Resources\OpportunityResource;
use App\Http\Resources\SkillResource;
use App\Services\OpportunitySkillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpportunitySkillController extends Controller
{
    /**
     * OpportunitySkillController Constructor.
     *
     * @param OpportunitySkillService $service
     */
    public function __construct(
        protected OpportunitySkillService $service
    ) {}

    /**
     * Display a paginated listing of skills linked to opportunities with relations.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAllWithRelations($filters, $perPage);

        return $this->paginate($data, 'Opportunity skills list fetched successfully');
    }

    /**
     * Store (attach) skills to an opportunity.
     *
     * @param StoreOpportunitySkillRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreOpportunitySkillRequest $request): JsonResponse
    {
        $opportunityId = $request->input('opportunity_id');
        $skillIds = $request->input('skill_ids');

        // Get opportunity for authorization check
        $opportunity = \App\Models\Opportunity::find($opportunityId);
        $user = Auth::user();

        // Check if user is super_administrator or system_admin or owner of opportunity
        if (
            !$user->hasRole('super_administrator') && !$user->hasRole('system_admin') &&
            (!$user->organization_profile || $user->organization_profile->id !== $opportunity->organization_profile_id)
        ) {
            return $this->error('Unauthorized to attach skills to this opportunity', 403);
        }

        $this->service->attachSkills($opportunityId, $skillIds);

        // Return updated opportunity with skills
        $opportunity = \App\Models\Opportunity::with('skills')->find($opportunityId);
        
        return $this->success(
            [
                'opportunity' => new OpportunityResource($opportunity),
                'skills' => SkillResource::collection($opportunity->skills)
            ],
            'Skills attached to opportunity successfully'
        );
    }

    /**
     * Display the specified opportunity's skills.
     *
     * @param int|string $opportunityId The opportunity ID.
     * @return JsonResponse
     */
    public function show(int|string $opportunityId): JsonResponse
    {
        $skills = $this->service->getSkillsByOpportunity($opportunityId);

        return $this->success(
            SkillResource::collection($skills),
            'Opportunity skills fetched successfully'
        );
    }

    /**
     * Update (sync) skills for an opportunity.
     *
     * @param UpdateOpportunitySkillRequest $request Validated input data.
     * @param int|string $opportunityId The opportunity ID.
     * @return JsonResponse
     */
    public function update(UpdateOpportunitySkillRequest $request, int|string $opportunityId): JsonResponse
    {
        // Get opportunity for authorization check
        $opportunity = \App\Models\Opportunity::find($opportunityId);
        $user = Auth::user();

        // Check if user is super_administrator or system_admin or owner of opportunity
        if (
            !$user->hasRole('super_administrator') && !$user->hasRole('system_admin') &&
            (!$user->organization_profile || $user->organization_profile->id !== $opportunity->organization_profile_id)
        ) {
            return $this->error('Unauthorized to sync skills for this opportunity', 403);
        }

        $skillIds = $request->input('skill_ids');

        $syncResult = $this->service->syncSkills($opportunityId, $skillIds);

        // Return updated opportunity with skills
        $opportunity = \App\Models\Opportunity::with('skills')->find($opportunityId);

        return $this->success(
            [
                'opportunity' => new OpportunityResource($opportunity),
                'skills' => SkillResource::collection($opportunity->skills),
                'sync_result' => [
                    'attached' => $syncResult['attached'] ?? [],
                    'detached' => $syncResult['detached'] ?? [],
                    'updated' => $syncResult['updated'] ?? []
                ]
            ],
            'Opportunity skills synced successfully'
        );
    }

    /**
     * Remove (detach) skills from an opportunity.
     *
     * @param DestroyOpportunitySkillRequest $request The validated form request.
     * @param int|string $opportunityId The opportunity ID.
     * @return JsonResponse
     */
    public function destroy(DestroyOpportunitySkillRequest $request, int|string $opportunityId): JsonResponse
    {
        // Get opportunity for authorization check
        $opportunity = \App\Models\Opportunity::find($opportunityId);
        $user = Auth::user();

        // Check if user is super_administrator or system_admin or owner of opportunity
        if (
            !$user->hasRole('super_administrator') && !$user->hasRole('system_admin') &&
            (!$user->organization_profile || $user->organization_profile->id !== $opportunity->organization_profile_id)
        ) {
            return $this->error('Unauthorized to detach skills from this opportunity', 403);
        }

        $skillIds = $request->input('skill_ids', []);

        // If no specific skills provided, detach all skills
        if (empty($skillIds)) {
            $allSkillIds = $opportunity->skills()->pluck('skills.id')->toArray();
            $this->service->detachSkills($opportunityId, $allSkillIds);
        } else {
            $this->service->detachSkills($opportunityId, $skillIds);
        }

        // Return updated opportunity with remaining skills
        $opportunity = \App\Models\Opportunity::with('skills')->find($opportunityId);

        return $this->success(
            [
                'opportunity' => new OpportunityResource($opportunity),
                'skills' => SkillResource::collection($opportunity->skills)
            ],
            'Skills detached from opportunity successfully'
        );
    }

    /**
     * Get opportunities for a specific skill.
     *
     * @param int|string $skillId The skill ID.
     * @return JsonResponse
     */
    public function getOpportunitiesBySkill(int|string $skillId): JsonResponse
    {
        $opportunities = $this->service->getOpportunitiesBySkill($skillId);

        return $this->success(
            OpportunityResource::collection($opportunities),
            'Opportunities for skill fetched successfully'
        );
    }

    /**
     * Check if an opportunity has a specific skill.
     *
     * @param int|string $opportunityId The opportunity ID.
     * @param int|string $skillId The skill ID.
     * @return JsonResponse
     */
    public function checkSkill(int|string $opportunityId, int|string $skillId): JsonResponse
    {
        $hasSkill = $this->service->hasSkill($opportunityId, $skillId);

        return $this->success(
            [
                'has_skill' => $hasSkill,
                'opportunity_id' => $opportunityId,
                'skill_id' => $skillId
            ],
            'Skill check completed successfully'
        );
    }

    /**
     * Get skills count for each opportunity.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getSkillsCount(Request $request): JsonResponse
    {
        $opportunityIds = $request->input('opportunity_ids', []);
        
        $opportunities = $this->service->getSkillsCount($opportunityIds);

        $result = $opportunities->map(function ($opportunity) {
            return [
                'id' => $opportunity->id,
                'title' => $opportunity->title,
                'skills_count' => $opportunity->skills_count
            ];
        });

        return $this->success($result, 'Skills count fetched successfully');
    }

    /**
     * Get popular skills across all opportunities.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getPopularSkills(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 10);
        
        $skills = $this->service->getPopularSkills($limit);

        return $this->success(
            SkillResource::collection($skills),
            'Popular skills fetched successfully'
        );
    }
}
