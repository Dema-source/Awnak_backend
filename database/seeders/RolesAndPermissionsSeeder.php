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

        // Definition of basic roles
        $roles = [
            'super_administrator',
            'system_admin',
            'volunteer',
            'opportunity_manager',
            'organization_admin',
            'volunteer_coordinator',
            'performance_evaluator',
        ];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
            ]);
        }


    }
}
