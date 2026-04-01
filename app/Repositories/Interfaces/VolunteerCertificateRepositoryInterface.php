<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\VolunteerCertificate;

/**
 * Interface VolunteerCertificateRepositoryInterface
 *
 * Defines the contract for CRUD operations.
 */
interface VolunteerCertificateRepositoryInterface
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
     * @return VolunteerCertificate 
     */
    public function findById(int|string $id): VolunteerCertificate;

    /**
     * Create a new record using the given data array.
     *
     * @param array $data.
     * @return VolunteerCertificate
     */
    public function create(array $data): VolunteerCertificate;

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return VolunteerCertificate
     */
    public function update(int|string $id, array $data): VolunteerCertificate;

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool;
}