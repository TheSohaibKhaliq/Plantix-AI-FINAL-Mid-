<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ExpertSeeder extends Seeder
{
    public function run(): void
    {
        // Create expert user accounts (role = dedicated 'expert' users)
        // and link them to the experts table
        $experts = [
            [
                'user' => [
                    'name'              => 'Dr. Ahmad Raza',
                    'email'             => 'ahmad.raza@plantix.ai',
                    'password'          => Hash::make('expert@123'),
                    'role'              => 'user',
                    'phone'             => '+92-300-1234567',
                    'is_active'         => 1,
                ],
                'expert' => [
                    'name'              => 'Dr. Ahmad Raza',
                    'specialization'    => 'Soil Science & Crop Nutrition',
                    'bio'               => 'PhD in Agronomy with 15 years of field experience.',
                    'experience_years'  => 15,
                    'fee'               => 1500.00,
                    'is_available'      => true,
                ],
            ],
            [
                'user' => [
                    'name'              => 'Dr. Fatima Malik',
                    'email'             => 'fatima.malik@plantix.ai',
                    'password'          => Hash::make('expert@123'),
                    'role'              => 'user',
                    'phone'             => '+92-321-9876543',
                    'is_active'         => 1,
                ],
                'expert' => [
                    'name'              => 'Dr. Fatima Malik',
                    'specialization'    => 'Plant Pathology & Disease Management',
                    'bio'               => 'Specialist in fungal and bacterial crop diseases.',
                    'experience_years'  => 10,
                    'fee'               => 2000.00,
                    'is_available'      => true,
                ],
            ],
            [
                'user' => [
                    'name'              => 'Prof. Tariq Hussain',
                    'email'             => 'tariq.hussain@plantix.ai',
                    'password'          => Hash::make('expert@123'),
                    'role'              => 'user',
                    'phone'             => '+92-333-5678901',
                    'is_active'         => 1,
                ],
                'expert' => [
                    'name'              => 'Prof. Tariq Hussain',
                    'specialization'    => 'Irrigation & Water Management',
                    'bio'               => 'Professor at UAF with expertise in drip and sprinkler irrigation.',
                    'experience_years'  => 20,
                    'fee'               => 2500.00,
                    'is_available'      => true,
                ],
            ],
        ];

        foreach ($experts as $data) {
            $userId = DB::table('users')->insertGetId(array_merge($data['user'], [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            DB::table('experts')->updateOrInsert(
                ['email' => $data['user']['email']],
                array_merge($data['expert'], [
                    'user_id'    => $userId,
                    'email'      => $data['user']['email'],
                    'phone'      => $data['user']['phone'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Experts seeded: ' . count($experts));
    }
}
