<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super_administrator = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('superadmin1234'),
        ]);

        $super_administrator->syncRoles(['super_administrator']);

        $system_admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin1234'),
        ]);

        $system_admin->syncRoles(['system_admin']);

        $volunteer = User::create([
            'name' => 'Volunteer',
            'email' => 'volunteer@gmail.com',
            'password' => Hash::make('volunteer1234'),
        ]);

        $volunteer->syncRoles(['volunteer']);

        User::factory()->count(10)->create();
    }
}
