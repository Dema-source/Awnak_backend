<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $guard = 'sanctum';

        // Definition of basic Permissions

        $permissions = [
            /*
            |--------------------------------------------------------------------------
            | Super_Admin & Admin
            |--------------------------------------------------------------------------
            */
            // Roles 
            'roles.read',
            'roles.create',
            'roles.update',
            'roles.delete',
            // Permissions
            'permissions.read',
            'permissions.create',
            'permissions.update',
            'permissions.delete',
            'permissions.check',
            'permissions.read',
            // 'roles.assign',
            // 'roles.remove',
            // 'roles.read',
            // 'roles.create',
            // 'roles.update',
            // 'roles.delete',
            // // Permissions
            // 'permissions.assign',
            // 'permissions.remove',
            // 'permissions.check',
            // 'permissions.read',
            // Users  
            'users.read',
            'users.create',
            'users.update',
            'users.delete',
            'users.status.update',

            //Profile
            'profile.create',
            'profile.read',
            'profile.update',
            'profile.delete',
            'organization.profile.delete',

            // Badges
            'badges.create',
            'badges.update',
            'badges.delete',

            // Certificates
            'certificates.create',
            'certificates.update',
            'certificates.delete',
            'certificates.view',
            'certificates.viewAny',

            // Skills
            'skills.read',
            'skills.create',
            'skills.update',
            'skills.delete',

            /*
            |--------------------------------------------------------------------------
            | Volunteer
            |--------------------------------------------------------------------------
            */
            // Profiles
            'profile.create.own',
            'profile.read.own',
            'profile.update.own',
            // Opportunity
            'opportunity.search',
            'opportunity.apply',
            // Tasks
            'tasks.read.assigned',
            'tasks.update.assigned',
            // Evaluations
            'evaluations.read.own',
            'evaluations.create.assigned.tasks',

            /*
            |--------------------------------------------------------------------------
            | Opportunity_Manager
            |--------------------------------------------------------------------------
            */
            // Opportunities
            'opportunity.create',
            'opportunity.read.own',
            'opportunity.update.own',
            'opportunity.delete.own',

            // Applications
            'applications.read',
            'volunteers.read.applicants',
            'volunteers.assign',

            /*
            |--------------------------------------------------------------------------
            | Organization
            |--------------------------------------------------------------------------
            */
            // Organization Profile
            'organization.profile.create.own',
            'organization.profile.read.own',
            'organization.profile.update.own',

            'organization.volunteers.read',
            'organization.volunteers.evaluate',

            // Opportunities
            'organizations.opportunities.create',
            'organizations.opportunities.update',
            'organizations.opportunities.delete',
            'organizations.opportunities.publish',

            // Evaluations
            'evaluations.create',
            'evaluations.update',
            'evaluations.delete',
            'evaluations.view',
            'evaluations.viewAny',

            // Badges
            'volunteersBadge.create',
            'volunteersBadge.update',
            'volunteersBadge.delete',
            'volunteersBadge.view',
            'volunteersBadge.viewAny',

            /*
            |--------------------------------------------------------------------------
            | Volunteer_Coordinator 
            |--------------------------------------------------------------------------
            */
            'volunteers.read.managed',
            'tasks.read.managed',

            /*
            |--------------------------------------------------------------------------
            | Performance_Evaluator 
            |--------------------------------------------------------------------------
            */
            'reports.performance.read',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => $guard,
            ]);
        }

        // Definition of basic roles
        $roles = [
            'super_administrator' => [
                'roles.read',
                'roles.create',
                'roles.update',
                'roles.delete',
                // ===============
                'permissions.read',
                'permissions.create',
                'permissions.update',
                'permissions.delete',

                // 'roles.assign',
                // 'roles.remove',
                // 'roles.read',
                // 'roles.create',
                // 'roles.update',
                // 'roles.delete',

                // 'permissions.assign',
                // 'permissions.remove',
                'permissions.check',
                'permissions.read',

                'users.read',
                'users.create',
                'users.update',
                'users.delete',
                'users.status.update',

                'roles.read',
                'roles.create',
                'roles.update',
                'roles.delete',

                'profile.create',
                'profile.read',
                'profile.update',
                'profile.delete',

                'profile.read.own',
                'profile.update.own',

                'badges.create',
                'badges.update',
                'badges.delete',

                'certificates.create',
                'certificates.update',
                'certificates.delete',
                'certificates.view',
                'certificates.viewAny',

                'skills.read',
                'skills.create',
                'skills.update',
                'skills.delete',

                'evaluations.create',
                'evaluations.update',
                'evaluations.delete',
                'evaluations.view',
                'evaluations.viewAny',

                'volunteersBadge.create',
                'volunteersBadge.update',
                'volunteersBadge.delete',
                'volunteersBadge.view',
                'volunteersBadge.viewAny',
            ],

            'system_admin' => [
                'users.read',
                'users.create',
                'users.update',
                'users.delete',
                'users.status.update',

                'profile.create',
                'profile.read',
                'profile.update',
                'profile.delete',

                'profile.read.own',
                'profile.update.own',

                'badges.create',
                'badges.update',
                'badges.delete',

                'certificates.create',
                'certificates.update',
                'certificates.delete',
                'certificates.view',
                'certificates.viewAny',

                'skills.read',
                'skills.create',
                'skills.update',
                'skills.delete',

                'evaluations.create',
                'evaluations.update',
                'evaluations.delete',
                'evaluations.view',
                'evaluations.viewAny',

                'volunteersBadge.create',
                'volunteersBadge.update',
                'volunteersBadge.delete',
                'volunteersBadge.view',
                'volunteersBadge.viewAny',
            ],
            'volunteer' => [
                'profile.create.own',
                'profile.read.own',
                'profile.update.own',

                'opportunity.search',
                'opportunity.apply',

                'tasks.read.assigned',
                'tasks.update.assigned',

                'evaluations.read.own',
                'evaluations.create.assigned.tasks',

                'skills.read',

                'certificates.view',
                'certificates.viewAny',

                'volunteersBadge.view',
                'volunteersBadge.viewAny',
            ],
            'opportunity_manager' => [
                'profile.create.own',
                'profile.read.own',
                'profile.update.own',

                'opportunity.create',
                'opportunity.read.own',
                'opportunity.update.own',
                'opportunity.delete.own',

                'applications.read',
                'volunteers.read.applicants',
                'volunteers.assign',

                'skills.read',
            ],
            'organization_admin' => [
                'organization.profile.create.own',
                'organization.profile.read.own',
                'organization.profile.update.own',

                'organization.volunteers.read',
                'organization.volunteers.evaluate',

                'organizations.opportunities.create',
                'organizations.opportunities.update',
                'organizations.opportunities.delete',
                'organizations.opportunities.publish',

                'evaluations.create',
                'evaluations.update',
                'evaluations.delete',
                'evaluations.view',
                'evaluations.viewAny',

                'skills.read',

                'volunteersBadge.create',
                'volunteersBadge.update',
                'volunteersBadge.delete',
                'volunteersBadge.view',
                'volunteersBadge.viewAny',
            ],
            'volunteer_coordinator' => [
                'profile.create.own',
                'profile.read.own',
                'profile.update.own',

                'volunteers.read.managed',

                'tasks.read.managed',

                'evaluations.create',
                'evaluations.update',
                'evaluations.delete',
                'evaluations.view',
                'evaluations.viewAny',

                'skills.read',

                'volunteersBadge.create',
                'volunteersBadge.update',
                'volunteersBadge.delete',
                'volunteersBadge.view',
                'volunteersBadge.viewAny',
            ],
            'performance_evaluator' => [
                'profile.create.own',
                'profile.read.own',
                'profile.update.own',

                'reports.performance.read',

                'certificates.create',
                'certificates.update',
                'certificates.delete',
                'certificates.view',
                'certificates.viewAny',

                'skills.read',
            ],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => $guard,
            ]);

            $role->syncPermissions($perms);
        }


        // update cache to know about the newly created permissions (required if using WithoutModelEvents in seeders)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
