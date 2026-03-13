<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        // Definition of basic roles
        $roles = [
            'super_administrator',
            'volunteer',
            'opportunity_manager',
            'volunteer_coordinator',
            'evaluator',
        ];
        foreach ($roles as $roleName) {
            // We use firstOrCreate to avoid duplication upon restart.
            $role = Role::firstOrCreate(['name' => $roleName]);
        }

        // Definition of basic Permissions
        $defaultPermissions = [
            // Profile & Organization Profile
            'get profiles',
            'get profile',
            'create profile',
            'update profile',
            'remove profile',
            'delete profile',

            // Volunteer
            'get volunteers',
            'get volunteer',
            'create volunteer',
            'update volunteer',
            'delete volunteer',

            // Skill
            'get skills',
            'get skill',
            'create skill',
            'update skill',
            'delete skill',

            // Interest
            'get interests',
            'get interest',
            'create interest',
            'update interest',
            'delete interest',

            // Opportunity
            'get opportunities',
            'get opportunity',
            'create opportunity',
            'update opportunity',
            'remove opportunity',
            'delete opportunity',

            // Task
            'get tasks',
            'get task',
            'create task',
            'update task',
            'delete task',

            // Application
            'get applications',
            'get application',
            'create application',
            'update application',
            'cancel application',
            'delete application',
            'accept application',

            // Evaluation
            'get evaluations',
            'get evaluation',
            'create evaluation',
            'update evaluation',
            'remove evaluation',
            'delete evaluation',

            // Notification
            'send notifications',

            // Certificate
            'get certificates',
            'get certificate',
        ];
        foreach ($defaultPermissions as $permName) {
            Permission::firstOrCreate(['name' => $permName]);
        }

        // Role's Permissions
        $permissionsForRoles = [
            'super_administrator' => $defaultPermissions,
            'volunteer' => [
                'get profile',
                'create profile',
                'update profile',
                'remove profile',
                'get opportunities',
                'get opportunity',
                'get tasks',
                'get task',
                'get applications',
                'get application',
                'create application',
                'cancel application',
                'get evaluations',
                'get evaluation',
                'create evaluation',
                'update evaluation',
                'remove evaluation',
                'get certificates',
                'get certificate',
            ],
            'opportunity_manager' => [
                'get opportunities',
                'get opportunity',
                'create opportunity',
                'updated opportunity',
                'remove opportunity',
                'get applications',
                'get application',
                'accept application',
                'get profile',
                'get tasks',
                'get task',
            ],
            'volunteer_coordinator' => [
                'get profiles',
                'get profile',
                'get tasks',
                'get task',
                'get evaluations',
                'get evaluation',
                'create evaluation',
                'send notifications',
            ],
            'evaluator' => [
                'get evaluations',
                'get evaluation',
            ],
        ];

        // Link permissions to roles
        foreach ($permissionsForRoles as $roleName => $perms) {
            $role = Role::firstWhere('name', $roleName);
            if ($role) {
                foreach ($perms as $permName) {
                    $permission = Permission::firstWhere('name', $permName);
                    if ($permission) {
                        if (!$role->hasPermissionTo($permission)) {
                            $role->givePermissionTo($permission);
                        }
                    }
                }
            }
        }

        // update cache to know about the newly created permissions (required if using WithoutModelEvents in seeders)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
