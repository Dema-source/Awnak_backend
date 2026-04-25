<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Skill;

/**
 * Interface SkillRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface SkillRepositoryInterface
{
    /**
     * Retrieve a paginated list of records with optional provided conditions.
     *
     * @param array $filters [Key => value] filters.
     * @param int $perPage size of items in each page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a record by its ID.
     *
     * @param int|string $id The primary key value.
     * @return Skill 
     */
    public function findById(int|string $id): Skill;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return Skill
     */
    public function create(array $data): Skill;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Skill
     */
    public function update(int|string $id, array $data): Skill;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Get skills with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get skill by ID with relationships.
     *
     * @param int|string $id The skill ID.
     * @param array $relations Relations to load.
     * @return Skill
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): Skill;

    /**
     * Search skills by name.
     *
     * @param string $searchTerm Search term.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function searchByName(string $searchTerm, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get skills with profiles count.
     *
     * @param int $minProfiles Minimum profiles count.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getWithProfilesCount(int $minProfiles = 1, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get skills with opportunities count.
     *
     * @param int $minOpportunities Minimum opportunities count.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getWithOpportunitiesCount(int $minOpportunities = 1, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get popular skills (most used in profiles).
     *
     * @param int $limit Number of skills to return.
     * @return LengthAwarePaginator
     */
    public function getPopularSkills(int $limit = 10): LengthAwarePaginator;

    /**
     * Get skills by multiple IDs.
     *
     * @param array $skillIds Array of skill IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $skillIds, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get skills not associated with specific profile.
     *
     * @param int|string $profileId The profile ID.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getNotInProfile(int|string $profileId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get skills not associated with specific opportunity.
     *
     * @param int|string $opportunityId The opportunity ID.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getNotInOpportunity(int|string $opportunityId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get skill statistics.
     *
     * @return array
     */
    public function getStatistics(): array;

    /**
     * Get skills created recently.
     *
     * @param int $days Number of days.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getRecentSkills(int $days = 30, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get skills for authenticated volunteer.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getMySkills(int $perPage = 15): LengthAwarePaginator;
}