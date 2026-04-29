<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Volunteer;

/**
 * Interface VolunteerRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface VolunteerRepositoryInterface
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
     * @return Volunteer 
     */
    public function findById(int|string $id): Volunteer;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return Volunteer
     */
    public function create(array $data): Volunteer;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Volunteer
     */
    public function update(int|string $id, array $data): Volunteer;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Get a paginated list of records with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a record by its ID with relationships loaded.
     *
     * @param int|string $id The primary key value.
     * @param array $relations Relations to load.
     * @return Volunteer|null
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): ?Volunteer;

    /**
     * Get active volunteers.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getActive(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get inactive volunteers.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getInactive(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get pending volunteers.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getPending(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get blocked volunteers.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getBlocked(int $perPage = 15): LengthAwarePaginator;

    /**
     * Activate a volunteer.
     *
     * @param int|string $id
     * @return Volunteer
     */
    public function activate(int|string $id): Volunteer;

    /**
     * Deactivate a volunteer.
     *
     * @param int|string $id
     * @return Volunteer
     */
    public function deactivate(int|string $id): Volunteer;

    /**
     * Block a volunteer.
     *
     * @param int|string $id
     * @return Volunteer
     */
    public function block(int|string $id): Volunteer;

    /**
     * Find volunteer by profile ID.
     *
     * @param int $profileId
     * @return Volunteer|null
     */
    public function findByProfileId(int $profileId): ?Volunteer;

    /**
     * Find volunteer by user ID.
     *
     * @param int $userId
     * @return Volunteer|null
     */
    public function findByUserId(int $userId): ?Volunteer;

    /**
     * Check if user has a volunteer.
     *
     * @param int $userId
     * @return bool
     */
    public function userHasVolunteer(int $userId): bool;

    /**
     * Get volunteer statistics.
     *
     * @return array
     */
    public function getStatistics(): array;

    }