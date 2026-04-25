<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StaffRegisterRequest;
use App\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;

class StaffRegistrationController extends Controller
{
    /**
     * StaffRegistrationController constructor.
     *
     * @param RegisterService $registerService
     */
    public function __construct(
        protected RegisterService $registerService
    ) {}

    /**
     * Register a new staff member with user, profile, and skills.
     *
     * @param StaffRegisterRequest $request
     * @return JsonResponse
     */
    public function register(StaffRegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->getValidatedData();
            $result = $this->registerService->registerStaff($data);

            return $this->success([
                'user' => $result['user'],
                'profile' => $result['user']->profile,
                'skills' => $result['user']->profile->skills,
                'token' => $result['token'],
            ], 'Staff member registered successfully');

        } catch (\Exception $e) {
            return $this->error(
                'Registration failed: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get staff registration requirements.
     *
     * @return JsonResponse
     */
    public function requirements(): JsonResponse
    {
        return $this->success([
            'required_fields' => [
                'name' => 'string, max:255',
                'email' => 'email, unique',
                'password' => 'min:8, confirmed',
                'phone' => 'integer, optional',
                'address' => 'string, optional',
                'role' => 'string, in:opportunity_manager,performance_evaluator,system_admin,volunteer_coordinator',
                'profile' => [
                    'bio' => 'string, max:1000, optional',
                    'age' => 'integer, min:18, max:120',
                    'gender' => 'string, in:male,female',
                    'interests' => 'array, optional'
                ],
                'skills' => 'array, min:1, skill_ids'
            ],
            'staff_roles' => [
                'opportunity_manager' => 'Manages opportunities and events',
                'performance_evaluator' => 'Evaluates volunteer performance',
                'system_admin' => 'System administrator',
                'volunteer_coordinator' => 'Coordinates volunteer activities'
            ],
            'example' => [
                'name' => 'Admin User',
                'email' => 'admin@company.org',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'phone' => 1234567890,
                'address' => '123 Admin St, City, State 12345',
                'role' => 'system_admin',
                'profile' => [
                    'bio' => 'System administrator with 10 years experience',
                    'age' => 30,
                    'gender' => 'male',
                    'interests' => ['technology', 'management']
                ],
                'skills' => [1, 2, 3] // Example skill IDs
            ]
        ], 'Staff registration requirements');
    }
}
