<?php

namespace App\Services;

use App\Repositories\Interfaces\OpportunitySkillRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Service layer for handling business logic related to Opportunity-Skill relationships.
 */
class OpportunitySkillService
{
    /**
     * OpportunitySkillService Constructor.
     *
     * @param OpportunitySkillRepositoryInterface $repository
     */
    public function __construct(
        protected OpportunitySkillRepositoryInterface $repository
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
        return $this->repository->getAllWithRelations($filters, $perPage);
    }

    /**
     * Get skills for a specific opportunity with relations.
     *
     * @param int|string $opportunityId
     * @return Collection
     */
    public function getSkillsByOpportunity(int|string $opportunityId): Collection
    {
        return $this->repository->getSkillsByOpportunity($opportunityId);
    }

    /**
     * Get opportunities for a specific skill with relations.
     *
     * @param int|string $skillId
     * @return Collection
     */
    public function getOpportunitiesBySkill(int|string $skillId): Collection
    {
        return $this->repository->getOpportunitiesBySkill($skillId);
    }

    /**
     * Attach skills to an opportunity with authorization check.
     *
     * @param int|string $opportunityId
     * @param array $skillIds
     * @return bool
     */
    public function attachSkills(int|string $opportunityId, array $skillIds): bool
    {
        $this->authorizeOpportunityAccess($opportunityId);
        return $this->repository->attachSkills($opportunityId, $skillIds);
    }

    /**
     * Detach skills from an opportunity with authorization check.
     *
     * @param int|string $opportunityId
     * @param array $skillIds
     * @return bool
     */
    public function detachSkills(int|string $opportunityId, array $skillIds): bool
    {
        $this->authorizeOpportunityAccess($opportunityId);
        return $this->repository->detachSkills($opportunityId, $skillIds);
    }

    /**
     * Sync skills for an opportunity with authorization check.
     *
     * @param int|string $opportunityId
     * @param array $skillIds
     * @return array
     */
    public function syncSkills(int|string $opportunityId, array $skillIds): array
    {
        $this->authorizeOpportunityAccess($opportunityId);
        return $this->repository->syncSkills($opportunityId, $skillIds);
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
        return $this->repository->hasSkill($opportunityId, $skillId);
    }

    /**
     * Get skills count for each opportunity.
     *
     * @param array $opportunityIds
     * @return Collection
     */
    public function getSkillsCount(array $opportunityIds = []): Collection
    {
        return $this->repository->getSkillsCount($opportunityIds);
    }

    /**
     * Get popular skills across all opportunities.
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopularSkills(int $limit = 10): Collection
    {
        return $this->repository->getPopularSkills($limit);
    }

    /**
     * Get skills for the current user's organization opportunities.
     *
     * @param int $opportunityId
     * @return Collection
     */
    public function getMyOrganizationOpportunitySkills(int $opportunityId): Collection
    {
        $user = Auth::user();
        if (!$user->organization_profile) {
            return collect([]);
        }

        // Verify the opportunity belongs to the user's organization
        $opportunity = $this->repository->getSkillsByOpportunity($opportunityId)->first();
        if (!$opportunity || $opportunity->organization_profile_id !== $user->organization_profile->id) {
            return collect([]);
        }

        return $this->repository->getSkillsByOpportunity($opportunityId);
    }

    /**
     * Authorize access to opportunity operations.
     *
     * @param int|string $opportunityId
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeOpportunityAccess(int|string $opportunityId): void
    {
        $user = Auth::user();
        
        // Super admins can access all opportunities
        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return;
        }

        // Check if the opportunity belongs to the user's organization
        if (!$user->organization_profile) {
            throw new \Illuminate\Auth\Access\AuthorizationException('User does not have an organization profile');
        }

        $opportunity = \App\Models\Opportunity::find($opportunityId);
        if (!$opportunity || $opportunity->organization_profile_id !== $user->organization_profile->id) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized to modify this opportunity');
        }
    }
}
