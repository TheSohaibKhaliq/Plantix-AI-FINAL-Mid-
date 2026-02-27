<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FarmProfileSeeder extends Seeder
{
    public function run(): void
    {
        $farmers = DB::table('users')->where('role', 'user')->get();

        if ($farmers->isEmpty()) {
            $this->command->warn('No farmer users found.');
            return;
        }

        $now = Carbon::now();

        // Realistic farm names from Pakistani agricultural regions
        $farmNames = [
            'Rehmat Ullah Farm', 'Al-Noor Agricultural Land', 'Chaudhry Farms Sahiwal',
            'Green Valley Holdings', 'Punjab Agri Estate', 'Kissan Khushhal Farm',
            'Pak Mitti Farm', 'Al-Barakah Fields', 'Hussain Land Holdings',
            'Bismillah Organic Farm', 'Sher-e-Punjab Crops', 'Ranjha Agricultural Complex',
            'Chenab Basin Farm', 'Indus Plains Cultivation', 'Mian Farms Okara',
        ];

        $soilTypes    = ['clay', 'sandy', 'loamy', 'silt', 'peat'];
        $waterSources = ['rain', 'irrigation', 'both'];
        $climateZones = ['Arid', 'Semi-Arid', 'Humid Subtropical', 'Highland', 'Desert'];

        // Matrix of crop combinations per region (realistic crop rotations)
        $cropSets = [
            ['wheat', 'rice', 'cotton'],
            ['wheat', 'maize', 'sugarcane'],
            ['wheat', 'cotton', 'sunflower'],
            ['wheat', 'rice', 'lentil'],
            ['wheat', 'potato', 'onion'],
            ['wheat', 'tomato', 'chilli'],
            ['wheat', 'groundnut', 'sorghum'],
            ['wheat', 'chickpea', 'mustard'],
            ['cotton', 'wheat', 'sugarcane'],
            ['rice', 'wheat', 'lentil'],
            ['maize', 'wheat', 'sunflower'],
            ['sugarcane', 'wheat', 'cotton'],
            ['wheat', 'mung-bean', 'sesame'],
            ['wheat', 'barley', 'peas'],
            ['wheat', 'rape-seed', 'fodder'],
        ];

        $proLocations = [
            'Sheikhupura Road, Lahore', 'Satiana Road, Faisalabad', 'Bosan Road, Multan',
            'Hafizabad Road, Gujranwala', 'Model Town, Bahawalpur', 'Chichawatni, Sahiwal',
            'Depalpur Road, Okara', 'Chunian Road, Kasur', 'Liaqatpur, Rahim Yar Khan',
            'Muridke, Sheikhupura', 'Rohri Road, Sukkur', 'Qasimabad, Hyderabad',
            'Ring Road, Peshawar', 'Takht Bhai, Mardan', 'Chaman Road, Quetta',
        ];

        $farmNotes = [
            'Sandy loam soil with moderate fertility. Practicing laser land leveling.',
            'Well-irrigated land along canal belt. Historically high wheat yield.',
            'Traditional farming methods mixed with modern pesticide schedule.',
            'Family-owned land for three generations. Switching to drip irrigation.',
            'Recently soil-tested — requires phosphorus supplementation.',
            'Mixed crop farm with livestock integration for organic manure.',
            'Facing water scarcity; installed solar tube-well for irrigation.',
            'High organic matter due to years of green manure crop rotation.',
            'Low nitrogen levels identified last season; urea applied in basal dose.',
            'Saline-prone zone; need soil amendment with gypsum.',
            'Good permeability clay loan — ideal for rice paddy cultivation.',
            'Elevated potassium; no K-fertilizer needed this season.',
            'Regular soil testing since 2020 — significant yield improvement noted.',
            'Orchard integration with mango and citrus in borders.',
            'Government subsidy received for drip irrigation installation.',
        ];

        $farmProfiles = [];
        $soilTests    = [];

        foreach ($farmers as $i => $farmer) {
            $idx      = $i % 15;
            $farmSize = round(rand(5, 200) + (rand(0, 99) / 100), 2);

            $farmProfile = [
                'user_id'         => $farmer->id,
                'farm_name'       => $farmNames[$idx],
                'location'        => $proLocations[$idx],
                'farm_size_acres' => $farmSize,
                'soil_type'       => $soilTypes[array_rand($soilTypes)],
                'water_source'    => $waterSources[array_rand($waterSources)],
                'climate_zone'    => $climateZones[$idx % count($climateZones)],
                'previous_crops'  => json_encode($cropSets[$idx]),
                'notes'           => $farmNotes[$idx],
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
            $farmProfiles[] = $farmProfile;
        }

        // Insert farm profiles and retrieve IDs
        DB::table('farm_profiles')->insert($farmProfiles);
        $profileRows = DB::table('farm_profiles')->orderBy('id')->get(['id', 'user_id']);

        // Soil chemistry ranges for Pakistani soils
        // pH tends alkaline (7.0–8.5), N low-medium, P low, K high
        $soilTestLabels = ['Certified', 'Field Test', 'NARC Lab', 'Punjab Agriculture Dept.'];

        foreach ($profileRows as $profile) {
            $numTests = rand(1, 2); // 1–2 soil tests per farm
            for ($t = 0; $t < $numTests; $t++) {
                $testedAt = Carbon::now()->subDays(rand(10, 365));
                $soilTests[] = [
                    'user_id'         => $profile->user_id,
                    'farm_profile_id' => $profile->id,
                    'nitrogen'        => round(rand(20, 120) + (rand(0, 99) / 100), 2),   // kg/ha
                    'phosphorus'      => round(rand(8, 50)  + (rand(0, 99) / 100), 2),    // kg/ha
                    'potassium'       => round(rand(80, 250) + (rand(0, 99) / 100), 2),   // kg/ha
                    'ph_level'        => round((rand(65, 85) / 10), 1),                   // 6.5–8.5
                    'organic_matter'  => round(rand(5, 35) / 10, 1),                      // 0.5–3.5%
                    'humidity'        => rand(40, 80),                                     // %
                    'rainfall_mm'     => rand(200, 600),                                   // mm/year
                    'temperature'     => rand(22, 38),                                     // °C avg
                    'lab_report'      => $soilTestLabels[array_rand($soilTestLabels)],
                    'tested_at'       => $testedAt->toDateTimeString(),
                    'created_at'      => $testedAt->toDateTimeString(),
                    'updated_at'      => $now,
                ];
            }
        }

        foreach (array_chunk($soilTests, 50) as $chunk) {
            DB::table('soil_tests')->insert($chunk);
        }

        $this->command->info(sprintf(
            'FarmProfileSeeder: %d farm profiles, %d soil tests seeded.',
            count($farmProfiles),
            count($soilTests)
        ));
    }
}
