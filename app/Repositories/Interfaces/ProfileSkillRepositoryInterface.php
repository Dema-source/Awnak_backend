<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface ProfileSkillRepositoryInterface
 *
 * Defines contract for Profile-Skill relationship operations.
 */
interface ProfileSkillRepositoryInterface
{
    /**
     * Get all skills linked to profiles with related profile and user.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get skills for a specific profile with relations.
     *
     * @param int|string $profileId
     * @return Collection
     */
    public function getSkillsByProfile(int|string $profileId): Collection;

    /**
     * Get profiles for a specific skill with relations.
     *
     * @param int|string $skillId
     * @return Collection
     */
    public function getProfilesBySkill(int|string $skillId): Collection;

    /**
     * Attach skills to a profile.
     *
     * @param int|string $profileId
     * @param array $skillIds
     * @return bool
     */
    public function attachSkills(int|string $profileId, array $skillIds): bool;

    /**
     * Detach skills from a profile.
     *
     * @param int|string $profileId
     * @param array $skillIds
     * @return bool
     */
    public function detachSkills(int|string $profileId, array $skillIds): bool;

    /**
     * Sync skills for a profile (detaches all current and attaches new ones).
     *
     * @param int|string $profileId
     * @param array $skillIds
     * @return array
     */
    public function syncSkills(int|string $profileId, array $skillIds): array;

    /**
     * Check if a profile has a specific skill.
     *
     * @param int|string $profileId
     * @param int|string $skillId
     * @return bool
     */
    public function hasSkill(int|string $profileId, int|string $skillId): bool;

    /**
     * Get skills count for each profile.
     *
     * @param array $profileIds
     * @return Collection
     */
    public function getSkillsCount(array $profileIds = []): Collection;

    /**
     * Get popular skills across all profiles.
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopularSkills(int $limit = 10): Collection;
}
