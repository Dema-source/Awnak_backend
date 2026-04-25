<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VolunteerRegisterRequest;
use App\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;

class VolunteerRegistrationController extends Controller
{
    /**
     * VolunteerRegistrationController constructor.
     *
     * @param RegisterService $registerService
     */
    public function __construct(
        protected RegisterService $registerService
    ) {}

    /**
     * Register a new volunteer with user, profile, skills, and volunteer record.
     *
     * @param VolunteerRegisterRequest $request
     * @return JsonResponse
     */
    public function register(VolunteerRegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->getValidatedData();
            $item = $this->registerService->registerVolunteer($data);

            return $this->success([
                'user' => $item['user'],
                'profile' => $item['user']->profile,
                'volunteer' => $item['user']->profile->volunteer,
                'skills' => $item['user']->profile->skills,
                'token' => $item['token'],
            ], 'Volunteer registered successfully');

        } catch (\Exception $e) {
            return $this->error(
                'Registration failed: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get volunteer registration requirements.
     *
     * @return JsonResponse
     */
    public function requirements(): JsonResponse
    {
        return $this->success([
            'required_fields' => [
                'email' => 'email, unique',
                'password' => 'min:8, confirmed',
                'phone' => 'integer, optional',
                'name' => 'object with en/ar translations',
                'name.en' => 'string, max:255, required',
                'name.ar' => 'string, max:255, optional',
                'address' => 'object with en/ar translations',
                'address.en' => 'string, max:500, required',
                'address.ar' => 'string, max:500, optional',
                'age' => 'integer, min:18, max:120',
                'gender' => 'string, in:male,female',
                'bio' => 'string, max:1000, optional',
                'interests' => 'array, optional',
                'interests.*' => 'string, max:255',
                'languages' => 'array, max:5, optional',
                'availability' => 'array, min:1, max:7, optional',
                'experience_years' => 'string, in:1,2,3,4,5, optional',
                'skills' => 'array, optional, skill_ids'
            ],
            'experience_levels' => [
                '1' => 'Less than 1 year',
                '2' => '1-2 years',
                '3' => '2-3 years',
                '4' => '3-4 years',
                '5' => '5+ years'
            ],
            'example' => [
                'email' => 'jane@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'phone' => 105154,
                'name' => [
                    'en' => 'dema',
                    'ar' => 'deme'
                ],
                'address' => [
                    'en' => 'test address',
                    'ar' => 'test address arabic'
                ],
                'age' => 26,
                'gender' => 'female',
                'bio' => 'test bio',
                'interests' => ['environment', 'education', 'community service'],
                'languages' => [
                    'Arabic',
                    'English',
                    'Spanish'
                ],
                'availability' => [
                    [
                        'days' => 'sunday-wednesday,friday',
                        'from' => '09:00',
                        'to' => '14:00'
                    ]
                ],
                'experience_years' => '2',
                'skills' => [1, 2, 3] // Example skill IDs
            ]
        ], 'Volunteer registration requirements');
    }
}
