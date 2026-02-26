<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReturnReasonSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            ['reason' => 'Wrong product delivered',          'is_active' => true],
            ['reason' => 'Damaged / defective product',      'is_active' => true],
            ['reason' => 'Product not as described',         'is_active' => true],
            ['reason' => 'Missing items in the order',       'is_active' => true],
            ['reason' => 'Expired product',                  'is_active' => true],
            ['reason' => 'Changed my mind',                  'is_active' => true],
            ['reason' => 'Received duplicate order',         'is_active' => true],
            ['reason' => 'Poor quality',                     'is_active' => true],
            ['reason' => 'Other (please specify in notes)',  'is_active' => true],
        ];

        foreach ($reasons as $reason) {
            DB::table('return_reasons')->updateOrInsert(
                ['reason' => $reason['reason']],
                array_merge($reason, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Return reasons seeded: ' . count($reasons));
    }
}
