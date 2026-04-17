<?php

namespace App\Repositories\Eloquent;

use App\Models\Opportunity;
use App\Models\Skill;
use App\Repositories\Interfaces\OpportunitySkillRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OpportunitySkillRepository implements OpportunitySkillRepositoryInterface
{
    /**
     * OpportunitySkillRepository Constructor.
     *
     * @param Opportunity $opportunityModel
     * @param Skill $skillModel
     */
    public function __construct(
        protected Opportunity $opportunityModel,
        protected Skill $skillModel
    ) {}

    /**
     * Get all skills linked to opportunities with related opportunity and organization.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->skillModel->with(['opportunities.organization', 'opportunities'])
            ->whereHas('opportunities');

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                switch ($field) {
                    case 'skill_name':
                        $query->where('name->' . app()->getLocale(), 'like', '%' . $value . '%');
                        break;
                    case 'organization_id':
                        $query->whereHas('opportunities', function ($q) use ($value) {
                            $q->where('organization_profile_id', $value);
                        });
                        break;
                    case 'opportunity_status':
                        $query->whereHas('opportunities', function ($q) use ($value) {
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
     * Get skills for a specific opportunity with relations.
     *
     * @param int|string $opportunityId
     * @return Collection
     */
    public function getSkillsByOpportunity(int|string $opportunityId): Collection
    {
        return $this->opportunityModel->findOrFail($opportunityId)
            ->skills()
            ->with(['opportunities.organization'])
            ->get();
    }

    /**
     * Get opportunities for a specific skill with relations.
     *
     * @param int|string $skillId
     * @return Collection
     */
    public function getOpportunitiesBySkill(int|string $skillId): Collection
    {
        return $this->skillModel->findOrFail($skillId)
            ->opportunities()
            ->with(['organization', 'skills'])
            ->get();
    }

    /**
     * Attach skills to an opportunity.
     *
     * @param int|string $opportunityId
     * @param array $skillIds
     * @return bool
     */
    public function attachSkills(int|string $opportunityId, array $skillIds): bool
    {
        $opportunity = $this->opportunityModel->findOrFail($opportunityId);
        
        // Validate that all skill IDs exist
        $existingSkills = $this->skillModel->whereIn('id', $skillIds)->pluck('id')->toArray();
        $invalidSkills = array_diff($skillIds, $existingSkills);
        
        if (!empty($invalidSkills)) {
            throw new \InvalidArgumentException('Invalid skill IDs: ' . implode(', ', $invalidSkills));
        }

        $opportunity->skills()->attach($existingSkills);
        return true;
    }

    /**
     * Detach skills from an opportunity.
     *
     * @param int|string $opportunityId
     * @param array $skillIds
     * @return bool
     */
    public function detachSkills(int|string $opportunityId, array $skillIds): bool
    {
        $opportunity = $this->opportunityModel->findOrFail($opportunityId);
        $opportunity->skills()->detach($skillIds);
        return true;
    }

    /**
     * Sync skills for an opportunity (detaches all current and attaches new ones).
     *
     * @param int|string $opportunityId
     * @param array $skillIds
     * @return array
     */
    public function syncSkills(int|string $opportunityId, array $skillIds): array
    {
        $opportunity = $this->opportunityModel->findOrFail($opportunityId);
        
        // Validate that all skill IDs exist
        $existingSkills = $this->skillModel->whereIn('id', $skillIds)->pluck('id')->toArray();
        $invalidSkills = array_diff($skillIds, $existingSkills);
        
        if (!empty($invalidSkills)) {
            throw new \InvalidArgumentException('Invalid skill IDs: ' . implode(', ', $invalidSkills));
        }

        return $opportunity->skills()->sync($existingSkills);
    }

    /**
     * Check if an opportunity has a specific skill.
     *
     * @param int|string $opportunityId
     * @param int|string $skillId
     * @return bool
     */
    public function hasSkill(int|string $opportunityId, int|string $skillId): bool
    {
        return $this->opportunityModel->findOrFail($opportunityId)
            ->skills()
            ->where('skills.id', $skillId)
            ->exists();
    }

    /**
     * Get skills count for each opportunity.
     *
     * @param array $opportunityIds
     * @return Collection
     */
    public function getSkillsCount(array $opportunityIds = []): Collection
    {
        $query = $this->opportunityModel->withCount('skills');
        
        if (!empty($opportunityIds)) {
            $query->whereIn('id', $opportunityIds);
        }

        return $query->get();
    }

    /**
     * Get popular skills across all opportunities.
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopularSkills(int $limit = 10): Collection
    {
        return $this->skillModel->withCount('opportunities')
            ->orderBy('opportunities_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
