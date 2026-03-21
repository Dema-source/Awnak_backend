<?php

namespace App\Repositories\Eloquent;

use App\Models\Opportunity;
use App\Repositories\Interfaces\OpportunityRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OpportunityRepository implements OpportunityRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param Opportunity $model  
     */ 
    public function __construct(
        protected Opportunity $model
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
        $query = $this->model->query();

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $query->where($field, $value);
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**  
     * Retrieve a single record by ID or throw an exception if not found.  
     *  
     * @param int|string $id  
     * @return Opportunity  
     */ 
    public function findById(int|string $id): Opportunity
    {
        return $this->model->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return Opportunity  
     */
    public function create(array $data): Opportunity
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Opportunity
     */
    public function update(int|string $id, array $data): Opportunity
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
}