<?php

namespace App\Repositories\Eloquent;

use App\Models\Volunteer;
use App\Repositories\Interfaces\VolunteerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VolunteerRepository implements VolunteerRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param Volunteer $model  
     */
    public function __construct(
        protected Volunteer $model
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
        // $query = $this->model->with('evaluation');

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                switch ($field) {
                    case 'search':
                        // Handle search term across multiple fields
                        $query->search($value);
                        break;

                    case 'active':
                        // Handle active user filter
                        $query->byActiveUser($value);
                        break;

                    case 'status':
                        // Handle status filter
                        $query->byStatus($value);
                        break;

                    case 'experience_years':
                        // Handle experience years filter
                        $query->byExperience($value);
                        break;

                    case 'languages':
                        // Handle languages filter (JSON field)
                        if (is_array($value) && !empty($value)) {
                            $query->byLanguages($value);
                        } elseif (!is_array($value) && $value !== '') {
                            $query->byLanguages([$value]);
                        }
                        break;

                    case 'availability':
                        // Handle availability filter (JSON field)
                        if (is_array($value) && !empty($value)) {
                            $query->byAvailability($value);
                        } elseif (!is_array($value) && $value !== '') {
                            $query->byAvailability([$value]);
                        }
                        break;

                    case 'profile_id':
                        // Handle profile ID filter
                        $query->byProfileId((int)$value);
                        break;

                    case 'created_on':
                        // Handle created on date filter
                        try {
                            $query->createdOn($value);
                        } catch (\Exception $e) {
                            // Skip invalid date format
                        }
                        break;

                    case 'created_from':
                        // Handle created from date filter
                        try {
                            $query->createdFrom($value);
                        } catch (\Exception $e) {
                            // Skip invalid date format
                        }
                        break;

                    case 'created_to':
                        // Handle created to date filter
                        try {
                            $query->createdTo($value);
                        } catch (\Exception $e) {
                            // Skip invalid date format
                        }
                        break;

                    default:
                        // Handle any other fields with proper escaping
                        if (in_array($field, $this->model->getFillable())) {
                            $query->where($field, $value);
                        }
                        break;
                }
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**  
     * Retrieve a single record by ID or throw an exception if not found.  
     *  
     * @param int|string $id  
     * @return Volunteer  
     */
    public function findById(int|string $id): Volunteer
    {
        return $this->model->with(['profile', 'applications', 'tasks', 'certificates'])->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return Volunteer  
     */
    public function create(array $data): Volunteer
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Volunteer
     */
    public function update(int|string $id, array $data): Volunteer
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
     * Get a paginated list of records with relationships loaded.
     *
     * @param array $relations Relations to load.
     * @param array $filters Optional filters.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getAllWithRelations(array $relations = [], array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with($relations);

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                switch ($field) {
                    case 'search':
                        // Handle search term across multiple fields
                        $query->search($value);
                        break;

                    case 'active':
                        // Handle active user filter
                        $query->byActiveUser($value);
                        break;

                    case 'status':
                        // Handle status filter
                        $query->byStatus($value);
                        break;

                    case 'experience_years':
                        // Handle experience years filter
                        $query->byExperience($value);
                        break;

                    case 'languages':
                        // Handle languages filter (JSON field)
                        if (is_array($value) && !empty($value)) {
                            $query->byLanguages($value);
                        } elseif (!is_array($value) && $value !== '') {
                            $query->byLanguages([$value]);
                        }
                        break;

                    case 'availability':
                        // Handle availability filter (JSON field)
                        if (is_array($value) && !empty($value)) {
                            $query->byAvailability($value);
                        } elseif (!is_array($value) && $value !== '') {
                            $query->byAvailability([$value]);
                        }
                        break;

                    case 'profile_id':
                        // Handle profile ID filter
                        $query->byProfileId((int)$value);
                        break;

                    case 'created_on':
                        // Handle created on date filter
                        try {
                            $query->createdOn($value);
                        } catch (\Exception $e) {
                            // Skip invalid date format
                        }
                        break;

                    case 'created_from':
                        // Handle created from date filter
                        try {
                            $query->createdFrom($value);
                        } catch (\Exception $e) {
                            // Skip invalid date format
                        }
                        break;

                    case 'created_to':
                        // Handle created to date filter
                        try {
                            $query->createdTo($value);
                        } catch (\Exception $e) {
                            // Skip invalid date format
                        }
                        break;

                    default:
                        // Handle any other fields with proper escaping
                        if (in_array($field, $this->model->getFillable())) {
                            $query->where($field, $value);
                        }
                        break;
                }
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Find a record by its ID with relationships loaded.
     *
     * @param int|string $id The primary key value.
     * @param array $relations Relations to load.
     * @return Volunteer|null
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): ?Volunteer
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Get active volunteers.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getActive(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->Active->latest()->paginate($perPage);
    }

    /**
     * Get inactive volunteers.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getInactive(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->Inactive->latest()->paginate($perPage);
    }

    /**
     * Get pending volunteers.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getPending(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->Pending->latest()->paginate($perPage);
    }

    /**
     * Get blocked volunteers.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getBlocked(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->Blocked->latest()->paginate($perPage);
    }

    /**
     * Activate a volunteer.
     *
     * @param int|string $id
     * @return Volunteer
     */
    public function activate(int|string $id): Volunteer
    {
        $volunteer = $this->findById($id);
        $volunteer->status = 'active';
        $volunteer->save();
        return $volunteer;
    }

    /**
     * Deactivate a volunteer.
     *
     * @param int|string $id
     * @return Volunteer
     */
    public function deactivate(int|string $id): Volunteer
    {
        $volunteer = $this->findById($id);
        $volunteer->status = 'In_active';
        $volunteer->save();
        return $volunteer;
    }

    /**
     * Block a volunteer.
     *
     * @param int|string $id
     * @return Volunteer
     */
    public function block(int|string $id): Volunteer
    {
        $volunteer = $this->findById($id);
        $volunteer->status = 'blocked';
        $volunteer->save();
        return $volunteer;
    }

    /**
     * Find volunteer by profile ID.
     *
     * @param int $profileId
     * @return Volunteer|null
     */
    public function findByProfileId(int $profileId): ?Volunteer
    {
        return $this->model->ByProfileId($profileId)->first();
    }

    /**
     * Find volunteer by user ID.
     *
     * @param int $userId
     * @return Volunteer|null
     */
    public function findByUserId(int $userId): ?Volunteer
    {
        return $this->model->whereHas('profile', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->first();
    }

    /**
     * Check if user has a volunteer.
     *
     * @param int $userId
     * @return bool
     */
    public function userHasVolunteer(int $userId): bool
    {
        return $this->model->whereHas('profile', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
    }

    /**
     * Get volunteer statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $total = $this->model->count();
        $active = $this->model->Active->count();
        $inactive = $this->model->Inactive->count();
        $pending = $this->model->Pending->count();
        $blocked = $this->model->Blocked->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'pending' => $pending,
            'blocked' => $blocked,
        ];
    }
}
