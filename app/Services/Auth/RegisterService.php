<?php

namespace App\Services\Auth;

use App\Repositories\Interfaces\Auth\AuthRepositoryInterface;
use App\Repositories\Interfaces\ProfileSkillRepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;

/**
 * Service layer for handling register business logic related to the "AuthRepositoryInterface" repository.
 */
class RegisterService
{
    /**
     * UserService Constructor.
     *
     * @param \App\Repositories\Interfaces\Auth\AuthRepositoryInterface $repository
     * @param \App\Repositories\Interfaces\ProfileSkillRepositoryInterface $profileSkillRepository
     */
    public function __construct(
        protected AuthRepositoryInterface $repository,
        protected ProfileSkillRepositoryInterface $profileSkillRepository
    ) {}

    /**
     * Handle the incoming registration request.
     *
     * @param array $data
     * @return array
     */
    public function handle(array $data): array
    {
        $user = $this->repository->createUser($data);

        event(new Registered($user));

        $token = $user->createToken('api_token')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Register a new organization admin with user and organization profile in a single transaction.
     *
     * @param array $data
     * @return array
     */
    public function registerOrganizationAdmin(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Create user
            $user = $this->repository->createUser($data['user']);

            // Assign role
            $user->assignRole('organization_admin');

            // Create organization profile
            $user->organization_profile()->create($data['organization_profile']);

            // Fire registration event
            event(new Registered($user));

            // Create API token
            $token = $user->createToken('api_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token
            ];
        });
    }

    /**
     * Register a new volunteer with user, profile, skills(if exists), and volunteer record in a single transaction.
     *
     * @param array $data
     * @return array
     */
    public function registerVolunteer(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Create user
            $user = $this->repository->createUser($data['user']);

            // Assign role
            $user->assignRole('volunteer');

            // Create profile
            $profile = $user->profile()->create($data['profile']);

            // Create volunteer record
            $profile->volunteer()->create($data['volunteer']);

            // Attach skills to profile using ProfileSkill repository (only if skills provided)
            if (!empty($data['skills'])) {
                $this->profileSkillRepository->attachSkills($profile->id, $data['skills']);
            }

            // Fire registration event
            event(new Registered($user));

            // Create API token
            $token = $user->createToken('api_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token
            ];
        });
    }

    /**
     * Register a new staff member with user, profile, and skills in a single transaction.
     *
     * @param array $data
     * @return array
     */
    public function registerStaff(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Create user
            $user = $this->repository->createUser($data['user']);

            // Create profile
            $profile = $user->profile()->create($data['profile']);

            // Attach skills to profile using ProfileSkill repository (only if skills provided)
            if (!empty($data['skills'])) {
                $this->profileSkillRepository->attachSkills($profile->id, $data['skills']);
            }

            // Fire registration event
            event(new Registered($user));

            // Create API token
            $token = $user->createToken('api_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token
            ];
        });
    }
}
