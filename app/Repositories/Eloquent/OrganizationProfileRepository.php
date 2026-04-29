<?php

namespace App\Repositories\Eloquent;

use App\Models\OrganizationProfile;
use App\Repositories\Interfaces\OrganizationProfileRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class OrganizationProfileRepository implements OrganizationProfileRepositoryInterface
{
    /**  
     * Dependency injection of the Eloquent model.  
     *  
     * @param OrganizationProfile $model  
     */
    public function __construct(
        protected OrganizationProfile $model
    ) {}

    /**  
     * Get a paginated list of records applying optional filters.  
     *  
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
                    $query->search($value);
                } elseif ($field === 'active') {
                    // Handle active user filter
                    $query->byActiveUser($value);
                } elseif ($field === 'status') {
                    // Handle status filter
                    $query->byStatus($value);
                } elseif ($field === 'type') {
                    // Handle type filter
                    $query->byType($value);
                } elseif ($field === 'license_number') {
                    // Handle license number filter
                    $query->byLicenseNumber($value);
                } elseif ($field === 'website') {
                    // Handle website filter
                    $query->byWebsite($value);
                } elseif ($field === 'bio') {
                    // Handle bio filter
                    $query->byBio($value);
                } elseif ($field === 'user_id') {
                    // Handle user ID filter
                    $query->byUserId($value);
                } elseif ($field === 'created_from') {
                    // Handle date range start
                    $query->whereDate('created_at', '>=', $value);
                } elseif ($field === 'created_to') {
                    // Handle date range end
                    $query->whereDate('created_at', '<=', $value);
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
     * @return OrganizationProfile  
     */
    public function findById(int|string $id): OrganizationProfile
    {
        return $this->model->with(['user', 'opportunities'])->findOrFail($id);
    }

    /**  
     * Create a new record in the database.  
     *  
     * @param array $data Mass-Assignment Attributes for creating the model.
     * @return OrganizationProfile  
     */
    public function create(array $data): OrganizationProfile
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record by ID with a given data.
     *
     * @param int|string $id The primary key value.
     * @param array $data.
     * @return OrganizationProfile
     */
    public function update(int|string $id, array $data): OrganizationProfile
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
     * Find organization profile by user ID.
     *
     * @param int $userId
     * @return OrganizationProfile|null
     */
    public function findByUserId(int $userId): ?OrganizationProfile
    {
        return $this->model->where('user_id', $userId)->first();
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
                if ($field === 'search') {
                    // Handle search term
                    $query->search($value);
                } elseif ($field === 'active') {
                    // Handle active user filter
                    $query->byActiveUser($value);
                } elseif ($field === 'status') {
                    // Handle status filter
                    $query->byStatus($value);
                } elseif ($field === 'type') {
                    // Handle type filter
                    $query->byType($value);
                } elseif ($field === 'license_number') {
                    // Handle license number filter
                    $query->byLicenseNumber($value);
                } elseif ($field === 'website') {
                    // Handle website filter
                    $query->byWebsite($value);
                } elseif ($field === 'bio') {
                    // Handle bio filter
                    $query->byBio($value);
                } elseif ($field === 'user_id') {
                    // Handle user ID filter
                    $query->byUserId($value);
                } elseif ($field === 'created_from') {
                    // Handle date range start
                    $query->whereDate('created_at', '>=', $value);
                } elseif ($field === 'created_to') {
                    // Handle date range end
                    $query->whereDate('created_at', '<=', $value);
                } else {
                    // Handle any other fields
                    $query->where($field, $value);
                }
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Retrieve a single record by ID with relationships or throw an exception if not found.
     *
     * @param int|string $id
     * @param array $relations Relations to load.
     * @return OrganizationProfile
     */
    public function findByIdWithRelations(int|string $id, array $relations = []): OrganizationProfile
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    /**
     * Apply filters to the query using model scopes.
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        // Status filter - use scopeByStatus
        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Type filter - use scopeByType
        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        // License number filter - use scopeByLicenseNumber
        if (isset($filters['license_number'])) {
            $query->byLicenseNumber($filters['license_number']);
        }

        // Website filter - use scopeByWebsite
        if (isset($filters['website'])) {
            $query->byWebsite($filters['website']);
        }

        // Bio filter - use scopeByBio
        if (isset($filters['bio'])) {
            $query->byBio($filters['bio']);
        }

        // Active user filter - use scopeByActiveUser
        if (isset($filters['active'])) {
            $query->byActiveUser($filters['active']);
        }

        // Created date filter - use scopeByDateRange
        if (isset($filters['created_from']) || isset($filters['created_to'])) {
            $query->byDateRange(
                $filters['created_from'] ?? null,
                $filters['created_to'] ?? null
            );
        }

        // User ID filter - use scopeByUserId
        if (isset($filters['user_id'])) {
            $query->byUserId($filters['user_id']);
        }

        return $query;
    }

    /**
     * List all not-active organizations.
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listNotActive(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()->inactive();
        return $this->applyFilters($query, $filters)->latest()->paginate($perPage);
    }

    /**
     * List all active organizations.
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listActive(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()->active();
        return $this->applyFilters($query, $filters)->latest()->paginate($perPage);
    }

    /**
     * Activate an organization.
     * 
     * @param OrganizationProfile $organization
     * @return bool
     */
    public function activate(OrganizationProfile $organization): bool
    {
        $organization->status = 'active';
        $organization->save();
        return true;
    }

    /**
     * Deactivate an organization.
     * 
     * @param OrganizationProfile $organization
     * @return bool
     */
    public function deactivate(OrganizationProfile $organization): bool
    {
        $organization->status = 'notactive';
        $organization->save();
        return true;
    }

    /**
     * Get organizations by type.
     *
     * @param string $type
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByType(string $type, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()->byType($type);
        return $this->applyFilters($query, $filters)->latest()->paginate($perPage);
    }

    /**
     * Check if user has organization profile.
     *
     * @param int $userId
     * @return bool
     */
    public function userHasProfile(int $userId): bool
    {
        return $this->model->where('user_id', $userId)->exists();
    }

    /**
     * Get organizations created in date range.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByDateRange(string $fromDate, string $toDate, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()->byDateRange($fromDate, $toDate);
        return $this->applyFilters($query, $filters)->latest()->paginate($perPage);
    }

    /**
     * Get organizations with opportunities.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getWithOpportunities(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()->withOpportunities();
        return $this->applyFilters($query, $filters)->latest()->paginate($perPage);
    }

    /**
     * Get all opportunities for a specific organization.
     * 
     * @param int $organizationId
     * @return Collection
     */
    public function getOrganizationOpportunities(int $organizationId): Collection
    {
        $profile = $this->findById($organizationId);

        if (!$profile) {
            return collect([]);
        }

        return $profile->opportunities ?? collect([]);
    }

    /**
     * Get organization statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $totalProfiles = $this->model->count();

        // Status distribution
        $statusStats = $this->model->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Type distribution
        $typeStats = $this->model->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Count organizations with opportunities
        $organizationsWithOpportunities = $this->model->whereHas('opportunities')->count();

        // Count recent organizations (last 30 days)
        $recentOrganizations = $this->model->where('created_at', '>=', now()->subDays(30))->count();

        // Count active organizations (with active users)
        $activeOrganizations = $this->model->whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->count();

        return [
            'total_profiles' => $totalProfiles,
            'status_distribution' => $statusStats,
            'type_distribution' => $typeStats,
            'organizations_with_opportunities' => $organizationsWithOpportunities,
            'recent_organizations' => $recentOrganizations,
            'active_organizations' => $activeOrganizations,
        ];
    }
}
