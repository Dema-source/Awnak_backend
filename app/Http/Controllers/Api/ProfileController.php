<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\StoreProfileRequest;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * ProfileController Constructor.
     *
     * @param ProfileService $service.
     */
    public function __construct(
        protected ProfileService $service
    ) {}

    /**
     * Display a paginated listing of Profiles with search and filtering capabilities.
     * Super admin and system admin can see all profiles with any status.
     * Regular users can only see profiles of active users.
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
            // Regular users can only see profiles of active users
            $filters['active'] = true;
        }

        // Add search term to filters if provided
        if ($searchTerm) {
            $filters['search'] = $searchTerm;
        }

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate(ProfileResource::collection($data), 'Profile list fetched successfully');
    }

    /**
     * Store a newly created Profile in storage.
     * Routes to appropriate method based on user role.
     *
     * @param StoreProfileRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreProfileRequest $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return $this->storeForAdmin($request);
        } else {
            return $this->storeForUser($request);
        }
    }

    /**
     * Store a newly created Profile for admin users.
     * Admin can create profile for any user by specifying user_id.
     *
     * @param StoreProfileRequest $request The validated form request.
     * @return JsonResponse
     */
    public function storeForAdmin(StoreProfileRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Admin must specify user_id in request
        if (isset($data['user_id'])) {
            $userId = $data['user_id'];
            unset($data['user_id']);
        } else {
            return $this->error('User ID is required for admin users', 422);
        }

        // Check if user already has a profile
        $existingProfile = \App\Models\Profile::where('user_id', $userId)->first();
        if ($existingProfile) {
            return $this->error('User already has a profile. Each user can only have one profile.', 422);
        }

        // Create profile with the specified user_id
        $finalData = array_merge($data, ['user_id' => $userId]);
        $item = $this->service->create($finalData);

        return $this->success(new ProfileResource($item), 'Profile created successfully by admin');
    }

    /**
     * Store a newly created Profile for regular users.
     * User can only create profile for themselves.
     *
     * @param StoreProfileRequest $request The validated form request.
     * @return JsonResponse
     */
    public function storeForUser(StoreProfileRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        // Regular users can only create profile for themselves
        $userId = $user->id;

        // Check if user already has a profile
        $existingProfile = \App\Models\Profile::where('user_id', $userId)->first();
        if ($existingProfile) {
            return $this->error('You already have a profile. Each user can only have one profile.', 422);
        }

        // Create profile with the authenticated user's ID
        $finalData = array_merge($data, ['user_id' => $userId]);
        $item = $this->service->create($finalData);

        return $this->success(new ProfileResource($item), 'Profile created successfully');
    }

    /**
     * Display the specified Profile.
     * Any user can view profiles if the associated user is active.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        // Check if user is not super admin or system admin, then verify activation
        $user = Auth::user();
        if ($user && !($user->hasRole('super_administrator') || $user->hasRole('system_admin'))) {        // Check if the user associated with this profile is active
            if ($item->user && $item->user->status !== 'active') {
                return $this->error('Profile not found', 404);
            }
        }

        return $this->success(new ProfileResource($item), 'Profile fetched successfully');
    }

    /**
     * Get profile by user ID.
     *
     * @param int|string $userId The user ID.
     * @return JsonResponse
     */
    public function getByUserId(int|string $userId): JsonResponse
    {
        $item = $this->service->findByUserId($userId);

        return $this->success(new ProfileResource($item), 'Profile fetched successfully');
    }

    /**
     * Update the specified Profile in storage.
     * Routes to appropriate method based on user role.
     * 
     * @param UpdateProfileRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request, int|string $id): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return $this->updateForAdmin($request, $id);
        } else {
            return $this->updateForUser($request, $id);
        }
    }

    /**
     * Update the specified Profile for admin users.
     * Admin can update any profile, but cannot change user_id if target user already has a profile.
     * 
     * @param UpdateProfileRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function updateForAdmin(UpdateProfileRequest $request, int|string $id): JsonResponse
    {
        $data = $request->validated();
        $item = $this->service->findById($id);

        // Check if admin is trying to change user_id
        if (isset($data['user_id']) && $data['user_id'] !== $item->user_id) {
            // Check if the new user_id already has a profile
            $existingProfile = \App\Models\Profile::where('user_id', $data['user_id'])->first();
            if ($existingProfile) {
                return $this->error('Cannot transfer profile ownership: User already has a profile. Each user can only have one profile.', 422);
            }
        }

        // Admin can update any profile
        $item = $this->service->update($id, $data);

        return $this->success(new ProfileResource($item), 'Profile updated successfully by admin');
    }

    /**
     * Update the specified Profile for regular users.
     * User can only update their own profile.
     * 
     * @param UpdateProfileRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function updateForUser(UpdateProfileRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);
        $user = Auth::user();

        // User can only update their own profile
        if ($item->user_id !== $user->id) {
            return $this->error('You can only update your own profile', 403);
        }

        $item = $this->service->update($id, $request->validated());

        return $this->success(new ProfileResource($item), 'Profile updated successfully');
    }

    /**
     * Remove the specified Profile from storage.
     * Routes to appropriate method based on user role.
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
     * Remove the specified Profile for admin users.
     * Admin can delete any profile.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroyForAdmin(int|string $id): JsonResponse
    {
        // Admin can delete any profile
        $this->service->delete($id);

        return $this->success(null, 'Profile deleted successfully by admin');
    }

    /**
     * Remove the specified Profile for regular users.
     * User can only delete their own profile.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroyForUser(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);
        $user = Auth::user();

        // User can only delete their own profile
        if ($item->user_id !== $user->id) {
            return $this->error('You can only delete your own profile', 403);
        }

        $this->service->delete($id);

        return $this->success(null, 'Profile deleted successfully');
    }

    /**
     * Get profiles with relationships loaded.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function indexWithRelations(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'relations' => 'sometimes|array',
            'relations.*' => 'string|in:skills,user,volunteer'
        ]);

        $relations = $validated['relations'] ?? [];
        $searchTerm = $request->input('search');
        $filters = $request->except(['page', 'per_page', 'relations', 'search']);
        $perPage = (int) $request->input('per_page', 15);

        // Check user role and apply appropriate filtering
        $user = Auth::user();
        if ($user && !($user->hasRole('super_administrator') || $user->hasRole('system_admin'))) {
            // Regular users can only see profiles of active users
            $filters['active'] = true;
        }

        // Add search term to filters if provided
        if ($searchTerm) {
            $filters['search'] = $searchTerm;
        }

        $data = $this->service->getAllWithRelations($relations, $filters, $perPage);

        return $this->paginate($data, 'Profiles with relations fetched successfully');
    }

    /**
     * Get profile by ID with relationships.
     *
     * @param Request $request The HTTP request.
     * @param int|string $id The profile ID.
     * @return JsonResponse
     */
    public function showWithRelations(Request $request, int|string $id): JsonResponse
    {
        $validated = $request->validate([
            'relations' => 'sometimes|array',
            'relations.*' => 'string|in:skills,user,volunteer'
        ]);

        $relations = $validated['relations'] ?? [];
        $item = $this->service->findByIdWithRelations($id, $relations);

        return $this->success(new ProfileResource($item), 'Profile with relations fetched successfully');
    }

    /**
     * Search profiles by bio or interests.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => 'required|string|min:2'
        ]);

        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->searchByBioOrInterests($validated['search'], $perPage);

        return $this->paginate($data, 'Profiles search results fetched successfully');
    }

    /**
     * Get profiles by gender.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getByGender(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'gender' => 'required|in:Male,Female'
        ]);

        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getByGender($validated['gender'], $perPage);

        return $this->paginate($data, 'Profiles by gender fetched successfully');
    }

    /**
     * Get profiles by age range.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getByAgeRange(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'min_age' => 'required|integer|min:1',
            'max_age' => 'required|integer|min:1|gte:min_age'
        ]);

        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getByAgeRange($validated['min_age'], $validated['max_age'], $perPage);

        return $this->paginate($data, 'Profiles by age range fetched successfully');
    }

    /**
     * Get profiles with specific skills.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getBySkills(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'skill_ids' => 'required|array|min:1',
            'skill_ids.*' => 'required|integer|exists:skills,id'
        ]);

        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getBySkills($validated['skill_ids'], $perPage);

        return $this->paginate($data, 'Profiles by skills fetched successfully');
    }

    /**
     * Get my profile (authenticated user's profile).
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getMyProfile(Request $request): JsonResponse
    {
        $user = Auth::user();
        $profile = $this->service->findByUserId($user->id);

        return $this->success(new ProfileResource($profile), 'My profile fetched successfully');
    }

    /**
     * Update my profile (authenticated user's profile).
     *
     * @param UpdateProfileRequest $request The validated form request.
     * @return JsonResponse
     */
    public function updateMyProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = Auth::user();
        $profile = $this->service->findByUserId($user->id);

        if (!$profile) {
            return $this->error('Profile not found', 404);
        }

        $item = $this->service->update($profile->id, $request->validated());

        return $this->success(new ProfileResource($item), 'My profile updated successfully');
    }

    /**
     * Get profile statistics.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $statistics = $this->service->getStatistics();

        return $this->success($statistics, 'Profile statistics fetched successfully');
    }
}
