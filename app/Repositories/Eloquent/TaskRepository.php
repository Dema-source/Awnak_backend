<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param Task $model  
     */
    public function __construct(
        protected Task $model
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
        $query = $this->model->with('evaluation');

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
     * @return Task  
     */
    public function findById(int|string $id): Task
    {
        return $this->model->with('evaluation')->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return Task  
     */
    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Task
     */
    public function update(int|string $id, array $data): Task
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

    // public function totalVolunteerHours(Volunteer $volunteer): int
    // {
    //     return Task::where('volunteer_id', $volunteer->id)
    //         ->where('status', 'completed')
    //         ->sum('hours');
    // }

    /**
     * Add evaluation for task.
     * 
     * @param array $data
     * @return mixed
     */
    public function addEvaluation(array $data): mixed
    {
        return $this->model->evaluates()->create($data);
    }
}
