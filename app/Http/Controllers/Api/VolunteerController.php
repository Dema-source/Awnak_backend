<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Volunteer\StoreVolunteerRequest;
use App\Http\Requests\Api\Volunteer\UpdateVolunteerRequest;
use App\Http\Requests\Api\Volunteer\UpdateVolunteerStatusRequest;
use App\Http\Resources\VolunteerResource;
use App\Services\VolunteerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerController extends Controller
{
    /**
     * VolunteerController Constructor.
     *
     * @param VolunteerService $service.
     */
    public function __construct(
        protected VolunteerService $service
    ) {}

    /**
     * Display a paginated listing of Volunteers.
     *
     * @param Request $request The HTTP request containing query filters.
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
            // Regular users can only see volunteers with active users
            $filters['active'] = true;
        }

        // Add search term to filters if provided
        if ($searchTerm) {
            $filters['search'] = $searchTerm;
        }

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Volunteer list fetched successfully');
    }

    /**
     * Store a newly created Volunteer in storage.
     * Routes to appropriate method based on user role.
     *
     * @param StoreVolunteerRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreVolunteerRequest $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return $this->storeForAdmin($request);
        } else {
            return $this->storeForUser($request);
        }
    }

    /**
     * Store a newly created Volunteer for admin users.
     * Admin can create volunteer for any user by specifying profile_id.
     *
     * @param StoreVolunteerRequest $request The validated form request.
     * @return JsonResponse
     */
    public function storeForAdmin(StoreVolunteerRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Admin must specify profile_id in request
        if (isset($data['profile_id'])) {
            $profileId = $data['profile_id'];
            unset($data['profile_id']);
        } else {
            return $this->error('Profile ID is required for admin users', 422);
        }

        // Check if profile already has a volunteer
        $existingVolunteer = \App\Models\Volunteer::where('profile_id', $profileId)->first();
        if ($existingVolunteer) {
            return $this->error('Profile already has a volunteer. Each profile can only have one volunteer.', 422);
        }

        // Create volunteer with the specified profile_id
        $finalData = array_merge($data, ['profile_id' => $profileId]);
        $item = $this->service->create($finalData);

        return $this->success(new VolunteerResource($item), 'Volunteer created successfully by admin');
    }

    /**
     * Store a newly created Volunteer for regular users.
     * User can only create volunteer for themselves.
     *
     * @param StoreVolunteerRequest $request The validated form request.
     * @return JsonResponse
     */
    public function storeForUser(StoreVolunteerRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        // Regular users can only create volunteer for themselves
        if (!$user->profile) {
            return $this->error('You must have a profile to create a volunteer record', 422);
        }

        $profileId = $user->profile->id;

        // Check if profile already has a volunteer
        $existingVolunteer = \App\Models\Volunteer::where('profile_id', $profileId)->first();
        if ($existingVolunteer) {
            return $this->error('You already have a volunteer record. Each profile can only have one volunteer.', 422);
        }

        // Create volunteer with the authenticated user's profile ID
        $finalData = array_merge($data, ['profile_id' => $profileId]);
        $item = $this->service->create($finalData);

        return $this->success(new VolunteerResource($item), 'Volunteer created successfully');
    }

    /**
     * Display the specified Volunteer.
     * Any user can view volunteers if the associated profile's user is active.
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
            // Check if the user associated with this volunteer's profile is active
            if ($item->profile && $item->profile->user && ($item->status !== 'active' || $item->profile->user->status !== 'active')) {
                return $this->error('Volunteer not found', 404);
            }
        }

        return $this->success(new VolunteerResource($item), 'Volunteer fetched successfully');
    }

    /**
     * Update the specified Volunteer in storage.
     * Routes to appropriate method based on user role.
     * 
     * @param UpdateVolunteerRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateVolunteerRequest $request, int|string $id): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('super_administrator') || $user->hasRole('system_admin')) {
            return $this->updateForAdmin($request, $id);
        } else {
            return $this->updateForUser($request, $id);
        }
    }

    /**
     * Update the specified Volunteer for admin users.
     * Admin can update any volunteer, but cannot change profile_id if target profile already has a volunteer.
     * 
     * @param UpdateVolunteerRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function updateForAdmin(UpdateVolunteerRequest $request, int|string $id): JsonResponse
    {
        $data = $request->validated();
        $item = $this->service->findById($id);

        // Check if admin is trying to change profile_id
        if (isset($data['profile_id']) && $data['profile_id'] !== $item->profile_id) {
            // Check if the new profile_id already has a volunteer
            $existingVolunteer = \App\Models\Volunteer::where('profile_id', $data['profile_id'])->first();
            if ($existingVolunteer) {
                return $this->error('Cannot transfer volunteer ownership: Profile already has a volunteer. Each profile can only have one volunteer.', 422);
            }
        }

        // Admin can update any volunteer
        $item = $this->service->update($id, $data);

        return $this->success(new VolunteerResource($item), 'Volunteer updated successfully by admin');
    }

    /**
     * Update the specified Volunteer for regular users.
     * User can only update their own volunteer.
     * 
     * @param UpdateVolunteerRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function updateForUser(UpdateVolunteerRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);
        $user = Auth::user();

        // User can only update their own volunteer (through their profile)
        if (!$user->profile || $item->profile_id !== $user->profile->id) {
            return $this->error('You can only update your own volunteer record', 403);
        }

        $item = $this->service->update($id, $request->validated());

        return $this->success(new VolunteerResource($item), 'Volunteer updated successfully');
    }

    /**
     * Remove the specified Volunteer from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Volunteer deleted successfully');
    }

    /**
     * Get volunteers with relationships loaded.
     *
     * @param Request $request The HTTP request containing query filters and relations.
     * @return JsonResponse
     */
    public function indexWithRelations(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'relations' => 'sometimes|array',
            'relations.*' => 'string|in:profile,user,applications,tasks,certificates,badges'
        ]);

        $relations = $validated['relations'] ?? [];
        $searchTerm = $request->input('search');
        $filters = $request->except(['page', 'per_page', 'relations', 'search']);
        $perPage = (int) $request->input('per_page', 15);

        // Check user role and apply appropriate filtering
        $user = Auth::user();
        if ($user && !($user->hasRole('super_administrator') || $user->hasRole('system_admin'))) {
            // Regular users can only see volunteers with active users
            $filters['active'] = true;
        }

        // Add search term to filters if provided
        if ($searchTerm) {
            $filters['search'] = $searchTerm;
        }

        $data = $this->service->getAllWithRelations($relations, $filters, $perPage);

        return $this->paginate($data, 'Volunteers with relations fetched successfully');
    }

    /**
     * Display the specified Volunteer with relations.
     *
     * @param int|string $id The primary key value.
     * @param Request $request The HTTP request containing relations parameter.
     * @return JsonResponse
     */
    public function showWithRelations(int|string $id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'relations' => 'sometimes|array',
            'relations.*' => 'string|in:profile,user,applications,tasks,certificates,badges'
        ]);

        $relations = $validated['relations'] ?? ['profile', 'user'];
        $item = $this->service->findByIdWithRelations($id, $relations);

        if (!$item) {
            return $this->error('Volunteer not found', 404);
        }

        // Check if user is not super admin or system admin, then verify activation
        $user = Auth::user();
        if ($user && !($user->hasRole('super_administrator') || $user->hasRole('system_admin'))) {
            // Check if the user associated with this volunteer's profile is active
            if ($item->profile && $item->profile->user && ($item->status !== 'active' || $item->profile->user->status !== 'active')) {
                return $this->error('Volunteer not found', 404);
            }
        }

        return $this->success(new VolunteerResource($item), 'Volunteer with relations fetched successfully');
    }

    /**
     * Get active volunteers.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listActive(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getActive($perPage);

        return $this->paginate($data, 'Active volunteers list fetched successfully');
    }

    /**
     * Get inactive volunteers.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listInactive(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getInactive($perPage);

        return $this->paginate($data, 'Inactive volunteers list fetched successfully');
    }

    /**
     * Get pending volunteers.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listPending(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getPending($perPage);

        return $this->paginate($data, 'Pending volunteers list fetched successfully');
    }

    /**
     * Get blocked volunteers.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listBlocked(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $data = $this->service->getBlocked($perPage);

        return $this->paginate($data, 'Blocked volunteers list fetched successfully');
    }

    /**
     * Activate a volunteer.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function activate(int|string $id): JsonResponse
    {
        $volunteer = $this->service->activate($id);

        return $this->success(new VolunteerResource($volunteer), 'Volunteer activated successfully');
    }

    /**
     * Deactivate a volunteer.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function deactivate(int|string $id): JsonResponse
    {
        $volunteer = $this->service->deactivate($id);

        return $this->success(new VolunteerResource($volunteer), 'Volunteer deactivated successfully');
    }

    /**
     * Block a volunteer.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function block(int|string $id): JsonResponse
    {
        $volunteer = $this->service->block($id);

        return $this->success(new VolunteerResource($volunteer), 'Volunteer blocked successfully');
    }

    /**
     * Get volunteer by profile ID.
     *
     * @param int $profileId
     * @return JsonResponse
     */
    public function getByProfileId(int $profileId): JsonResponse
    {
        $volunteer = $this->service->findByProfileId($profileId);

        if (!$volunteer) {
            return $this->error('Volunteer not found', 404);
        }

        return $this->success(new VolunteerResource($volunteer), 'Volunteer fetched successfully');
    }

    /**
     * Get volunteer by user ID.
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getByUserId(int $userId): JsonResponse
    {
        $volunteer = $this->service->findByUserId($userId);

        if (!$volunteer) {
            return $this->error('Volunteer not found', 404);
        }

        return $this->success(new VolunteerResource($volunteer), 'Volunteer fetched successfully');
    }

    /**
     * Check if user has a volunteer.
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function userHasVolunteer(int $userId): JsonResponse
    {
        $hasVolunteer = $this->service->userHasVolunteer($userId);

        return $this->success(['has_volunteer' => $hasVolunteer], 'Volunteer status checked successfully');
    }
    
    /**
     * Get volunteer statistics.
     *
     * @return JsonResponse
     */
    public function getStatistics(): JsonResponse
    {
        $statistics = $this->service->getStatistics();

        return $this->success($statistics, 'Volunteer statistics fetched successfully');
    }
}
