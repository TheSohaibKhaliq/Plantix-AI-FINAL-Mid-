<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FertilizerRecommendationSeeder extends Seeder
{
    public function run(): void
    {
        $soilTests = DB::table('soil_tests')->get();

        if ($soilTests->isEmpty()) {
            $this->command->warn('No soil tests found. Run FarmProfileSeeder first.');
            return;
        }

        $now = Carbon::now();

        // Crop-to-fertilizer plans — realistic Pakistani fertilizer programs
        $fertilizerPlans = [
            'Wheat' => [
                'growth_stages' => ['Sowing', 'Tillering', 'Jointing', 'Booting'],
                'basal_plan' => [
                    ['product' => 'DAP (Di-Ammonium Phosphate)',  'quantity_kg_acre' => 50,  'timing' => 'At sowing',          'method' => 'Broadcast + incorporate'],
                    ['product' => 'Urea (46% N)',                  'quantity_kg_acre' => 25,  'timing' => '3rd week (tillering)', 'method' => 'Topdress + irrigate'],
                    ['product' => 'Urea (46% N)',                  'quantity_kg_acre' => 25,  'timing' => '6th week (jointing)', 'method' => 'Topdress'],
                    ['product' => 'Zinc Sulfate 33%',              'quantity_kg_acre' => 2.5, 'timing' => 'Foliar at booting',   'method' => '500g in 100L water'],
                ],
                'application_instructions' => 'Apply DAP at sowing for P+N base. Split urea in two doses (tillering + jointing) for efficient N use. Zinc sulfate foliar spray at booting prevents lodging and improves grain weight.',
            ],
            'Cotton' => [
                'growth_stages' => ['Seedling', 'Squaring', 'Flowering', 'Boll Setting'],
                'basal_plan' => [
                    ['product' => 'DAP',                           'quantity_kg_acre' => 25,  'timing' => 'At sowing',            'method' => 'Band placement in furrow'],
                    ['product' => 'Urea',                          'quantity_kg_acre' => 30,  'timing' => 'Squaring (45 DAE)',     'method' => 'Side dress + irrigate'],
                    ['product' => 'SOP (Sulfate of Potash)',       'quantity_kg_acre' => 25,  'timing' => 'Boll formation stage', 'method' => 'Topdress before irrigation'],
                    ['product' => 'Boron (Borax)',                 'quantity_kg_acre' => 1,   'timing' => 'Flower initiation',    'method' => 'Foliar 1g/L water'],
                ],
                'application_instructions' => 'Reduce N during vegetative stage to prevent excess growth. Increase K and B at reproductive stage for better boll retention and fiber quality.',
            ],
            'Rice' => [
                'growth_stages' => ['Transplanting', 'Tillering', 'Panicle Initiation', 'Heading'],
                'basal_plan' => [
                    ['product' => 'DAP',                           'quantity_kg_acre' => 30,  'timing' => '1 week after transplant', 'method' => 'Broadcast in standing water'],
                    ['product' => 'Urea',                          'quantity_kg_acre' => 20,  'timing' => 'Tillering (3 WAT)',    'method' => 'Broadcast'],
                    ['product' => 'Urea',                          'quantity_kg_acre' => 20,  'timing' => 'Panicle initiation',   'method' => 'Topdress'],
                    ['product' => 'Zinc Sulfate',                  'quantity_kg_acre' => 5,   'timing' => 'At transplant',        'method' => 'Soil application or seedling dip'],
                ],
                'application_instructions' => 'Zinc deficiency is common in rice in Punjab — always apply Zn. Drain field before urea application to avoid N loss via denitrification.',
            ],
            'Sugarcane' => [
                'growth_stages' => ['Germination', 'Tillering', 'Grand Growth', 'Maturation'],
                'basal_plan' => [
                    ['product' => 'DAP',                           'quantity_kg_acre' => 50,  'timing' => 'At planting',          'method' => 'In-furrow band'],
                    ['product' => 'Ammonium Nitrate or Urea',      'quantity_kg_acre' => 40,  'timing' => 'Tillering (60 DAP)',   'method' => 'Broadcast + irrigate'],
                    ['product' => 'SOP',                           'quantity_kg_acre' => 30,  'timing' => 'Grand growth phase',   'method' => 'Topdress'],
                    ['product' => 'Calcium Ammonium Nitrate (CAN)','quantity_kg_acre' => 25,  'timing' => '4 months after plant', 'method' => 'Split application'],
                ],
                'application_instructions' => 'Sugarcane is a heavy feeder. Apply FYM 4 ton/acre before planting. Potassium critical for sucrose accumulation — do not skip SOP at grand growth.',
            ],
            'Maize' => [
                'growth_stages' => ['Sowing', 'V6 (6-leaf)', 'Tassel', 'Grain Fill'],
                'basal_plan' => [
                    ['product' => 'DAP',                           'quantity_kg_acre' => 35,  'timing' => 'At sowing',            'method' => 'Band placement'],
                    ['product' => 'Urea',                          'quantity_kg_acre' => 40,  'timing' => '6-leaf stage',         'method' => 'Side-dress in furrow'],
                    ['product' => 'Urea',                          'quantity_kg_acre' => 25,  'timing' => 'Tasseling stage',      'method' => 'Topdress before irrigation'],
                    ['product' => 'Potassium Chloride (MOP)',      'quantity_kg_acre' => 15,  'timing' => 'Grain fill start',     'method' => 'Broadcast'],
                ],
                'application_instructions' => 'Maize requires high N. Split urea in 2 doses. First irrigation critical within 3 days of sowing. Deficiency of Zn common — apply 2.5kg Zinc Sulfate if soil Zn < 0.5 mg/kg.',
            ],
        ];

        $cropTypes   = array_keys($fertilizerPlans);
        $modelVersion = 'plantix-fertilizer-v1.';

        $rows = [];

        foreach ($soilTests as $i => $soilTest) {
            $cropType   = $cropTypes[$i % count($cropTypes)];
            $plan       = $fertilizerPlans[$cropType];
            $growStages = $plan['growth_stages'];
            $growStage  = $growStages[array_rand($growStages)];

            // Estimate cost based on products and quantity
            $unitCosts = [
                'DAP (Di-Ammonium Phosphate)'  => 4200,  // PKR/50kg bag
                'DAP'                           => 4200,
                'Urea (46% N)'                 => 2200,
                'Urea'                          => 2200,
                'SOP (Sulfate of Potash)'       => 6500,
                'SOP'                           => 6500,
                'Zinc Sulfate 33%'             => 120,   // per kg
                'Zinc Sulfate'                 => 120,
                'Boron (Borax)'                => 250,
                'Ammonium Nitrate or Urea'     => 2200,
                'Calcium Ammonium Nitrate (CAN)' => 3200,
                'Potassium Chloride (MOP)'     => 5500,
            ];

            $estimatedCost = 0;
            foreach ($plan['basal_plan'] as $item) {
                $unit  = $unitCosts[$item['product']] ?? 2000;
                $estimatedCost += $item['quantity_kg_acre'] * ($unit / 50);
            }
            $estimatedCost = round($estimatedCost);

            $rows[] = [
                'user_id'                    => $soilTest->user_id,
                'soil_test_id'               => $soilTest->id,
                'crop_type'                  => $cropType,
                'growth_stage'               => $growStage,
                'nitrogen'                   => $soilTest->nitrogen,
                'phosphorus'                 => $soilTest->phosphorus,
                'potassium'                  => $soilTest->potassium,
                'ph_level'                   => $soilTest->ph_level,
                'temperature'                => $soilTest->temperature,
                'humidity'                   => $soilTest->humidity,
                'fertilizer_plan'            => json_encode($plan['basal_plan']),
                'application_instructions'   => $plan['application_instructions'],
                'estimated_cost_pkr'         => $estimatedCost,
                'model_version'              => $modelVersion . rand(1, 5),
                'created_at'                 => Carbon::now()->subDays(rand(5, 150)),
                'updated_at'                 => $now,
            ];
        }

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('fertilizer_recommendations')->insert($chunk);
        }

        $this->command->info('FertilizerRecommendationSeeder: ' . count($rows) . ' fertilizer recommendations seeded.');
    }
}
