<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param User $model  
     */
    public function __construct(
        protected User $model
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
     * @return User  
     */
    public function findById(int|string $id): User
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Find user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): User
    {
        return User::where('email', $email)->first();
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return User  
     */
    public function create(array $data): User
    {
        $data['password'] = $this->hashPassword($data['password']);
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return User
     */
    public function update(int|string $id, array $data): User
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
     * Hash the given plain password.
     *
     * @param string $plainPassword
     * @return string
     */
    public function hashPassword(string $plainPassword): string
    {
        return Hash::make($plainPassword);
    }

    /**
     * Search users by name, email with optional filters.
     *
     * @param string|null $search
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(?string $search = null, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Apply search
        if ($search) {
            $query->search($search);
        }

        // Apply additional filters
        $query->filter($filters);

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get users filtered by various criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function filter(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Apply all filters using the model scope
        $query->filter($filters);

        return $query->latest()->paginate($perPage);
    }

    /**
     * Inactive a user by setting status to 'notActive'.
     *
     * @param int|string $id The user ID.
     * @return User
     */
    public function inactive(int|string $id): User
    {
        $user = $this->findById($id);
        $user->update(['status' => 'notActive']);

        return $user->fresh();
    }
}
