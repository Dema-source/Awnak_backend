<?php

namespace App\Repositories\Eloquent;

use App\Models\Profile;
use App\Models\Skill;
use App\Repositories\Interfaces\ProfileSkillRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProfileSkillRepository implements ProfileSkillRepositoryInterface
{
    /**
     * ProfileSkillRepository Constructor.
     *
     * @param Profile $profileModel
     * @param Skill $skillModel
     */
    public function __construct(
        protected Profile $profileModel,
        protected Skill $skillModel
    ) {}

    /**
     * Get all skills linked to profiles with related profile and user.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->skillModel->with(['profiles.user', 'profiles'])
            ->whereHas('profiles');

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                switch ($field) {
                    case 'skill_name':
                        $query->where('name->' . app()->getLocale(), 'like', '%' . $value . '%');
                        break;
                    case 'user_id':
                        $query->whereHas('profiles', function ($q) use ($value) {
                            $q->where('user_id', $value);
                        });
                        break;
                    case 'profile_status':
                        $query->whereHas('profiles', function ($q) use ($value) {
                            $q->where('status', $value);
                        });
                        break;
                    default:
                        $query->where($field, $value);
                        break;
                }
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get skills for a specific profile with relations.
     *
     * @param int|string $profileId
     * @return Collection
     */
    public function getSkillsByProfile(int|string $profileId): Collection
    {
        return $this->profileModel->findOrFail($profileId)
            ->skills()
            ->with(['profiles.user'])
            ->get();
    }

    /**
     * Get profiles for a specific skill with relations.
     *
     * @param int|string $skillId
     * @return Collection
     */
    public function getProfilesBySkill(int|string $skillId): Collection
    {
        return $this->skillModel->findOrFail($skillId)
            ->profiles()
            ->with(['user', 'skills'])
            ->get();
    }

    /**
     * Attach skills to a profile.
     *
     * @param int|string $profileId
     * @param array $skillIds
     * @return bool
     */
    public function attachSkills(int|string $profileId, array $skillIds): bool
    {
        $profile = $this->profileModel->findOrFail($profileId);
        
        // Validate that all skill IDs exist
        $existingSkills = $this->skillModel->whereIn('id', $skillIds)->pluck('id')->toArray();
        $invalidSkills = array_diff($skillIds, $existingSkills);
        
        if (!empty($invalidSkills)) {
            throw new \InvalidArgumentException('Invalid skill IDs: ' . implode(', ', $invalidSkills));
        }

        $profile->skills()->attach($existingSkills);
        return true;
    }

    /**
     * Detach skills from a profile.
     *
     * @param int|string $profileId
     * @param array $skillIds
     * @return bool
     */
    public function detachSkills(int|string $profileId, array $skillIds): bool
    {
        $profile = $this->profileModel->findOrFail($profileId);
        $profile->skills()->detach($skillIds);
        return true;
    }

    /**
     * Sync skills for a profile (detaches all current and attaches new ones).
     *
     * @param int|string $profileId
     * @param array $skillIds
     * @return array
     */
    public function syncSkills(int|string $profileId, array $skillIds): array
    {
        $profile = $this->profileModel->findOrFail($profileId);
        
        // Validate that all skill IDs exist
        $existingSkills = $this->skillModel->whereIn('id', $skillIds)->pluck('id')->toArray();
        $invalidSkills = array_diff($skillIds, $existingSkills);
        
        if (!empty($invalidSkills)) {
            throw new \InvalidArgumentException('Invalid skill IDs: ' . implode(', ', $invalidSkills));
        }

        return $profile->skills()->sync($existingSkills);
    }

    /**
     * Check if a profile has a specific skill.
     *
     * @param int|string $profileId
     * @param int|string $skillId
     * @return bool
     */
    public function hasSkill(int|string $profileId, int|string $skillId): bool
    {
        return $this->profileModel->findOrFail($profileId)
            ->skills()
            ->where('skills.id', $skillId)
            ->exists();
    }

    /**
     * Get skills count for each profile.
     *
     * @param array $profileIds
     * @return Collection
     */
    public function getSkillsCount(array $profileIds = []): Collection
    {
        $query = $this->profileModel->withCount('skills');
        
        if (!empty($profileIds)) {
            $query->whereIn('id', $profileIds);
        }

        return $query->get();
    }

    /**
     * Get popular skills across all profiles.
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopularSkills(int $limit = 10): Collection
    {
        return $this->skillModel->withCount('profiles')
            ->orderBy('profiles_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
