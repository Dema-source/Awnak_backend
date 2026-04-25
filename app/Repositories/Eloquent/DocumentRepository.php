<?php

namespace App\Repositories\Eloquent;

use App\Models\Document;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DocumentRepository implements DocumentRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param Document $model  
     */ 
    public function __construct(
        protected Document $model
    ) {}

    /**  
     * Get a paginated list of records applying optional filters.  
     *  
     * @param array $filters Key/value filters to apply to the query.  
     * @param int $perPage Number of items per page.  
     * @return LengthAwarePaginator  
     */  
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->filter($filters)->with('documentable')->latest()->paginate($perPage);
    }

    /**  
     * Retrieve a single record by ID or throw an exception if not found.  
     *  
     * @param int|string $id  
     * @return Document  
     */ 
    public function findById(int|string $id): Document
    {
        return $this->model->with('documentable')->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return Document  
     */
    public function create(array $data): Document
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Document
     */
    public function update(int|string $id, array $data): Document
    {
        $item = $this->findById($id);
        $item->update($data);

        return $item->fresh();
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id The primary key value.
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $item = $this->findById($id);

        return (bool) $item->delete();
    }

    /**
     * Get documents by user ID.
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->byVolunteer($userId)->with('documentable')->latest()->paginate($perPage);
    }

    /**
     * Get documents by organization ID.
     *
     * @param int $organizationId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByOrganization(int $organizationId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->byOrganization($organizationId)->with('documentable')->latest()->paginate($perPage);
    }

    /**
     * Get documents by type.
     *
     * @param string $type
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByType(string $type, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $filters = array_merge($filters, ['type' => $type]);
        return $this->model->filter($filters)->with('documentable')->latest()->paginate($perPage);
    }

    /**
     * Search documents by title.
     *
     * @param string $searchTerm
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchByTitle(string $searchTerm, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $filters = array_merge($filters, ['search' => $searchTerm]);
        return $this->model->filter($filters)->with('documentable')->latest()->paginate($perPage);
    }
}
