<?php

namespace App\Repositories\Eloquent;

use App\Models\Profile;
use App\Repositories\Interfaces\ProfileRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProfileRepository implements ProfileRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param Profile $model  
     */
    public function __construct(
        protected Profile $model
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
                if ($field === 'search') {
                    // Handle search term
                    $query->searchInBioOrInterests($value);
                } elseif ($field === 'active') {
                    // Handle active user filter
                    $query->whereHas('user', function($q) use ($value) {
                        $q->where('status', $value ? 'active' : 'notActive');
                    });
                } elseif ($field === 'gender') {
                    // Handle gender filter
                    $query->ofGender($value);
                } elseif ($field === 'min_age') {
                    // Handle age range (min)
                    if (isset($filters['max_age'])) {
                        $query->ofAgeRange($value, $filters['max_age']);
                    }
                } elseif ($field === 'max_age') {
                    // Handle age range (max)
                    if (isset($filters['min_age'])) {
                        $query->ofAgeRange($filters['min_age'], $value);
                    }
                } elseif ($field === 'skill_ids') {
                    // Handle skills filter
                    if (is_array($value)) {
                        $query->withSkills($value);
                    }
                } elseif ($field === 'age') {
                    // Handle single age filter
                    $query->where('age', $value);
                } elseif ($field === 'bio') {
                    // Handle bio filter
                    $query->where('bio', 'like', "%{$value}%");
                } elseif ($field === 'user_id') {
                    // Handle user ID filter
                    $query->where('user_id', $value);
                } elseif ($field === 'created_on') {
                    // Handle created on date filter
                    $query->createdOn($value);
                } elseif ($field === 'created_from') {
                    // Handle created from date filter
                    $query->createdFrom($value);
                } elseif ($field === 'created_to') {
                    // Handle created to date filter
                    $query->createdTo($value);
                } elseif ($field === 'interests') {
                    // Handle interests filter (JSON contains)
                    if (is_array($value)) {
                        foreach ($value as $interest) {
                            $query->orWhereJsonContains('interests', $interest);
                        }
                    } else {
                        $query->whereJsonContains('interests', $value);
                    }
                } else {
                    // Handle any other fields
                    $query->where($field, $value);
                }
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**  
     * Retrieve a single record by ID or throw an exception if not found.  
     *  
     * @param int|string $id  
     * @return Profile  
     */
    public function findById(int|string $id): Profile
    {
        return $this->model->with(['user', 'skills'])->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return Profile  
     */
    public function create(array $data): Profile
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Profile
     */
    public function update(int|string $id, array $data): Profile
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
     * Find a profile by user ID.  
     *  
     * @param int|string $userId The user ID.  
     * @return Profile|null  
     */
    public function findByUserId(int|string $userId): ?Profile
    {
        return $this->model->where('user_id', $userId)->first();
    }

    /**  
     * Get profiles with relationships loaded.  
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
                $query->where($field, $value);
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**  
     * Get profile by ID with relationships.  
     *  
     * @param int|string $id The profile ID.  
     * @param array $relations Relations to load.  
     * @return Profile  
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): Profile
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    /**  
     * Get profiles by gender.  
     *  
     * @param string $gender The gender to filter by.  
     * @param int $perPage Items per page.  
     * @return LengthAwarePaginator  
     */
    public function getByGender(string $gender, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->ofGender($gender)->latest()->paginate($perPage);
    }

    /**  
     * Get profiles by age range.  
     *  
     * @param int $minAge Minimum age.  
     * @param int $maxAge Maximum age.  
     * @param int $perPage Items per page.  
     * @return LengthAwarePaginator  
     */
    public function getByAgeRange(int $minAge, int $maxAge, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->ofAgeRange($minAge, $maxAge)->latest()->paginate($perPage);
    }

    /**  
     * Search profiles by bio or interests.  
     *  
     * @param string $searchTerm Search term.  
     * @param int $perPage Items per page.  
     * @return LengthAwarePaginator  
     */
    public function searchByBioOrInterests(string $searchTerm, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->searchInBioOrInterests($searchTerm)->latest()->paginate($perPage);
    }

    /**  
     * Get profiles with specific skills.  
     *  
     * @param array $skillIds Array of skill IDs.  
     * @param int $perPage Items per page.  
     * @return LengthAwarePaginator  
     */
    public function getBySkills(array $skillIds, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->withSkills($skillIds)->latest()->paginate($perPage);
    }

    /**
     * Get profile statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $totalProfiles = $this->model->count();

        // Count profiles by gender
        $genderStats = $this->model->selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        // Count profiles by age ranges
        $ageRanges = [
            '18-25' => $this->model->whereBetween('age', [18, 25])->count(),
            '26-35' => $this->model->whereBetween('age', [26, 35])->count(),
            '36-45' => $this->model->whereBetween('age', [36, 45])->count(),
            '46-55' => $this->model->whereBetween('age', [46, 55])->count(),
            '56+' => $this->model->where('age', '>=', 56)->count(),
        ];

        // Count profiles with skills
        $profilesWithSkills = $this->model->whereHas('skills')->count();

        // Count recent profiles (last 30 days)
        $recentProfiles = $this->model->where('created_at', '>=', now()->subDays(30))->count();

        // Calculate average age
        $avgAge = $this->model->avg('age');

        // Count profiles by skills count
        $skillsCountStats = $this->model->selectRaw('(SELECT COUNT(*) FROM profile_skill WHERE profile_skill.profile_id = profiles.id) as skills_count, COUNT(*) as count')
            ->groupBy('skills_count')
            ->orderBy('skills_count')
            ->pluck('count', 'skills_count')
            ->toArray();

        return [
            'total_profiles' => $totalProfiles,
            'gender_distribution' => $genderStats,
            'age_distribution' => $ageRanges,
            'profiles_with_skills' => $profilesWithSkills,
            'recent_profiles' => $recentProfiles,
            'average_age' => round($avgAge, 2),
            'skills_count_distribution' => $skillsCountStats,
            'profiles_without_skills' => $totalProfiles - $profilesWithSkills,
            'skills_adoption_rate' => $totalProfiles > 0
                ? round(($profilesWithSkills / $totalProfiles) * 100, 2)
                : 0
        ];
    }

    }
