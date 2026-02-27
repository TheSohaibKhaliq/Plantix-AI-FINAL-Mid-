<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserLocationSeeder extends Seeder
{
    public function run(): void
    {
        $farmers = DB::table('users')->where('role', 'user')->get();

        if ($farmers->isEmpty()) {
            $this->command->warn('No farmer users found.');
            return;
        }

        // Realistic Pakistani agricultural districts with coordinates
        $locations = [
            ['city' => 'Lahore',        'region' => 'Punjab',         'country' => 'Pakistan', 'lat' => 31.5497, 'lng' => 74.3436],
            ['city' => 'Faisalabad',    'region' => 'Punjab',         'country' => 'Pakistan', 'lat' => 31.4180, 'lng' => 73.0790],
            ['city' => 'Multan',        'region' => 'Punjab',         'country' => 'Pakistan', 'lat' => 30.1978, 'lng' => 71.4711],
            ['city' => 'Gujranwala',    'region' => 'Punjab',         'country' => 'Pakistan', 'lat' => 32.1617, 'lng' => 74.1883],
            ['city' => 'Bahawalpur',    'region' => 'Punjab',         'country' => 'Pakistan', 'lat' => 29.3956, 'lng' => 71.6836],
            ['city' => 'Sahiwal',       'region' => 'Punjab',         'country' => 'Pakistan', 'lat' => 30.6706, 'lng' => 73.1064],
            ['city' => 'Okara',         'region' => 'Punjab',         'country' => 'Pakistan', 'lat' => 30.8138, 'lng' => 73.4454],
            ['city' => 'Kasur',         'region' => 'Punjab',         'country' => 'Pakistan', 'lat' => 31.1167, 'lng' => 74.4500],
            ['city' => 'Rahim Yar Khan','region' => 'Punjab',         'country' => 'Pakistan', 'lat' => 28.4200, 'lng' => 70.2989],
            ['city' => 'Sheikhupura',   'region' => 'Punjab',         'country' => 'Pakistan', 'lat' => 31.7131, 'lng' => 73.9850],
            ['city' => 'Sukkur',        'region' => 'Sindh',          'country' => 'Pakistan', 'lat' => 27.7052, 'lng' => 68.8574],
            ['city' => 'Hyderabad',     'region' => 'Sindh',          'country' => 'Pakistan', 'lat' => 25.3960, 'lng' => 68.3578],
            ['city' => 'Peshawar',      'region' => 'KPK',            'country' => 'Pakistan', 'lat' => 34.0150, 'lng' => 71.5249],
            ['city' => 'Mardan',        'region' => 'KPK',            'country' => 'Pakistan', 'lat' => 34.1987, 'lng' => 72.0404],
            ['city' => 'Quetta',        'region' => 'Balochistan',    'country' => 'Pakistan', 'lat' => 30.1798, 'lng' => 66.9750],
        ];

        $rows = [];
        $now  = Carbon::now();

        foreach ($farmers as $i => $farmer) {
            // Assign primary location
            $primary = $locations[$i % count($locations)];
            $rows[] = [
                'user_id'    => $farmer->id,
                'label'      => 'Home Farm',
                'city'       => $primary['city'],
                'region'     => $primary['region'],
                'country'    => $primary['country'],
                'latitude'   => $primary['lat'] + (rand(-100, 100) / 10000),
                'longitude'  => $primary['lng'] + (rand(-100, 100) / 10000),
                'is_primary' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // 30% chance of a secondary location (village field)
            if (rand(1, 10) <= 3) {
                $secondary = $locations[($i + 3) % count($locations)];
                $rows[] = [
                    'user_id'    => $farmer->id,
                    'label'      => 'Field Plot',
                    'city'       => $secondary['city'],
                    'region'     => $secondary['region'],
                    'country'    => $secondary['country'],
                    'latitude'   => $secondary['lat'] + (rand(-200, 200) / 10000),
                    'longitude'  => $secondary['lng'] + (rand(-200, 200) / 10000),
                    'is_primary' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('user_locations')->insert($rows);
        $this->command->info('UserLocationSeeder: ' . count($rows) . ' locations seeded for ' . $farmers->count() . ' farmers.');
    }
}
