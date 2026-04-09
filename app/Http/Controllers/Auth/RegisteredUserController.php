<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Controller responsible for handling user registration requests.
 */
class RegisteredUserController extends Controller
{
    /**
     * Constructor injecting the required RegisterService dependency.
     * 
     * @param RegisterService $service
     */
    public function __construct(
        protected RegisterService $service
    ) {}
    
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $data = $this->service->handle($request->validated());

        return $this->success(['access_token' => $data['token'], 'user' => new UserResource($data['user'])], 'Registered successfully');
    }
}
