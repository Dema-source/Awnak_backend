<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Document;

/**
 * Interface DocumentRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface DocumentRepositoryInterface
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
     * @return Document 
     */
    public function findById(int|string $id): Document;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return Document
     */
    public function create(array $data): Document;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Document
     */
    public function update(int|string $id, array $data): Document;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Get documents by user ID.
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByVolunteer(int $userId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get documents by organization ID.
     *
     * @param int $organizationId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByOrganization(int $organizationId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get documents by type.
     *
     * @param string $type
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByType(string $type, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Search documents by title.
     *
     * @param string $searchTerm
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchByTitle(string $searchTerm, array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
