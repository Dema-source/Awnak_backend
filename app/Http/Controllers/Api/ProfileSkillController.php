<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\SkillResource;
use App\Services\ProfileSkillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileSkillController extends Controller
{
    /**
     * ProfileSkillController Constructor.
     *
     * @param ProfileSkillService $service
     */
    public function __construct(
        protected ProfileSkillService $service
    ) {}

    /**
     * Display a paginated listing of skills linked to profiles with relations.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAllWithRelations($filters, $perPage);

        return $this->paginate($data, 'Profile skills list fetched successfully');
    }

    /**
     * Store (attach) skills to a profile.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $profileId = $request->input('profile_id');
        $skillIds = $request->input('skill_ids');

        // Get profile for authorization check
        $profile = \App\Models\Profile::find($profileId);
        $user = Auth::user();

        // Check if user is super_administrator or system_admin or owner of profile
        if (
            !$user->hasRole('super_administrator') && !$user->hasRole('system_admin') &&
            (!$user->profile || $user->profile->id !== $profile->id)
        ) {
            return $this->error('Unauthorized to attach skills to this profile', 403);
        }

        $this->service->attachSkills($profileId, $skillIds);

        // Return updated profile with skills
        $profile = \App\Models\Profile::with('skills')->find($profileId);
        
        return $this->success(
            [
                'profile' => new ProfileResource($profile),
                'skills' => SkillResource::collection($profile->skills)
            ],
            'Skills attached to profile successfully'
        );
    }

    /**
     * Display specified profile's skills.
     *
     * @param int|string $profileId The profile ID.
     * @return JsonResponse
     */
    public function show(int|string $profileId): JsonResponse
    {
        $skills = $this->service->getSkillsByProfile($profileId);

        return $this->success(
            SkillResource::collection($skills),
            'Profile skills fetched successfully'
        );
    }

    /**
     * Update (sync) skills for a profile.
     *
     * @param Request $request Validated input data.
     * @param int|string $profileId The profile ID.
     * @return JsonResponse
     */
    public function update(Request $request, int|string $profileId): JsonResponse
    {
        // Get profile for authorization check
        $profile = \App\Models\Profile::find($profileId);
        $user = Auth::user();

        // Check if user is super_administrator or system_admin or owner of profile
        if (
            !$user->hasRole('super_administrator') && !$user->hasRole('system_admin') &&
            (!$user->profile || $user->profile->id !== $profile->id)
        ) {
            return $this->error('Unauthorized to sync skills for this profile', 403);
        }

        $skillIds = $request->input('skill_ids');

        $syncResult = $this->service->syncSkills($profileId, $skillIds);

        // Return updated profile with skills
        $profile = \App\Models\Profile::with('skills')->find($profileId);

        return $this->success(
            [
                'profile' => new ProfileResource($profile),
                'skills' => SkillResource::collection($profile->skills),
                'sync_result' => [
                    'attached' => $syncResult['attached'] ?? [],
                    'detached' => $syncResult['detached'] ?? [],
                    'updated' => $syncResult['updated'] ?? []
                ]
            ],
            'Profile skills synced successfully'
        );
    }

    /**
     * Remove (detach) skills from a profile.
     *
     * @param Request $request The HTTP request.
     * @param int|string $profileId The profile ID.
     * @return JsonResponse
     */
    public function destroy(Request $request, int|string $profileId): JsonResponse
    {
        // Get profile for authorization check
        $profile = \App\Models\Profile::find($profileId);
        $user = Auth::user();

        // Check if user is super_administrator or system_admin or owner of profile
        if (
            !$user->hasRole('super_administrator') && !$user->hasRole('system_admin') &&
            (!$user->profile || $user->profile->id !== $profile->id)
        ) {
            return $this->error('Unauthorized to detach skills from this profile', 403);
        }

        $skillIds = $request->input('skill_ids', []);

        // If no specific skills provided, detach all skills
        if (empty($skillIds)) {
            $allSkillIds = $profile->skills()->pluck('skills.id')->toArray();
            $this->service->detachSkills($profileId, $allSkillIds);
        } else {
            $this->service->detachSkills($profileId, $skillIds);
        }

        // Return updated profile with remaining skills
        $profile = \App\Models\Profile::with('skills')->find($profileId);

        return $this->success(
            [
                'profile' => new ProfileResource($profile),
                'skills' => SkillResource::collection($profile->skills)
            ],
            'Skills detached from profile successfully'
        );
    }

    /**
     * Get profiles for a specific skill.
     *
     * @param int|string $skillId The skill ID.
     * @return JsonResponse
     */
    public function getProfilesBySkill(int|string $skillId): JsonResponse
    {
        $profiles = $this->service->getProfilesBySkill($skillId);

        return $this->success(
            ProfileResource::collection($profiles),
            'Profiles for skill fetched successfully'
        );
    }

    /**
     * Check if a profile has a specific skill.
     *
     * @param int|string $profileId The profile ID.
     * @param int|string $skillId The skill ID.
     * @return JsonResponse
     */
    public function checkSkill(int|string $profileId, int|string $skillId): JsonResponse
    {
        $hasSkill = $this->service->hasSkill($profileId, $skillId);

        return $this->success(
            [
                'has_skill' => $hasSkill,
                'profile_id' => $profileId,
                'skill_id' => $skillId
            ],
            'Skill check completed successfully'
        );
    }

    /**
     * Get skills count for each profile.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getSkillsCount(Request $request): JsonResponse
    {
        $profileIds = $request->input('profile_ids', []);
        
        $profiles = $this->service->getSkillsCount($profileIds);

        $result = $profiles->map(function ($profile) {
            return [
                'id' => $profile->id,
                'first_name' => $profile->first_name,
                'last_name' => $profile->last_name,
                'skills_count' => $profile->skills_count
            ];
        });

        return $this->success($result, 'Skills count fetched successfully');
    }

    /**
     * Get popular skills across all profiles.
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
