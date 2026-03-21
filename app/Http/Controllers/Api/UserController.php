<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\StoreUserRequest;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * Display a paginated listing of Users.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

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
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

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
}