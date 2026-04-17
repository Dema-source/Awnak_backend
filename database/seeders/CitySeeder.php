<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // United States
            ['country_id' => 1, 'name' => 'New York', 'state' => 'New York', 'latitude' => 40.7128, 'longitude' => -74.0060, 'postal_code' => '10001'],
            ['country_id' => 1, 'name' => 'Los Angeles', 'state' => 'California', 'latitude' => 34.0522, 'longitude' => -118.2437, 'postal_code' => '90001'],
            ['country_id' => 1, 'name' => 'Chicago', 'state' => 'Illinois', 'latitude' => 41.8781, 'longitude' => -87.6298, 'postal_code' => '60601'],
            ['country_id' => 1, 'name' => 'Houston', 'state' => 'Texas', 'latitude' => 29.7604, 'longitude' => -95.3698, 'postal_code' => '77001'],
            ['country_id' => 1, 'name' => 'Phoenix', 'state' => 'Arizona', 'latitude' => 33.4484, 'longitude' => -112.0740, 'postal_code' => '85001'],
            ['country_id' => 1, 'name' => 'Philadelphia', 'state' => 'Pennsylvania', 'latitude' => 39.9526, 'longitude' => -75.1652, 'postal_code' => '19101'],
            ['country_id' => 1, 'name' => 'San Antonio', 'state' => 'Texas', 'latitude' => 29.4241, 'longitude' => -98.4936, 'postal_code' => '78201'],
            ['country_id' => 1, 'name' => 'San Diego', 'state' => 'California', 'latitude' => 32.7157, 'longitude' => -117.1611, 'postal_code' => '92101'],
            ['country_id' => 1, 'name' => 'Dallas', 'state' => 'Texas', 'latitude' => 32.7767, 'longitude' => -96.7970, 'postal_code' => '75201'],
            ['country_id' => 1, 'name' => 'San Jose', 'state' => 'California', 'latitude' => 37.3382, 'longitude' => -121.8863, 'postal_code' => '95101'],

            // United Kingdom
            ['country_id' => 2, 'name' => 'London', 'state' => 'England', 'latitude' => 51.5074, 'longitude' => -0.1278, 'postal_code' => 'SW1A 0AA'],
            ['country_id' => 2, 'name' => 'Manchester', 'state' => 'England', 'latitude' => 53.4808, 'longitude' => -2.2426, 'postal_code' => 'M1 1AA'],
            ['country_id' => 2, 'name' => 'Birmingham', 'state' => 'England', 'latitude' => 52.4862, 'longitude' => -1.8904, 'postal_code' => 'B1 1AA'],
            ['country_id' => 2, 'name' => 'Glasgow', 'state' => 'Scotland', 'latitude' => 55.8642, 'longitude' => -4.2518, 'postal_code' => 'G1 1AA'],
            ['country_id' => 2, 'name' => 'Liverpool', 'state' => 'England', 'latitude' => 53.4084, 'longitude' => -2.9916, 'postal_code' => 'L1 1AA'],
            ['country_id' => 2, 'name' => 'Edinburgh', 'state' => 'Scotland', 'latitude' => 55.9533, 'longitude' => -3.1883, 'postal_code' => 'EH1 1AA'],
            ['country_id' => 2, 'name' => 'Bristol', 'state' => 'England', 'latitude' => 51.4545, 'longitude' => -2.5879, 'postal_code' => 'BS1 1AA'],
            ['country_id' => 2, 'name' => 'Leeds', 'state' => 'England', 'latitude' => 53.8008, 'longitude' => -1.5491, 'postal_code' => 'LS1 1AA'],
            ['country_id' => 2, 'name' => 'Sheffield', 'state' => 'England', 'latitude' => 53.3811, 'longitude' => -1.4709, 'postal_code' => 'S1 1AA'],
            ['country_id' => 2, 'name' => 'Cardiff', 'state' => 'Wales', 'latitude' => 51.4816, 'longitude' => -3.1791, 'postal_code' => 'CF10 1AA'],

            // Canada
            ['country_id' => 3, 'name' => 'Toronto', 'state' => 'Ontario', 'latitude' => 43.6532, 'longitude' => -79.3832, 'postal_code' => 'M5V 2T6'],
            ['country_id' => 3, 'name' => 'Montreal', 'state' => 'Quebec', 'latitude' => 45.5017, 'longitude' => -73.5673, 'postal_code' => 'H3A 0A2'],
            ['country_id' => 3, 'name' => 'Vancouver', 'state' => 'British Columbia', 'latitude' => 49.2827, 'longitude' => -123.1207, 'postal_code' => 'V6A 1A2'],
            ['country_id' => 3, 'name' => 'Calgary', 'state' => 'Alberta', 'latitude' => 51.0447, 'longitude' => -114.0719, 'postal_code' => 'T2P 1V4'],
            ['country_id' => 3, 'name' => 'Edmonton', 'state' => 'Alberta', 'latitude' => 53.5461, 'longitude' => -113.4938, 'postal_code' => 'T5J 0A2'],
            ['country_id' => 3, 'name' => 'Ottawa', 'state' => 'Ontario', 'latitude' => 45.4215, 'longitude' => -75.6972, 'postal_code' => 'K1A 0A6'],
            ['country_id' => 3, 'name' => 'Winnipeg', 'state' => 'Manitoba', 'latitude' => 49.8951, 'longitude' => -97.1384, 'postal_code' => 'R3B 0C4'],
            ['country_id' => 3, 'name' => 'Quebec City', 'state' => 'Quebec', 'latitude' => 46.8139, 'longitude' => -71.2080, 'postal_code' => 'G1R 2A9'],
            ['country_id' => 3, 'name' => 'Hamilton', 'state' => 'Ontario', 'latitude' => 43.2557, 'longitude' => -79.8711, 'postal_code' => 'L8P 1A2'],
            ['country_id' => 3, 'name' => 'Halifax', 'state' => 'Nova Scotia', 'latitude' => 44.6488, 'longitude' => -63.5752, 'postal_code' => 'B3H 2A5'],

            // Australia
            ['country_id' => 4, 'name' => 'Sydney', 'state' => 'New South Wales', 'latitude' => -33.8688, 'longitude' => 151.2093, 'postal_code' => '2000'],
            ['country_id' => 4, 'name' => 'Melbourne', 'state' => 'Victoria', 'latitude' => -37.8136, 'longitude' => 144.9631, 'postal_code' => '3000'],
            ['country_id' => 4, 'name' => 'Brisbane', 'state' => 'Queensland', 'latitude' => -27.4698, 'longitude' => 153.0251, 'postal_code' => '4000'],
            ['country_id' => 4, 'name' => 'Perth', 'state' => 'Western Australia', 'latitude' => -31.9505, 'longitude' => 115.8605, 'postal_code' => '6000'],
            ['country_id' => 4, 'name' => 'Adelaide', 'state' => 'South Australia', 'latitude' => -34.9285, 'longitude' => 138.6007, 'postal_code' => '5000'],
            ['country_id' => 4, 'name' => 'Gold Coast', 'state' => 'Queensland', 'latitude' => -28.0167, 'longitude' => 153.4000, 'postal_code' => '4217'],
            ['country_id' => 4, 'name' => 'Canberra', 'state' => 'Australian Capital Territory', 'latitude' => -35.2809, 'longitude' => 149.1300, 'postal_code' => '2600'],
            ['country_id' => 4, 'name' => 'Newcastle', 'state' => 'New South Wales', 'latitude' => -32.9267, 'longitude' => 151.7789, 'postal_code' => '2300'],
            ['country_id' => 4, 'name' => 'Wollongong', 'state' => 'New South Wales', 'latitude' => -34.4278, 'longitude' => 150.8931, 'postal_code' => '2500'],
            ['country_id' => 4, 'name' => 'Hobart', 'state' => 'Tasmania', 'latitude' => -42.8821, 'longitude' => 147.3243, 'postal_code' => '7000'],

            // Germany
            ['country_id' => 5, 'name' => 'Berlin', 'state' => 'Berlin', 'latitude' => 52.5200, 'longitude' => 13.4050, 'postal_code' => '10115'],
            ['country_id' => 5, 'name' => 'Hamburg', 'state' => 'Hamburg', 'latitude' => 53.5511, 'longitude' => 9.9937, 'postal_code' => '20095'],
            ['country_id' => 5, 'name' => 'Munich', 'state' => 'Bavaria', 'latitude' => 48.1351, 'longitude' => 11.5820, 'postal_code' => '80331'],
            ['country_id' => 5, 'name' => 'Cologne', 'state' => 'North Rhine-Westphalia', 'latitude' => 50.9375, 'longitude' => 6.9603, 'postal_code' => '50667'],
            ['country_id' => 5, 'name' => 'Frankfurt', 'state' => 'Hesse', 'latitude' => 50.1109, 'longitude' => 8.6821, 'postal_code' => '60311'],
            ['country_id' => 5, 'name' => 'Stuttgart', 'state' => 'Baden-Württemberg', 'latitude' => 48.7758, 'longitude' => 9.1829, 'postal_code' => '70173'],
            ['country_id' => 5, 'name' => 'Düsseldorf', 'state' => 'North Rhine-Westphalia', 'latitude' => 51.2277, 'longitude' => 6.7735, 'postal_code' => '40210'],
            ['country_id' => 5, 'name' => 'Dortmund', 'state' => 'North Rhine-Westphalia', 'latitude' => 51.5136, 'longitude' => 7.4653, 'postal_code' => '44137'],
            ['country_id' => 5, 'name' => 'Essen', 'state' => 'North Rhine-Westphalia', 'latitude' => 51.4556, 'longitude' => 7.0116, 'postal_code' => '45127'],
            ['country_id' => 5, 'name' => 'Leipzig', 'state' => 'Saxony', 'latitude' => 51.3397, 'longitude' => 12.3731, 'postal_code' => '04109'],

            // France
            ['country_id' => 6, 'name' => 'Paris', 'state' => 'Île-de-France', 'latitude' => 48.8566, 'longitude' => 2.3522, 'postal_code' => '75001'],
            ['country_id' => 6, 'name' => 'Marseille', 'state' => 'Provence-Alpes-Côte d\'Azur', 'latitude' => 43.2965, 'longitude' => 5.3698, 'postal_code' => '13001'],
            ['country_id' => 6, 'name' => 'Lyon', 'state' => 'Auvergne-Rhône-Alpes', 'latitude' => 45.7640, 'longitude' => 4.8357, 'postal_code' => '69001'],
            ['country_id' => 6, 'name' => 'Toulouse', 'state' => 'Occitanie', 'latitude' => 43.6047, 'longitude' => 1.4442, 'postal_code' => '31000'],
            ['country_id' => 6, 'name' => 'Nice', 'state' => 'Provence-Alpes-Côte d\'Azur', 'latitude' => 43.7102, 'longitude' => 7.2620, 'postal_code' => '06000'],
            ['country_id' => 6, 'name' => 'Nantes', 'state' => 'Pays de la Loire', 'latitude' => 47.2184, 'longitude' => -1.5536, 'postal_code' => '44000'],
            ['country_id' => 6, 'name' => 'Strasbourg', 'state' => 'Grand Est', 'latitude' => 48.5846, 'longitude' => 7.7507, 'postal_code' => '67000'],
            ['country_id' => 6, 'name' => 'Montpellier', 'state' => 'Occitanie', 'latitude' => 43.6108, 'longitude' => 3.8767, 'postal_code' => '34000'],
            ['country_id' => 6, 'name' => 'Bordeaux', 'state' => 'Nouvelle-Aquitaine', 'latitude' => 44.8378, 'longitude' => -0.5792, 'postal_code' => '33000'],
            ['country_id' => 6, 'name' => 'Lille', 'state' => 'Hauts-de-France', 'latitude' => 50.6292, 'longitude' => 3.0573, 'postal_code' => '59000'],

            // Japan
            ['country_id' => 7, 'name' => 'Tokyo', 'state' => 'Tokyo', 'latitude' => 35.6762, 'longitude' => 139.6503, 'postal_code' => '100-0001'],
            ['country_id' => 7, 'name' => 'Osaka', 'state' => 'Osaka', 'latitude' => 34.6937, 'longitude' => 135.5023, 'postal_code' => '530-0001'],
            ['country_id' => 7, 'name' => 'Kyoto', 'state' => 'Kyoto', 'latitude' => 35.0116, 'longitude' => 135.7681, 'postal_code' => '604-0001'],
            ['country_id' => 7, 'name' => 'Yokohama', 'state' => 'Kanagawa', 'latitude' => 35.4437, 'longitude' => 139.6380, 'postal_code' => '220-0001'],
            ['country_id' => 7, 'name' => 'Nagoya', 'state' => 'Aichi', 'latitude' => 35.1815, 'longitude' => 136.9066, 'postal_code' => '450-0001'],
            ['country_id' => 7, 'name' => 'Sapporo', 'state' => 'Hokkaido', 'latitude' => 43.0642, 'longitude' => 141.3469, 'postal_code' => '060-0001'],
            ['country_id' => 7, 'name' => 'Fukuoka', 'state' => 'Fukuoka', 'latitude' => 33.5904, 'longitude' => 130.4017, 'postal_code' => '810-0001'],
            ['country_id' => 7, 'name' => 'Kobe', 'state' => 'Hyogo', 'latitude' => 34.6901, 'longitude' => 135.1830, 'postal_code' => '650-0001'],
            ['country_id' => 7, 'name' => 'Kawasaki', 'state' => 'Kanagawa', 'latitude' => 35.5311, 'longitude' => 139.5395, 'postal_code' => '210-0001'],
            ['country_id' => 7, 'name' => 'Saitama', 'state' => 'Saitama', 'latitude' => 35.8617, 'longitude' => 139.6455, 'postal_code' => '330-0001'],

            // China
            ['country_id' => 8, 'name' => 'Beijing', 'state' => 'Beijing', 'latitude' => 39.9042, 'longitude' => 116.4074, 'postal_code' => '100000'],
            ['country_id' => 8, 'name' => 'Shanghai', 'state' => 'Shanghai', 'latitude' => 31.2304, 'longitude' => 121.4737, 'postal_code' => '200000'],
            ['country_id' => 8, 'name' => 'Guangzhou', 'state' => 'Guangdong', 'latitude' => 23.1291, 'longitude' => 113.2644, 'postal_code' => '510000'],
            ['country_id' => 8, 'name' => 'Shenzhen', 'state' => 'Guangdong', 'latitude' => 22.5431, 'longitude' => 114.0579, 'postal_code' => '518000'],
            ['country_id' => 8, 'name' => 'Chongqing', 'state' => 'Chongqing', 'latitude' => 29.5630, 'longitude' => 106.5516, 'postal_code' => '400000'],
            ['country_id' => 8, 'name' => 'Tianjin', 'state' => 'Tianjin', 'latitude' => 39.0842, 'longitude' => 117.2010, 'postal_code' => '300000'],
            ['country_id' => 8, 'name' => 'Wuhan', 'state' => 'Hubei', 'latitude' => 30.5928, 'longitude' => 114.3055, 'postal_code' => '430000'],
            ['country_id' => 8, 'name' => 'Dongguan', 'state' => 'Guangdong', 'latitude' => 23.0489, 'longitude' => 113.7447, 'postal_code' => '523000'],
            ['country_id' => 8, 'name' => 'Chengdu', 'state' => 'Sichuan', 'latitude' => 30.5728, 'longitude' => 104.0668, 'postal_code' => '610000'],
            ['country_id' => 8, 'name' => 'Hangzhou', 'state' => 'Zhejiang', 'latitude' => 30.2741, 'longitude' => 120.1551, 'postal_code' => '310000'],

            // India
            ['country_id' => 9, 'name' => 'New Delhi', 'state' => 'Delhi', 'latitude' => 28.6139, 'longitude' => 77.2090, 'postal_code' => '110001'],
            ['country_id' => 9, 'name' => 'Mumbai', 'state' => 'Maharashtra', 'latitude' => 19.0760, 'longitude' => 72.8777, 'postal_code' => '400001'],
            ['country_id' => 9, 'name' => 'Bangalore', 'state' => 'Karnataka', 'latitude' => 12.9716, 'longitude' => 77.5946, 'postal_code' => '560001'],
            ['country_id' => 9, 'name' => 'Chennai', 'state' => 'Tamil Nadu', 'latitude' => 13.0827, 'longitude' => 80.2707, 'postal_code' => '600001'],
            ['country_id' => 9, 'name' => 'Kolkata', 'state' => 'West Bengal', 'latitude' => 22.5726, 'longitude' => 88.3639, 'postal_code' => '700001'],
            ['country_id' => 9, 'name' => 'Hyderabad', 'state' => 'Telangana', 'latitude' => 17.3850, 'longitude' => 78.4867, 'postal_code' => '500001'],
            ['country_id' => 9, 'name' => 'Pune', 'state' => 'Maharashtra', 'latitude' => 18.5204, 'longitude' => 73.8567, 'postal_code' => '411001'],
            ['country_id' => 9, 'name' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0225, 'longitude' => 72.5714, 'postal_code' => '380001'],
            ['country_id' => 9, 'name' => 'Jaipur', 'state' => 'Rajasthan', 'latitude' => 26.9124, 'longitude' => 75.7873, 'postal_code' => '302001'],
            ['country_id' => 9, 'name' => 'Surat', 'state' => 'Gujarat', 'latitude' => 21.1702, 'longitude' => 72.8311, 'postal_code' => '395001'],

            // Brazil
            ['country_id' => 10, 'name' => 'São Paulo', 'state' => 'São Paulo', 'latitude' => -23.5505, 'longitude' => -46.6333, 'postal_code' => '01000-000'],
            ['country_id' => 10, 'name' => 'Rio de Janeiro', 'state' => 'Rio de Janeiro', 'latitude' => -22.9068, 'longitude' => -43.1729, 'postal_code' => '20000-000'],
            ['country_id' => 10, 'name' => 'Brasília', 'state' => 'Federal District', 'latitude' => -15.8267, 'longitude' => -47.9218, 'postal_code' => '70000-000'],
            ['country_id' => 10, 'name' => 'Salvador', 'state' => 'Bahia', 'latitude' => -12.9714, 'longitude' => -38.5014, 'postal_code' => '40000-000'],
            ['country_id' => 10, 'name' => 'Fortaleza', 'state' => 'Ceará', 'latitude' => -3.7319, 'longitude' => -38.5267, 'postal_code' => '60000-000'],
            ['country_id' => 10, 'name' => 'Belo Horizonte', 'state' => 'Minas Gerais', 'latitude' => -19.9167, 'longitude' => -43.9345, 'postal_code' => '30000-000'],
            ['country_id' => 10, 'name' => 'Manaus', 'state' => 'Amazonas', 'latitude' => -3.1190, 'longitude' => -60.0217, 'postal_code' => '69000-000'],
            ['country_id' => 10, 'name' => 'Curitiba', 'state' => 'Paraná', 'latitude' => -25.4284, 'longitude' => -49.2733, 'postal_code' => '80000-000'],
            ['country_id' => 10, 'name' => 'Recife', 'state' => 'Pernambuco', 'latitude' => -8.0476, 'longitude' => -34.8770, 'postal_code' => '50000-000'],
            ['country_id' => 10, 'name' => 'Porto Alegre', 'state' => 'Rio Grande do Sul', 'latitude' => -30.0346, 'longitude' => -51.2177, 'postal_code' => '90000-000'],
        ];

        // Add is_active flag to all cities
        foreach ($cities as &$city) {
            $city['is_active'] = true;
        }

        DB::table('cities')->insert($cities);
    }
}
