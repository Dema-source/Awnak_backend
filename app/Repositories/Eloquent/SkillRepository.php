<?php

namespace App\Repositories\Eloquent;

use App\Models\Skill;
use App\Repositories\Interfaces\SkillRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SkillRepository implements SkillRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param Skill $model  
     */
    public function __construct(
        protected Skill $model
    ) {}

    /**  
     * Get a paginated list of records applying optional filters and search using local scopes.  
     *  
     * @param array $filters Key/value filters to apply to the query.  
     * @param int $perPage Number of items per page.  
     * @return LengthAwarePaginator  
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->filter($filters)->latest()->paginate($perPage);
    }

    /**  
     * Retrieve a single record by ID or throw an exception if not found.  
     *  
     * @param int|string $id  
     * @return Skill  
     */
    public function findById(int|string $id): Skill
    {
        return $this->model->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return Skill  
     */
    public function create(array $data): Skill
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return Skill
     */
    public function update(int|string $id, array $data): Skill
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
     * Get skills with relationships loaded.
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
     * Get skill by ID with relationships.
     *
     * @param int|string $id The skill ID.
     * @param array $relations Relations to load.
     * @return Skill
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): Skill
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    /**
     * Search skills by name.
     *
     * @param string $searchTerm Search term.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function searchByName(string $searchTerm, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->searchByName($searchTerm)->latest()->paginate($perPage);
    }

    /**
     * Get skills with profiles count.
     *
     * @param int $minProfiles Minimum profiles count.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getWithProfilesCount(int $minProfiles = 1, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->withProfilesCount($minProfiles)->latest()->paginate($perPage);
    }

    /**
     * Get skills with opportunities count.
     *
     * @param int $minOpportunities Minimum opportunities count.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getWithOpportunitiesCount(int $minOpportunities = 1, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->withOpportunitiesCount($minOpportunities)->latest()->paginate($perPage);
    }

    /**
     * Get popular skills (most used in profiles).
     *
     * @param int $limit Number of skills to return.
     * @return LengthAwarePaginator
     */
    public function getPopularSkills(int $limit = 10): LengthAwarePaginator
    {
        return $this->model->popular()
            ->paginate($limit);
    }

    /**
     * Get skills by multiple IDs.
     *
     * @param array $skillIds Array of skill IDs.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getByIds(array $skillIds, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->whereIn('id', $skillIds)->latest()->paginate($perPage);
    }

    /**
     * Get skills not associated with specific profile.
     *
     * @param int|string $profileId The profile ID.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getNotInProfile(int|string $profileId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->NotInProfile($profileId)->latest()->paginate($perPage);
    }

    /**
     * Get skills not associated with specific opportunity.
     *
     * @param int|string $opportunityId The opportunity ID.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getNotInOpportunity(int|string $opportunityId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->NotInOpportunity($opportunityId)->latest()->paginate($perPage);
    }

    /**
     * Get skill statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $totalSkills = $this->model->count();
        
        // Count skills that are associated with at least one profile
        $skillsWithProfiles = $this->model->whereHas('profiles')->count();
        
        // Count skills that are associated with at least one opportunity
        $skillsWithOpportunities = $this->model->whereHas('opportunities')->count();
        
        // Count skills created in the last 30 days
        $recentSkills = $this->model->where('created_at', '>=', now()->subDays(30))->count();

        // Calculate average number of profiles per skill
        // This counts all profiles associated with each skill and computes the average
        $avgProfilesPerSkill = $this->model->withCount('profiles')
            ->get()
            ->avg('profiles_count');

        // Calculate average number of opportunities per skill
        // This counts all opportunities associated with each skill and computes the average
        $avgOpportunitiesPerSkill = $this->model->withCount('opportunities')
            ->get()
            ->avg('opportunities_count');

        return [
            'total_skills' => $totalSkills,
            'skills_with_profiles' => $skillsWithProfiles,
            'skills_with_opportunities' => $skillsWithOpportunities,
            'recent_skills' => $recentSkills,
            'avg_profiles_per_skill' => round($avgProfilesPerSkill, 2),
            'avg_opportunities_per_skill' => round($avgOpportunitiesPerSkill, 2),
            'skills_usage_percentage' => $totalSkills > 0
                ? round(($skillsWithProfiles / $totalSkills) * 100, 2)
                : 0
        ];
    }

    /**
     * Get skills created recently.
     *
     * @param int $days Number of days.
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getRecentSkills(int $days = 30, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->recent($days)->latest()->paginate($perPage);
    }

    /**
     * Get skills for the authenticated volunteer.
     *
     * @param int $perPage Items per page.
     * @return LengthAwarePaginator
     */
    public function getMySkills(int $perPage = 15): LengthAwarePaginator
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        if (!$user) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                new \Illuminate\Support\Collection(),
                0,
                $perPage
            );
        }

        return $this->model->whereHas('profiles', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->withCount('profiles')->latest()->paginate($perPage);
    }
}
