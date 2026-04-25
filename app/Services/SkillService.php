<?php

namespace App\Services;

use App\Repositories\Interfaces\SkillRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service layer for handling business logic related to the "SkillRepositoryInterface" repository.
 */
class SkillService
{
    /**
     * SkillService Constructor.
     *
     * @param \App\Repositories\Interfaces\SkillRepositoryInterface $repository
     */
    public function __construct(
        protected SkillRepositoryInterface $repository
    ) {}

    /**
     * Retrieve a paginated list of records applying optional dynamic filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }

    /**
     * Find a record by its ID.
     *
     * @param int|string $id
     * @return mixed
     */
    public function findById(int|string $id): mixed
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new record using the provided data.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing record by ID with the given data.
     *
     * @param int|string $id
     * @param array $data
     * @return mixed
     */
    public function update(int|string $id, array $data): mixed
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get skills with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllWithRelations($relations, $filters, $perPage);
    }

    /**
     * Get skill by ID with relationships.
     *
     * @param int|string $id The skill ID.
     * @param array $relations Relations to load.
     * @return mixed
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): mixed
    {
        return $this->repository->findByIdWithRelations($id, $relations);
    }

    /**
     * Search skills by name.
     *
     * @param string $searchTerm Search term.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function searchByName(string $searchTerm, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->searchByName($searchTerm, $perPage);
    }

    /**
     * Get skills with profiles count.
     *
     * @param int $minProfiles Minimum profiles count.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getWithProfilesCount(int $minProfiles = 1, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getWithProfilesCount($minProfiles, $perPage);
    }

    /**
     * Get skills with opportunities count.
     *
     * @param int $minOpportunities Minimum opportunities count.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getWithOpportunitiesCount(int $minOpportunities = 1, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getWithOpportunitiesCount($minOpportunities, $perPage);
    }

    /**
     * Get popular skills (most used in profiles).
     *
     * @param int $limit Number of skills to return.
     * @return LengthAwarePaginator
     */
    public function getPopularSkills(int $limit = 10): LengthAwarePaginator
    {
        return $this->repository->getPopularSkills($limit);
    }

    /**
     * Get skills by multiple IDs.
     *
     * @param array $skillIds Array of skill IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $skillIds, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByIds($skillIds, $perPage);
    }

    /**
     * Get skills not associated with specific profile.
     *
     * @param int|string $profileId The profile ID.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getNotInProfile(int|string $profileId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getNotInProfile($profileId, $perPage);
    }

    /**
     * Get skills not associated with specific opportunity.
     *
     * @param int|string $opportunityId The opportunity ID.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getNotInOpportunity(int|string $opportunityId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getNotInOpportunity($opportunityId, $perPage);
    }

    /**
     * Get skill statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return $this->repository->getStatistics();
    }

    /**
     * Get skills created recently.
     *
     * @param int $days Number of days.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getRecentSkills(int $days = 30, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getRecentSkills($days, $perPage);
    }

    /**
     * Get skills for the authenticated volunteer.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getMySkills(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getMySkills($perPage);
    }
}