<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('superadmin1234'),
        ]);
        DB::table('users')->insert([
            'name' => 'volunteer',
            'email' => 'volunteer@gmail.com',
            'password' => Hash::make('superadmin1234'),
        ]);
        DB::table('users')->insert([
            'name' => 'opportunity manager',
            'email' => 'opportunitymanager@gmail.com',
            'password' => Hash::make('superadmin1234'),
        ]);
        DB::table('users')->insert([
            'name' => 'volunteer coordinator',
            'email' => 'volunteercoordinator@gmail.com',
            'password' => Hash::make('superadmin1234'),
        ]);
        DB::table('users')->insert([
            'name' => 'evaluator',
            'email' => 'evaluator@gmail.com',
            'password' => Hash::make('superadmin1234'),
        ]);
    }
}
