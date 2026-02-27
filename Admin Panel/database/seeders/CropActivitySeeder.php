<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CropActivitySeeder extends Seeder
{
    public function run(): void
    {
        $farmers      = DB::table('users')->where('role', 'user')->pluck('id');
        $farmProfiles = DB::table('farm_profiles')->get(['id', 'user_id', 'farm_size_acres', 'soil_type', 'climate_zone']);
        $soilTests    = DB::table('soil_tests')->get();

        if ($farmers->isEmpty() || $farmProfiles->isEmpty()) {
            $this->command->warn('Missing farmers or farm profiles. Run FarmProfileSeeder first.');
            return;
        }

        $now = Carbon::now();

        // ─── Crop recommendation data ────────────────────────────────────────────
        $cropRecommendationSets = [
            [
                'crops'       => ['Wheat', 'Chickpea', 'Mustard'],
                'explanation' => 'High pH (7.8) and moderate N levels favor Rabi crops. Wheat is primary choice; chickpea adds nitrogen fixation. Mustard for oilseed diversification.',
            ],
            [
                'crops'       => ['Rice', 'Cotton', 'Maize'],
                'explanation' => 'Loamy soil with adequate organic matter suits Kharif crops. Rice excels in waterlogged areas; cotton for fiber yield; maize as livestock feed.',
            ],
            [
                'crops'       => ['Sugarcane', 'Wheat', 'Potato'],
                'explanation' => 'High potassium (210 kg/ha) and irrigation availability support sugarcane. Wheat rotation improves soil structure. Potato as high-value cash crop.',
            ],
            [
                'crops'       => ['Cotton', 'Wheat', 'Sunflower'],
                'explanation' => 'Sandy loam with low humidity is ideal for cotton boll development. Sunflower tolerates alkaline conditions and adds vegetable oil value.',
            ],
            [
                'crops'       => ['Lentil', 'Wheat', 'Mung Bean'],
                'explanation' => 'Low nitrogen soils benefit from legume rotation (lentil, mung). Wheat as main cash crop. This rotation will naturally boost N by ~30 kg/ha next season.',
            ],
        ];

        // ─── Crop plan schedules ─────────────────────────────────────────────────
        $seasons = ['Rabi', 'Kharif', 'Zaid'];
        $years   = [2023, 2024];

        $cropScheduleTemplates = [
            'Wheat' => [
                ['week' => 1, 'activity' => 'Land preparation — deep ploughing + laser leveling'],
                ['week' => 2, 'activity' => 'Seed treatment with Vitavax fungicide 2g/kg seed'],
                ['week' => 3, 'activity' => 'Sowing — broadcast or drill method at 50kg/acre'],
                ['week' => 5, 'activity' => 'First irrigation (crown root initiation stage)'],
                ['week' => 8, 'activity' => 'Apply urea 25kg/acre (topdress)'],
                ['week' => 12, 'activity' => 'Second irrigation + weed control (Atlantis herbicide)'],
                ['week' => 16, 'activity' => 'Third irrigation at booting stage'],
                ['week' => 20, 'activity' => 'Foliar spray zinc sulfate 500g/100L water'],
                ['week' => 24, 'activity' => 'Harvest at grain moisture < 14%'],
            ],
            'Cotton' => [
                ['week' => 1, 'activity' => 'Land preparation — bed formation at 75cm spacing'],
                ['week' => 2, 'activity' => 'Seed treatment with Imidacloprid 70WP'],
                ['week' => 3, 'activity' => 'Sowing — dibbling method at 10cm in-row spacing'],
                ['week' => 5, 'activity' => 'First irrigation + half-dose urea 25kg/acre'],
                ['week' => 8, 'activity' => 'Thinning to one plant/hill'],
                ['week' => 10, 'activity' => 'Spray Chlorpyrifos for jassid control'],
                ['week' => 14, 'activity' => 'Second dose urea at square formation'],
                ['week' => 18, 'activity' => 'Foliar potassium + boron spray at boll setting'],
                ['week' => 22, 'activity' => 'First picking when bolls open > 60%'],
            ],
            'Rice' => [
                ['week' => 1, 'activity' => 'Nursery preparation — 1 kg seeds per marla nursery'],
                ['week' => 4, 'activity' => 'Transplanting — 25-30 day old seedlings, 2 per hill'],
                ['week' => 6, 'activity' => 'Apply DAP 25kg + Urea 15kg / acre basal'],
                ['week' => 8, 'activity' => 'Flood irrigation maintained at 5cm standing water'],
                ['week' => 12, 'activity' => 'Topdress urea 20kg/acre at tillering stage'],
                ['week' => 16, 'activity' => 'Panicle initiation — apply Zinc Sulfate'],
                ['week' => 20, 'activity' => 'Drain field 2 weeks before harvest'],
                ['week' => 22, 'activity' => 'Harvest when 85-90% grains turn golden straw color'],
            ],
        ];

        $waterPlanTemplates = [
            'Wheat' => [
                ['irrigation' => 1, 'day' => 25,  'stage' => 'Crown Root Initiation'],
                ['irrigation' => 2, 'day' => 55,  'stage' => 'Tillering'],
                ['irrigation' => 3, 'day' => 90,  'stage' => 'Jointing'],
                ['irrigation' => 4, 'day' => 115, 'stage' => 'Booting'],
                ['irrigation' => 5, 'day' => 135, 'stage' => 'Grain Filling'],
            ],
            'Cotton' => [
                ['irrigation' => 1, 'day' => 20,  'stage' => 'Seedling Establishment'],
                ['irrigation' => 2, 'day' => 45,  'stage' => 'Squaring'],
                ['irrigation' => 3, 'day' => 70,  'stage' => 'Boll Formation'],
                ['irrigation' => 4, 'day' => 95,  'stage' => 'Boll Maturation'],
            ],
            'Rice' => [
                ['irrigation' => 1, 'day' => 0,   'stage' => 'Continuous flood (0-5cm)'],
                ['irrigation' => 2, 'day' => 60,  'stage' => 'Drain at panicle initiation'],
                ['irrigation' => 3, 'day' => 80,  'stage' => 'Re-flood at heading'],
                ['irrigation' => 4, 'day' => 110, 'stage' => 'Drain 2 weeks before harvest'],
            ],
        ];

        $statuses = ['pending', 'completed', 'failed'];
        $planStatuses = ['draft', 'active', 'completed', 'archived'];
        $cropNames = ['Wheat', 'Cotton', 'Rice'];

        $recommendations = [];
        $cropPlans       = [];

        foreach ($farmProfiles as $profile) {
            // Find most recent soil test for this farm
            $soilTest = $soilTests->where('farm_profile_id', $profile->id)->sortByDesc('tested_at')->first();
            if (!$soilTest) {
                continue;
            }

            // 1–2 crop recommendations per profile
            $numRecs = rand(1, 2);
            for ($r = 0; $r < $numRecs; $r++) {
                $recSet = $cropRecommendationSets[$r % count($cropRecommendationSets)];
                $recommendations[] = [
                    'user_id'             => $profile->user_id,
                    'soil_test_id'        => $soilTest->id,
                    'nitrogen'            => $soilTest->nitrogen,
                    'phosphorus'          => $soilTest->phosphorus,
                    'potassium'           => $soilTest->potassium,
                    'ph_level'            => $soilTest->ph_level,
                    'humidity'            => $soilTest->humidity,
                    'rainfall_mm'         => $soilTest->rainfall_mm,
                    'temperature'         => $soilTest->temperature,
                    'recommended_crops'   => json_encode($recSet['crops']),
                    'explanation'         => $recSet['explanation'],
                    'model_version'       => 'plantix-ml-v2.' . rand(1, 4),
                    'status'              => $statuses[array_rand($statuses)],
                    'created_at'          => Carbon::now()->subDays(rand(10, 200)),
                    'updated_at'          => $now,
                ];
            }

            // 1 crop plan per profile
            $season    = $seasons[array_rand($seasons)];
            $year      = $years[array_rand($years)];
            $cropName  = $cropNames[array_rand($cropNames)];
            $sched     = $cropScheduleTemplates[$cropName]  ?? $cropScheduleTemplates['Wheat'];
            $waterPlan = $waterPlanTemplates[$cropName]     ?? $waterPlanTemplates['Wheat'];
            $yieldTons = round($profile->farm_size_acres * rand(8, 18) / 10, 2);

            $cropPlans[] = [
                'user_id'                => $profile->user_id,
                'farm_profile_id'        => $profile->id,
                'season'                 => $season,
                'year'                   => $year,
                'primary_crop'           => $cropName,
                'crop_schedule'          => json_encode($sched),
                'water_plan'             => json_encode($waterPlan),
                'expected_yield_tons'    => $yieldTons,
                'estimated_revenue'      => round($yieldTons * rand(30000, 55000), 0),
                'soil_suitability_notes' => 'pH ' . $soilTest->ph_level . ' suitable for ' . $cropName . '. Organic matter ' . $soilTest->organic_matter . '% — apply FYM 2 ton/acre before sowing.',
                'recommendations'        => 'Apply DAP 50kg/acre as basal dose. Topdress urea in 2 splits. Ensure adequate irrigation at critical stages.',
                'status'                 => $planStatuses[array_rand($planStatuses)],
                'created_at'             => Carbon::now()->subDays(rand(5, 180)),
                'updated_at'             => $now,
            ];
        }

        foreach (array_chunk($recommendations, 50) as $chunk) {
            DB::table('crop_recommendations')->insert($chunk);
        }
        foreach (array_chunk($cropPlans, 50) as $chunk) {
            DB::table('crop_plans')->insert($chunk);
        }

        $this->command->info(sprintf(
            'CropActivitySeeder: %d crop recommendations, %d crop plans seeded.',
            count($recommendations),
            count($cropPlans)
        ));
    }
}
