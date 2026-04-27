<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\StoreUserRequest;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * UserController Constructor.
     *
     * @param UserService $service.
     */
    public function __construct(
        protected UserService $service
    ) {}

    /**
     * Display a paginated listing of Users with search and filtering capabilities.
     * Volunteer and Organization roles can only see active users.
     *
     * @param Request $request The HTTP request containing query filters and search parameters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $searchTerm = $request->input('search');
        $filters = $request->except(['page', 'per_page', 'search']);
        $perPage = (int) $request->input('per_page', 15);

        // Convert string boolean values to proper booleans
        if (isset($filters['active'])) {
            $activeValue = $filters['active'];
            // Handle string representations of booleans
            if ($activeValue === 'false' || $activeValue === '0' || $activeValue === 0) {
                $filters['active'] = false;
            } elseif ($activeValue === 'true' || $activeValue === '1' || $activeValue === 1) {
                $filters['active'] = true;
            } else {
                // Fallback to boolean conversion
                $filters['active'] = (bool)$activeValue;
            }
        }

        // Use search if search term is provided, otherwise use filters
        if ($searchTerm) {
            $data = $this->service->search($searchTerm, $filters, $perPage);
        } else {
            $data = $this->service->filter($filters, $perPage);
        }

        return $this->paginate($data, 'User list fetched successfully');
    }

    /**
     * Store a newly created User in storage.
     *
     * @param StoreUserRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success($item, 'User created successfully');
    }

    /**
     * Display the specified User.
     * Volunteer and Organization roles can only view active users.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        // Check user role and restrict access for volunteer and organization roles
        $user = Auth::user();
        if ($user && ($user->hasRole('volunteer') || $user->hasRole('organization_admin'))) {
            // Only allow access to active users
            if ($item->status !== 'active') {
                return $this->error('User not found', 404);
            }
        }

        return $this->success($item, 'User fetched successfully');
    }

    /**
     * Update the specified User in storage.
     * 
     * @param UpdateUserRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'User updated successfully');
    }

    /**
     * Remove the specified User from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'User deleted successfully');
    }

    /**
     * Inactive the specified User.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function inactive(int|string $id): JsonResponse
    {
        $user = $this->service->inactive($id);

        return $this->success($user, 'User inactivated successfully');
    }
}