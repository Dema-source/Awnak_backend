<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OrganizationAdminRegisterRequest;
use App\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;

class OrganizationAdminRegistrationController extends Controller
{
    /**
     * OrganizationAdminRegistrationController constructor.
     *
     * @param RegisterService $registerService
     */
    public function __construct(
        protected RegisterService $registerService
    ) {}

    /**
     * Register a new organization admin with user and organization profile.
     *
     * @param OrganizationAdminRegisterRequest $request
     * @return JsonResponse
     */
    public function register(OrganizationAdminRegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->getValidatedData();
            $item = $this->registerService->registerOrganizationAdmin($data);

            return $this->success([
                'user' => $item['user'],
                'organization_profile' => $item['user']->organizationProfile,
                'token' => $item['token'],
            ], 'Organization admin registered successfully');

        } catch (\Exception $e) {
            return $this->error(
                'Registration failed: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get organization admin registration requirements.
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
                'organization_profile' => [
                    'license_number' => 'string, max:255, required, unique',
                    'type' => 'string, required, organization type',
                    'bio' => 'text, optional',
                    'website' => 'url, max:255, optional'
                ]
            ],
            'organization_types' => [
                'Charitable organization' => 'Charitable organization',
                'Civil society organization' => 'Civil society organization',
                'Voluntary educational/university institution' => 'Voluntary educational/university institution',
                'Hospital' => 'Hospital',
                'Religious organization' => 'Religious organization',
                'Company with a Corporate Social Responsibility (CSR) program' => 'Company with CSR program',
                'Student club/association' => 'Student club/association',
                'Environmental organization' => 'Environmental organization'
            ],
            'example' => [
                'email' => 'john@company.org',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'phone' => 1234567890,
                'name' => [
                    'en' => 'John Doe Organization',
                    'ar' => 'John Doe Organization'
                ],
                'address' => [
                    'en' => '123 Main St, City, State 12345',
                    'ar' => '123 Main St, City, State 12345'
                ],
                'organization_profile' => [
                    'license_number' => 'ORG-2024-001',
                    'type' => 'Charitable organization',
                    'bio' => 'Dedicated to community service',
                    'website' => 'https://company.org'
                ]
            ]
        ], 'Organization admin registration requirements');
    }
}
