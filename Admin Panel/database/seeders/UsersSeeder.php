<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ── Admin users ──────────────────────────────────────────────
        DB::table('users')->insert([
            [
                'name'        => 'Super Admin',
                'email'       => 'admin@plantixai.com',
                'password'    => Hash::make('Admin@1234'),
                'phone'       => '+92-300-0000001',
                'role'        => 'admin',
                'active'      => true,
                'is_document_verified' => true,
                'wallet_amount' => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Khalid Raheem',
                'email'       => 'khalid.admin@plantixai.com',
                'password'    => Hash::make('Admin@1234'),
                'phone'       => '+92-300-0000002',
                'role'        => 'admin',
                'active'      => true,
                'is_document_verified' => true,
                'wallet_amount' => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);

        // ── Vendor users ─────────────────────────────────────────────
        $vendors = [
            ['Arshad Ali',      'arshad.vendor@plantixai.com',  '+92-301-1112221'],
            ['Naseem Akhtar',   'naseem.vendor@plantixai.com',  '+92-301-1112222'],
            ['Tariq Mehmood',   'tariq.vendor@plantixai.com',   '+92-301-1112223'],
            ['Bilal Hussain',   'bilal.vendor@plantixai.com',   '+92-301-1112224'],
            ['Shahid Iqbal',    'shahid.vendor@plantixai.com',  '+92-301-1112225'],
            ['Ghulam Mustafa',  'ghulam.vendor@plantixai.com',  '+92-301-1112226'],
            ['Imran Siddiqui',  'imran.vendor@plantixai.com',   '+92-301-1112227'],
            ['Riaz Ahmed',      'riaz.vendor@plantixai.com',    '+92-301-1112228'],
            ['Zahid Chaudhry',  'zahid.vendor@plantixai.com',   '+92-301-1112229'],
            ['Pervez Anwar',    'pervez.vendor@plantixai.com',  '+92-301-1112230'],
        ];

        foreach ($vendors as $v) {
            DB::table('users')->insert([
                'name'        => $v[0],
                'email'       => $v[1],
                'password'    => Hash::make('Vendor@1234'),
                'phone'       => $v[2],
                'role'        => 'vendor',
                'active'      => true,
                'is_document_verified' => true,
                'wallet_amount' => rand(500, 15000),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // ── Driver users ─────────────────────────────────────────────
        $drivers = [
            ['Kamran Baig',     'kamran.driver@plantixai.com',  '+92-303-2221111'],
            ['Salman Farooq',   'salman.driver@plantixai.com',  '+92-303-2221112'],
            ['Faisal Nawaz',    'faisal.driver@plantixai.com',  '+92-303-2221113'],
            ['Usman Raza',      'usman.driver@plantixai.com',   '+92-303-2221114'],
            ['Hassan Zafar',    'hassan.driver@plantixai.com',  '+92-303-2221115'],
        ];

        foreach ($drivers as $d) {
            DB::table('users')->insert([
                'name'        => $d[0],
                'email'       => $d[1],
                'password'    => Hash::make('Driver@1234'),
                'phone'       => $d[2],
                'role'        => 'driver',
                'active'      => true,
                'is_document_verified' => true,
                'wallet_amount' => rand(200, 3000),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // ── Customer users ────────────────────────────────────────────
        $customers = [
            ['Muhammad Aslam',  'aslam@gmail.com',      '+92-333-5551001', 1200.00],
            ['Nadia Bibi',      'nadia@gmail.com',       '+92-333-5551002', 800.00],
            ['Qasim Raza',      'qasim@gmail.com',       '+92-333-5551003', 2500.00],
            ['Shazia Khatoon',  'shazia@gmail.com',      '+92-333-5551004', 500.00],
            ['Ejaz Haider',     'ejaz@gmail.com',        '+92-333-5551005', 3000.00],
            ['Rukhsana Bibi',   'rukhsana@gmail.com',    '+92-333-5551006', 1500.00],
            ['Jameel Ahmed',    'jameel@gmail.com',      '+92-333-5551007', 700.00],
            ['Saima Nawaz',     'saima@gmail.com',       '+92-333-5551008', 4200.00],
            ['Asif Javed',      'asif@gmail.com',        '+92-333-5551009', 950.00],
            ['Parveen Begum',   'parveen@gmail.com',     '+92-333-5551010', 2100.00],
            ['Mushtaq Hussain', 'mushtaq@gmail.com',     '+92-333-5551011', 600.00],
            ['Zainab Fatima',   'zainab@gmail.com',      '+92-333-5551012', 1800.00],
            ['Wasim Akram',     'wasim@gmail.com',       '+92-333-5551013', 3300.00],
            ['Hina Akhtar',     'hina@gmail.com',        '+92-333-5551014', 450.00],
            ['Naveed Sultan',   'naveed@gmail.com',      '+92-333-5551015', 2750.00],
        ];

        foreach ($customers as $c) {
            DB::table('users')->insert([
                'name'          => $c[0],
                'email'         => $c[1],
                'password'      => Hash::make('User@1234'),
                'phone'         => $c[2],
                'role'          => 'user',
                'active'        => true,
                'is_document_verified' => false,
                'wallet_amount' => $c[3],
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        $this->command->info('UsersSeeder: ' . DB::table('users')->count() . ' users inserted.');
    }
}
