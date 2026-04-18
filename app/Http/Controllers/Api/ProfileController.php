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
     * Display a paginated listing of Profiles.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Profile list fetched successfully');
    }

    /**
     * Store a newly created Profile in storage.
     *
     * @param StoreProfileRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreProfileRequest $request): JsonResponse
    {
        $data = $request->validated() + ['user_id' => Auth::user()->id];

        $item = $this->service->create($data);

        return $this->success(new ProfileResource($item), 'Profile created successfully');
    }

    /**
     * Display the specified Profile.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

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
     * 
     * @param UpdateProfileRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success(new ProfileResource($item), 'Profile updated successfully');
    }

    /**
     * Remove the specified Profile from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
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
            'relations.*' => 'string|in:skills,users,volunteers'
        ]);

        $relations = $validated['relations'] ?? [];
        $filters = $request->except(['page', 'per_page', 'relations']);
        $perPage = (int) $request->input('per_page', 15);

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
            'relations.*' => 'string|in:skills,users,volunteers'
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
