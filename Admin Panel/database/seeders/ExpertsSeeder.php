<?php

namespace Database\Seeders;

use App\Models\Expert;
use App\Models\ExpertProfile;
use App\Models\ExpertSpecialization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * ExpertsSeeder
 *
 * Seeds demo expert & agency users into the shared `users` table,
 * with corresponding `experts`, `expert_profiles`, and
 * `expert_specializations` records.
 *
 * Login credentials:
 *   Expert users  → password: Expert@1234
 *   Agency users  → password: Agency@1234
 */
class ExpertsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ── Demo Experts ──────────────────────────────────────────────────────
        $expertsData = [
            [
                'name'          => 'Dr. Arif Ullah',
                'email'         => 'arif.expert@plantixai.com',
                'phone'         => '+92-321-9990001',
                'specialty'     => 'Crop Diseases & Pathology',
                'bio'           => 'Senior plant pathologist with 15 years of field experience across Punjab.',
                'hourly_rate'   => 1500.00,
                'account_type'  => 'individual',
                'agency_name'   => null,
                'specialization'=> 'Plant Pathology',
                'experience'    => 15,
                'city'          => 'Lahore',
                'certifications'=> 'PhD Plant Pathology (UAF), CABI Certified Consultant',
                'specs'         => [
                    ['name' => 'Crop Diseases',  'level' => 'expert'],
                    ['name' => 'Soil Science',   'level' => 'intermediate'],
                ],
            ],
            [
                'name'          => 'Dr. Sadia Noor',
                'email'         => 'sadia.expert@plantixai.com',
                'phone'         => '+92-321-9990002',
                'specialty'     => 'Soil Science & Fertility',
                'bio'           => 'Soil fertility specialist, former NARC researcher.',
                'hourly_rate'   => 1200.00,
                'account_type'  => 'individual',
                'agency_name'   => null,
                'specialization'=> 'Soil Fertility',
                'experience'    => 12,
                'city'          => 'Islamabad',
                'certifications'=> 'MSc Soil Science (PARC), Certified Agronomist',
                'specs'         => [
                    ['name' => 'Soil Fertility',     'level' => 'expert'],
                    ['name' => 'Fertilizer Planning','level' => 'expert'],
                    ['name' => 'Crop Nutrition',     'level' => 'intermediate'],
                ],
            ],
            [
                'name'          => 'Engr. Tariq Bashir',
                'email'         => 'tariq.expert@plantixai.com',
                'phone'         => '+92-321-9990003',
                'specialty'     => 'Agricultural Water Management',
                'bio'           => 'Irrigation engineer specializing in drip and sprinkler systems.',
                'hourly_rate'   => 1000.00,
                'account_type'  => 'individual',
                'agency_name'   => null,
                'specialization'=> 'Irrigation Engineering',
                'experience'    => 10,
                'city'          => 'Multan',
                'certifications'=> 'BE Agricultural Engineering, WUA Certified Trainer',
                'specs'         => [
                    ['name' => 'Drip Irrigation',   'level' => 'expert'],
                    ['name' => 'Water Conservation', 'level' => 'expert'],
                ],
            ],
            [
                'name'          => 'Bilal Agro Consultants',
                'email'         => 'bilal.agency@plantixai.com',
                'phone'         => '+92-322-8880001',
                'specialty'     => 'Full-Spectrum Agricultural Consulting',
                'bio'           => 'Leading agri-consultancy firm serving 500+ farms across Pakistan.',
                'hourly_rate'   => 2500.00,
                'account_type'  => 'agency',
                'agency_name'   => 'Bilal Agro Consultants Pvt. Ltd.',
                'specialization'=> 'General Farm Management',
                'experience'    => 20,
                'city'          => 'Faisalabad',
                'certifications'=> 'ISO 9001:2015 Certified, PSQCA Registered',
                'specs'         => [
                    ['name' => 'Farm Management',   'level' => 'expert'],
                    ['name' => 'Pest Control',      'level' => 'expert'],
                    ['name' => 'Crop Planning',     'level' => 'expert'],
                    ['name' => 'Soil Science',      'level' => 'intermediate'],
                ],
            ],
            [
                'name'          => 'Dr. Zara Khan',
                'email'         => 'zara.expert@plantixai.com',
                'phone'         => '+92-321-9990004',
                'specialty'     => 'Integrated Pest Management',
                'bio'           => 'IPM specialist with extensive research in biological control.',
                'hourly_rate'   => 1300.00,
                'account_type'  => 'individual',
                'agency_name'   => null,
                'specialization'=> 'Pest Management',
                'experience'    => 8,
                'city'          => 'Peshawar',
                'certifications'=> 'MSc Entomology (AUP), FAO-IPM Certified',
                'specs'         => [
                    ['name' => 'Integrated Pest Management', 'level' => 'expert'],
                    ['name' => 'Biological Control',         'level' => 'expert'],
                ],
            ],
        ];

        foreach ($expertsData as $idx => $data) {
            $role     = $data['account_type'] === 'agency' ? 'agency_expert' : 'expert';
            $password = $data['account_type'] === 'agency' ? 'Agency@1234' : 'Expert@1234';

            // 1) Create / find user
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'                 => $data['name'],
                    'password'             => Hash::make($password),
                    'phone'                => $data['phone'],
                    'role'                 => $role,
                    'active'               => true,
                    'is_document_verified' => true,
                    'wallet_amount'        => 0,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ]
            );

            // 2) Create expert record
            $expert = Expert::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialty'    => $data['specialty'],
                    'bio'          => $data['bio'],
                    'is_available' => true,
                    'hourly_rate'  => $data['hourly_rate'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]
            );

            // 3) Create extended profile (approved so demo can log in)
            ExpertProfile::firstOrCreate(
                ['expert_id' => $expert->id],
                [
                    'agency_name'           => $data['agency_name'],
                    'specialization'        => $data['specialization'],
                    'experience_years'      => $data['experience'],
                    'certifications'        => $data['certifications'],
                    'availability_schedule' => json_encode([
                        'monday'    => ['09:00', '17:00'],
                        'tuesday'   => ['09:00', '17:00'],
                        'wednesday' => ['09:00', '17:00'],
                        'thursday'  => ['09:00', '17:00'],
                        'friday'    => ['09:00', '13:00'],
                    ]),
                    'city'            => $data['city'],
                    'country'         => 'Pakistan',
                    'account_type'    => $data['account_type'],
                    'approval_status' => 'approved',       // pre-approved for demo
                    'approved_at'     => $now,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]
            );

            // 4) Seed specializations
            foreach ($data['specs'] as $spec) {
                ExpertSpecialization::firstOrCreate(
                    ['expert_id' => $expert->id, 'name' => $spec['name']],
                    ['level' => $spec['level'], 'created_at' => $now, 'updated_at' => $now]
                );
            }
        }

        $this->command->info(
            'ExpertsSeeder: ' . Expert::count() . ' experts seeded successfully.'
        );
        $this->command->table(
            ['Name', 'Email', 'Password', 'Type'],
            collect($expertsData)->map(fn ($e) => [
                $e['name'],
                $e['email'],
                $e['account_type'] === 'agency' ? 'Agency@1234' : 'Expert@1234',
                $e['account_type'],
            ])->toArray()
        );
    }
}
