<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Profile;

/**
 * Interface ProfileRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface ProfileRepositoryInterface
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
     * @return Profile 
     */
    public function findById(int|string $id): Profile;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return Profile
     */
    public function create(array $data): Profile;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Profile
     */
    public function update(int|string $id, array $data): Profile;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Find a profile by user ID.
     *
     * @param int|string $userId The user ID.
     * @return Profile|null
     */
    public function findByUserId(int|string $userId): ?Profile;

    /**
     * Get profiles with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get profile by ID with relationships.
     *
     * @param int|string $id The profile ID.
     * @param array $relations Relations to load.
     * @return Profile
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): Profile;

    /**
     * Get profiles by gender.
     *
     * @param string $gender The gender to filter by.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByGender(string $gender, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get profiles by age range.
     *
     * @param int $minAge Minimum age.
     * @param int $maxAge Maximum age.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByAgeRange(int $minAge, int $maxAge, int $perPage = 15): LengthAwarePaginator;

    /**
     * Search profiles by bio or interests.
     *
     * @param string $searchTerm Search term.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function searchByBioOrInterests(string $searchTerm, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get profiles with specific skills.
     *
     * @param array $skillIds Array of skill IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getBySkills(array $skillIds, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get profile statistics.
     *
     * @return array
     */
    public function getStatistics(): array;
}