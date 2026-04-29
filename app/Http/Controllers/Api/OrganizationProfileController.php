<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrganizationProfile\StoreOrganizationProfileRequest;
use App\Http\Requests\Api\OrganizationProfile\UpdateOrganizationProfileRequest;
use App\Services\OrganizationProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrganizationProfileResource;

class OrganizationProfileController extends Controller
{
    /**
     * OrganizationProfileController Constructor.
     *
     * @param OrganizationProfileService $service.
     */
    public function __construct(
        protected OrganizationProfileService $service
    ) {}

    /**
     * Display a paginated listing of Organization Profiles with search and filtering capabilities.
     * Super admin and system admin can see all profiles with any status.
     * Regular users can only see active organization profiles.
     *
     * @param Request $request The HTTP request containing query filters and search parameters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $searchTerm = $request->input('search');
        $filters = $request->except(['page', 'per_page', 'search']);
        $perPage = (int) $request->input('per_page', 15);

        // Check user role and apply appropriate filtering
        $user = Auth::user();
        if ($user && !($user->hasRole('super_administrator') || $user->hasRole('system_admin'))) {
            // Regular users can only see active organization profiles
            $filters['status'] = 'active';
        }

        // Add search term to filters if provided
        if ($searchTerm) {
            $filters['search'] = $searchTerm;
        }

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'OrganizationProfile list fetched successfully');
    }

    /**
     * Store a newly created OrganizationProfile in storage.
     *
     * @param StoreOrganizationProfileRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreOrganizationProfileRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return $this->storeForAdmin($request);
        } else {
            return $this->storeForUser($request);
        }
    }

    /**
     * Store a newly created OrganizationProfile for admin users.
     * Admin can create profile for any user.
     *
     * @param StoreOrganizationProfileRequest $request The validated form request.
     * @return JsonResponse
     */
    public function storeForAdmin(StoreOrganizationProfileRequest $request): JsonResponse
    {
        $data = $request->validated();
        $userId = $data['user_id'];
        unset($data['user_id']);

        // Check if user already has an organization profile
        $existingProfile = $this->service->findByUserId($userId);
        if ($existingProfile) {
            return $this->error('User already has an organization profile. Each user can only have one organization profile.', 422);
        }

        // Create profile with the specified user_id
        $finalData = array_merge($data, ['user_id' => $userId]);
        $item = $this->service->create($finalData);

        return $this->success(new OrganizationProfileResource($item), 'OrganizationProfile created successfully by admin');
    }

    /**
     * Store a newly created OrganizationProfile for regular users.
     * User can only create profile for themselves.
     *
     * @param StoreOrganizationProfileRequest $request The validated form request.
     * @return JsonResponse
     */
    public function storeForUser(StoreOrganizationProfileRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        // Check if user already has an organization profile
        $existingProfile = $this->service->findByUserId($user->id);
        if ($existingProfile) {
            return $this->error('You already have an organization profile. Each user can only have one organization profile.', 422);
        }

        // Regular users can only create profile for themselves
        $finalData = array_merge($data, ['user_id' => $user->id]);
        $item = $this->service->create($finalData);

        return $this->success(new OrganizationProfileResource($item), 'OrganizationProfile created successfully');
    }

    /**
     * Display the specified OrganizationProfile.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        // Check if user is not super admin or system admin, then verify activation
        $user = Auth::user();
        if ($user && !($user->hasRole('super_administrator') || $user->hasRole('system_admin'))) {
            // Regular users can only view active organization profiles with active users
            if ($item->user && ($item->user->status !== 'active' || $item->status !== 'active')) {
                return $this->error('Organization profile is not active', 403);
            }
        }

        return $this->success(new OrganizationProfileResource($item), 'OrganizationProfile fetched successfully');
    }

    /**
     * Update the specified OrganizationProfile in storage.
     *
     * @param UpdateOrganizationProfileRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateOrganizationProfileRequest $request, int|string $id): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return $this->updateForAdmin($request, $id);
        } else {
            return $this->updateForUser($request, $id);
        }
    }

    /**
     * Update the specified OrganizationProfile for admin users.
     * Admin can update any profile.
     *
     * @param UpdateOrganizationProfileRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function updateForAdmin(UpdateOrganizationProfileRequest $request, int|string $id): JsonResponse
    {
        $data = $request->validated();
        $currentProfile = $this->service->findById($id);

        // Check if admin is trying to change user_id
        if (isset($data['user_id']) && $data['user_id'] !== $currentProfile->user_id) {
            // Check if the new user_id already has a profile
            $existingProfile = $this->service->findByUserId($data['user_id']);
            if ($existingProfile) {
                return $this->error('Cannot transfer profile ownership: User already has an organization profile. Each user can only have one organization profile.', 422);
            }
        }

        // Admin can update any profile
        $item = $this->service->update($id, $data);

        return $this->success(new OrganizationProfileResource($item), 'OrganizationProfile updated successfully by admin');
    }

    /**
     * Update the specified OrganizationProfile for regular users.
     * User can only update their own profile.
     *
     * @param UpdateOrganizationProfileRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function updateForUser(UpdateOrganizationProfileRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);
        $user = Auth::user();

        if ($item->user_id === $user->id) {
            $item = $this->service->update($id, $request->validated());
        } else {
            return $this->error('You can only update your own organization profile', 403);
        }

        return $this->success(new OrganizationProfileResource($item), 'OrganizationProfile updated successfully');
    }

    /**
     * Remove the specified OrganizationProfile from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return $this->destroyForAdmin($id);
        } else {
            return $this->destroyForUser($id);
        }
    }

    /**
     * Remove the specified OrganizationProfile for admin users.
     * Admin can delete any profile.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroyForAdmin(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'OrganizationProfile deleted successfully by admin');
    }

    /**
     * Remove the specified OrganizationProfile for regular users.
     * User can only delete their own profile.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroyForUser(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);
        $user = Auth::user();

        if (!($user->hasRole('super_administrator') || $user->hasRole('system_admin'))) {
            if ($item->user_id !== $user->id) {
                return $this->error('You can only delete your own organization profile', 403);
            }
        }

        $this->service->delete($id);

        return $this->success(null, 'OrganizationProfile deleted successfully');
    }

    /**
     * Display a paginated listing of Organization Profiles with relationships loaded.
     *
     * @param Request $request The HTTP request containing query filters and relations.
     * @return JsonResponse
     */
    public function indexWithRelations(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'relations' => 'sometimes|array',
            'relations.*' => 'string|in:user,opportunities'
        ]);

        $searchTerm = $request->input('search');
        $filters = $request->except(['page', 'per_page', 'search', 'relations']);
        $relations = $validated['relations'] ?? [];
        $perPage = (int) $request->input('per_page', 15);

        // Check user role and apply appropriate filtering
        $user = Auth::user();
        if ($user && !($user->hasRole('super_administrator') || $user->hasRole('system_admin'))) {
            // Regular users can only see active organization profiles
            $filters['status'] = 'active';
        }

        // Add search term to filters if provided
        if ($searchTerm) {
            $filters['search'] = $searchTerm;
        }

        $data = $this->service->getAllWithRelations($relations, $filters, $perPage);

        return $this->paginate(OrganizationProfileResource::collection($data), 'OrganizationProfile list with relations fetched successfully');
    }

    /**
     * Display the specified OrganizationProfile with relationships loaded.
     *
     * @param int|string $id The primary key value.
     * @param Request $request The HTTP request containing relations parameter.
     * @return JsonResponse
     */
    public function showWithRelations(int|string $id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'relations' => 'sometimes|array',
            'relations.*' => 'string|in:user,opportunities'
        ]);

        $relations = $validated['relations'] ?? [];

        // Parse relations parameter
        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }

        $item = $this->service->findByIdWithRelations($id, $relations);

        // Check if user is not super admin or system admin, then verify activation
        $user = Auth::user();
        if ($user && !($user->hasRole('super_administrator') || $user->hasRole('system_admin'))) {
            // Regular users can only view active organization profiles with active users
            if ($item->user && ($item->user->status !== 'active' || $item->status !== 'active')) {
                return $this->error('Organization profile is not active', 403);
            }
        }

        return $this->success(new OrganizationProfileResource($item), 'OrganizationProfile with relations fetched successfully');
    }

    /**
     * List all active organizations.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listActive(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->listActive($filters, $perPage);

        return $this->paginate($data, 'Active organization profiles list fetched successfully');
    }

    /**
     * List all not-active organizations.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listNotActive(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->listNotActive($filters, $perPage);

        return $this->paginate($data, 'Inactive organization profiles list fetched successfully');
    }

    /**
     * Activate an organization.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function activate(int|string $id): JsonResponse
    {
        $this->service->activate($id);

        return $this->success(null, 'OrganizationProfile activated successfully');
    }

    /**
     * Deactivate an organization.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function deactivate(int|string $id): JsonResponse
    {
        $this->service->deactivate($id);

        return $this->success(null, 'OrganizationProfile deactivated successfully');
    }

    /**
     * Get organizations by type.
     *
     * @param Request $request
     * @param string $type
     * @return JsonResponse
     */
    public function getByType(Request $request, string $type): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getByType($type, $filters, $perPage);

        return $this->paginate($data, "Organization profiles of type '{$type}' fetched successfully");
    }

    /**
     * Get organizations with opportunities.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getWithOpportunities(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getWithOpportunities($filters, $perPage);

        return $this->paginate($data, 'Organization profiles with opportunities fetched successfully');
    }

    /**
     * Get organizations created in date range.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getByDateRange(Request $request): JsonResponse
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $filters = $request->except(['page', 'per_page', 'from_date', 'to_date']);
        $perPage = (int) $request->input('per_page', 15);

        if (!$fromDate || !$toDate) {
            return $this->error('Both from_date and to_date are required', 422);
        }

        $data = $this->service->getByDateRange($fromDate, $toDate, $filters, $perPage);

        return $this->paginate($data, 'Organization profiles in date range fetched successfully');
    }

    /**
     * Get all opportunities for a specific organization.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function getOrganizationOpportunities(int|string $id): JsonResponse
    {
        $opportunities = $this->service->getOrganizationOpportunities($id);

        return $this->success($opportunities, 'Organization opportunities fetched successfully');
    }

    /**
     * Check if user has an organization profile.
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function userHasProfile(int $userId): JsonResponse
    {
        $hasProfile = $this->service->userHasProfile($userId);

        return $this->success([
            'user_id' => $userId,
            'has_profile' => $hasProfile
        ], 'User profile check completed successfully');
    }

    /**
     * Get organization profile by user ID.
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getByUserId(int $userId): JsonResponse
    {
        $profile = $this->service->findByUserId($userId);

        if (!$profile) {
            return $this->error('Organization profile not found for this user', 404);
        }

        return $this->success(new OrganizationProfileResource($profile), 'Organization profile fetched successfully');
    }

    /**
     * Get the current user's organization profile.
     *
     * @return JsonResponse
     */
    public function getMyOrganizationProfile(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        $profile = $this->service->findByUserId($user->id);

        if (!$profile) {
            return $this->error('Organization profile not found for this user', 404);
        }

        return $this->success(new OrganizationProfileResource($profile), 'My organization profile fetched successfully');
    }

    /**
     * Update the current user's organization profile.
     *
     * @param UpdateOrganizationProfileRequest $request
     * @return JsonResponse
     */
    public function updateMyOrganizationProfile(UpdateOrganizationProfileRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        $profile = $this->service->findByUserId($user->id);

        if (!$profile) {
            return $this->error('Organization profile not found for this user', 404);
        }

        $data = $request->validated();
        $updatedProfile = $this->service->update($profile->id, $data);

        return $this->success(new OrganizationProfileResource($updatedProfile), 'My organization profile updated successfully');
    }

    /**
     * Get organization statistics.
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        $statistics = $this->service->getStatistics();

        return $this->success($statistics, 'OrganizationProfile statistics fetched successfully');
    }
}
