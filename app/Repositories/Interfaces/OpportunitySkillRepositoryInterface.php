<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface OpportunitySkillRepositoryInterface
 *
 * Defines the contract for Opportunity-Skill relationship operations.
 */
interface OpportunitySkillRepositoryInterface
{
    /**
     * Get all skills linked to opportunities with related opportunity and organization.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get skills for a specific opportunity with relations.
     *
     * @param int|string $opportunityId
     * @return Collection
     */
    public function getSkillsByOpportunity(int|string $opportunityId): Collection;

    /**
     * Get opportunities for a specific skill with relations.
     *
     * @param int|string $skillId
     * @return Collection
     */
    public function getOpportunitiesBySkill(int|string $skillId): Collection;

    /**
     * Attach skills to an opportunity.
     *
     * @param int|string $opportunityId
     * @param array $skillIds
     * @return bool
     */
    public function attachSkills(int|string $opportunityId, array $skillIds): bool;

    /**
     * Detach skills from an opportunity.
     *
     * @param int|string $opportunityId
     * @param array $skillIds
     * @return bool
     */
    public function detachSkills(int|string $opportunityId, array $skillIds): bool;

    /**
     * Sync skills for an opportunity (detaches all current and attaches new ones).
     *
     * @param int|string $opportunityId
     * @param array $skillIds
     * @return array
     */
    public function syncSkills(int|string $opportunityId, array $skillIds): array;

    /**
     * Check if an opportunity has a specific skill.
     *
     * @param int|string $opportunityId
     * @param int|string $skillId
     * @return bool
     */
    public function hasSkill(int|string $opportunityId, int|string $skillId): bool;

    /**
     * Get skills count for each opportunity.
     *
     * @param array $opportunityIds
     * @return Collection
     */
    public function getSkillsCount(array $opportunityIds = []): Collection;

    /**
     * Get popular skills across all opportunities.
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopularSkills(int $limit = 10): Collection;
}
