<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Models\Opportunity;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        // Seed countries first
        $this->call([
            CountrySeeder::class,
            CitySeeder::class,
        ]);

        // Create sample locations (minimal structure)
        $locations = [
            [
                'city_id' => 1, // New York
                'latitude' => 40.7589,
                'longitude' => -73.9851,
            ],
            [
                'city_id' => 2, // Los Angeles
                'latitude' => 34.0928,
                'longitude' => -118.3287,
            ],
            [
                'city_id' => 3, // Chicago
                'latitude' => 41.8819,
                'longitude' => -87.6278,
            ],
        ];

        $createdLocations = [];
        foreach ($locations as $locationData) {
            $location = Location::create($locationData);
            $createdLocations[] = $location;
        }

        // Create sample opportunities if they don't exist
        if (Opportunity::count() === 0) {
            $opportunities = [
                [
                    'organization_profile_id' => 1,
                    'title' => 'Web Development Volunteer',
                    'required_volunteer' => 3,
                    'start_date' => now()->addDays(7),
                    'end_date' => now()->addDays(30),
                    'status' => 'active',
                ],
                [
                    'organization_profile_id' => 1,
                    'title' => 'Community Garden Helper',
                    'required_volunteer' => 2,
                    'start_date' => now()->addDays(3),
                    'end_date' => now()->addDays(90),
                    'status' => 'active',
                ],
                [
                    'organization_profile_id' => 1,
                    'title' => 'Youth Mentor',
                    'required_volunteer' => 7,
                    'start_date' => now()->addDays(14),
                    'end_date' => now()->addDays(60),
                    'status' => 'active',
                ],
            ];

            $createdOpportunities = [];
            foreach ($opportunities as $opportunityData) {
                $opportunity = Opportunity::create($opportunityData);
                $createdOpportunities[] = $opportunity;
            }

            // Attach locations to opportunities with building-specific details
            // First opportunity gets all locations with different building details
            $createdOpportunities[0]->locations()->attach([
                $createdLocations[0]->id => [
                    'building_name' => 'Tech Hub Building',
                    'floor_number' => '5',
                    'apartment_number' => '501',
                    'landmark' => 'Near Central Park',
                ],
                $createdLocations[1]->id => [
                    'building_name' => 'Creative Center',
                    'floor_number' => '3',
                    'apartment_number' => '301',
                    'landmark' => 'Near Hollywood Walk of Fame',
                ],
                $createdLocations[2]->id => [
                    'building_name' => 'Innovation Tower',
                    'floor_number' => '10',
                    'apartment_number' => '1001',
                    'landmark' => 'Near Millennium Park',
                ],
            ]);

            // Second opportunity gets first two locations
            $createdOpportunities[1]->locations()->attach([
                $createdLocations[0]->id => [
                    'building_name' => 'Community Center',
                    'floor_number' => '2',
                    'apartment_number' => '201',
                    'landmark' => 'Near Library',
                ],
                $createdLocations[1]->id => [
                    'building_name' => 'Garden Office',
                    'floor_number' => '1',
                    'apartment_number' => '101',
                    'landmark' => 'Main Entrance',
                ],
            ]);

            // Third opportunity gets only the third location
            $createdOpportunities[2]->locations()->attach([
                $createdLocations[2]->id => [
                    'building_name' => 'Education Building',
                    'floor_number' => '4',
                    'apartment_number' => '402',
                    'landmark' => 'Near School',
                ],
            ]);
        }

        Model::reguard();
    }
}
