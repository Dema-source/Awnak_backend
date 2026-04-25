<?php

namespace App\Services;

use App\Repositories\Interfaces\ProfileSkillRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Service layer for handling business logic related to Profile-Skill relationships.
 */
class ProfileSkillService
{
    /**
     * ProfileSkillService Constructor.
     *
     * @param ProfileSkillRepositoryInterface $repository
     */
    public function __construct(
        protected ProfileSkillRepositoryInterface $repository
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
        return $this->repository->getAllWithRelations($filters, $perPage);
    }

    /**
     * Get skills for a specific profile with relations.
     *
     * @param int|string $profileId
     * @return Collection
     */
    public function getSkillsByProfile(int|string $profileId): Collection
    {
        return $this->repository->getSkillsByProfile($profileId);
    }

    /**
     * Get profiles for a specific skill with relations.
     *
     * @param int|string $skillId
     * @return Collection
     */
    public function getProfilesBySkill(int|string $skillId): Collection
    {
        return $this->repository->getProfilesBySkill($skillId);
    }

    /**
     * Attach skills to a profile with authorization check.
     *
     * @param int|string $profileId
     * @param array $skillIds
     * @return bool
     */
    public function attachSkills(int|string $profileId, array $skillIds): bool
    {
        $this->authorizeProfileAccess($profileId);
        return $this->repository->attachSkills($profileId, $skillIds);
    }

    /**
     * Detach skills from a profile with authorization check.
     *
     * @param int|string $profileId
     * @param array $skillIds
     * @return bool
     */
    public function detachSkills(int|string $profileId, array $skillIds): bool
    {
        $this->authorizeProfileAccess($profileId);
        return $this->repository->detachSkills($profileId, $skillIds);
    }

    /**
     * Sync skills for a profile with authorization check.
     *
     * @param int|string $profileId
     * @param array $skillIds
     * @return array
     */
    public function syncSkills(int|string $profileId, array $skillIds): array
    {
        $this->authorizeProfileAccess($profileId);
        return $this->repository->syncSkills($profileId, $skillIds);
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
        return $this->repository->hasSkill($profileId, $skillId);
    }

    /**
     * Get skills count for each profile.
     *
     * @param array $profileIds
     * @return Collection
     */
    public function getSkillsCount(array $profileIds = []): Collection
    {
        return $this->repository->getSkillsCount($profileIds);
    }

    /**
     * Get popular skills across all profiles.
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopularSkills(int $limit = 10): Collection
    {
        return $this->repository->getPopularSkills($limit);
    }

    /**
     * Get skills for current user's profile.
     *
     * @param int $profileId
     * @return Collection
     */
    public function getMyProfileSkills(int $profileId): Collection
    {
        $user = Auth::user();
        if (!$user->profile) {
            return collect([]);
        }

        // Verify profile belongs to current user
        $profile = $this->repository->getSkillsByProfile($profileId)->first();
        if (!$profile || $profile->user_id !== $user->id) {
            return collect([]);
        }

        return $this->repository->getSkillsByProfile($profileId);
    }

    /**
     * Authorize access to profile operations.
     *
     * @param int|string $profileId
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeProfileAccess(int|string $profileId): void
    {
        $user = Auth::user();
        
        // Super admins can access all profiles
        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return;
        }

        // Check if profile belongs to current user
        if (!$user->profile) {
            throw new \Illuminate\Auth\Access\AuthorizationException('User does not have a profile');
        }

        $profile = \App\Models\Profile::find($profileId);
        if (!$profile || $profile->user_id !== $user->id) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized to modify this profile');
        }
    }
}
