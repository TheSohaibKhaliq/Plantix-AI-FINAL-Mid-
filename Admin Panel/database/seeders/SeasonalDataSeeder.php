<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeasonalDataSeeder extends Seeder
{
    /**
     * Seed seasonal_data reference table with Pakistan-specific
     * Rabi / Kharif / Zaid crop calendars.
     *
     * Column mapping (matches 2026_02_27_100001 migration):
     *   sowing_months, harvesting_months, water_requirement_mm,
     *   soil_type_compatibility, min_temp_celsius, max_temp_celsius,
     *   avg_yield_tons_per_acre, region, is_active
     */
    public function run(): void
    {
        $now  = Carbon::now();
        $crops = [

            // â”€â”€â”€ Rabi (Octâ€“Apr, cool/dry season) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            [
                'crop_name'               => 'Wheat',
                'season'                  => 'Rabi',
                'region'                  => 'Punjab, Sindh, KPK',
                'sowing_months'           => 'October-November',
                'harvesting_months'       => 'March-April',
                'water_requirement_mm'    => 450.00,
                'soil_type_compatibility' => 'loamy, clay loam, silt loam',
                'min_temp_celsius'        => '8',
                'max_temp_celsius'        => '25',
                'avg_yield_tons_per_acre' => 1.500,
                'notes'                   => 'Most important staple crop of Pakistan. Requires 4â€“5 irrigations.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Chickpea',
                'season'                  => 'Rabi',
                'region'                  => 'Punjab, Balochistan',
                'sowing_months'           => 'October-November',
                'harvesting_months'       => 'February-March',
                'water_requirement_mm'    => 250.00,
                'soil_type_compatibility' => 'sandy loam, loamy',
                'min_temp_celsius'        => '5',
                'max_temp_celsius'        => '22',
                'avg_yield_tons_per_acre' => 0.600,
                'notes'                   => 'Drought-tolerant legume. Fixes atmospheric nitrogen.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Lentil',
                'season'                  => 'Rabi',
                'region'                  => 'Punjab, KPK',
                'sowing_months'           => 'November',
                'harvesting_months'       => 'March-April',
                'water_requirement_mm'    => 220.00,
                'soil_type_compatibility' => 'loamy, well-drained',
                'min_temp_celsius'        => '5',
                'max_temp_celsius'        => '20',
                'avg_yield_tons_per_acre' => 0.500,
                'notes'                   => 'Grown in Punjab and KPK. Prefers well-drained loamy soils.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Potato',
                'season'                  => 'Rabi',
                'region'                  => 'Punjab, KPK',
                'sowing_months'           => 'October',
                'harvesting_months'       => 'December-January',
                'water_requirement_mm'    => 700.00,
                'soil_type_compatibility' => 'sandy loam, loamy',
                'min_temp_celsius'        => '7',
                'max_temp_celsius'        => '20',
                'avg_yield_tons_per_acre' => 8.000,
                'notes'                   => 'High-value vegetable crop. Cool temps preferred for tuber development.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Onion',
                'season'                  => 'Rabi',
                'region'                  => 'Sindh, Punjab',
                'sowing_months'           => 'November',
                'harvesting_months'       => 'March-April',
                'water_requirement_mm'    => 500.00,
                'soil_type_compatibility' => 'sandy loam, loamy',
                'min_temp_celsius'        => '10',
                'max_temp_celsius'        => '25',
                'avg_yield_tons_per_acre' => 5.000,
                'notes'                   => 'Major cash crop in Sindh. Prefers sandy loam soil.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Mustard',
                'season'                  => 'Rabi',
                'region'                  => 'Punjab, Sindh',
                'sowing_months'           => 'October-November',
                'harvesting_months'       => 'February-March',
                'water_requirement_mm'    => 300.00,
                'soil_type_compatibility' => 'loamy, sandy loam, clay loam',
                'min_temp_celsius'        => '5',
                'max_temp_celsius'        => '25',
                'avg_yield_tons_per_acre' => 0.600,
                'notes'                   => 'Important oilseed crop. Tolerates mild frost.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Sunflower',
                'season'                  => 'Rabi',
                'region'                  => 'Punjab, Sindh',
                'sowing_months'           => 'January',
                'harvesting_months'       => 'April',
                'water_requirement_mm'    => 280.00,
                'soil_type_compatibility' => 'loamy, sandy loam',
                'min_temp_celsius'        => '10',
                'max_temp_celsius'        => '30',
                'avg_yield_tons_per_acre' => 0.700,
                'notes'                   => 'Oil crop. Short duration. Suitable as relay crop after cotton.',
                'is_active'               => true,
            ],

            // â”€â”€â”€ Kharif (Junâ€“Oct, hot/wet season) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            [
                'crop_name'               => 'Rice',
                'season'                  => 'Kharif',
                'region'                  => 'Punjab, Sindh',
                'sowing_months'           => 'May-June',
                'harvesting_months'       => 'October-November',
                'water_requirement_mm'    => 1200.00,
                'soil_type_compatibility' => 'clay, clay loam',
                'min_temp_celsius'        => '20',
                'max_temp_celsius'        => '38',
                'avg_yield_tons_per_acre' => 1.800,
                'notes'                   => 'Major export commodity. Requires standing water for 60â€“90 days.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Maize',
                'season'                  => 'Kharif',
                'region'                  => 'Punjab, KPK',
                'sowing_months'           => 'June',
                'harvesting_months'       => 'September',
                'water_requirement_mm'    => 650.00,
                'soil_type_compatibility' => 'loamy, well-drained',
                'min_temp_celsius'        => '18',
                'max_temp_celsius'        => '35',
                'avg_yield_tons_per_acre' => 2.000,
                'notes'                   => 'Second most important cereal. Used for fodder, food, and feed.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Cotton',
                'season'                  => 'Kharif',
                'region'                  => 'Punjab, Sindh',
                'sowing_months'           => 'April-May',
                'harvesting_months'       => 'October-November',
                'water_requirement_mm'    => 700.00,
                'soil_type_compatibility' => 'sandy loam, loamy',
                'min_temp_celsius'        => '18',
                'max_temp_celsius'        => '40',
                'avg_yield_tons_per_acre' => 1.200,
                'notes'                   => 'White gold of Pakistan. Major export earner. Sandy loam preferred.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Sugarcane',
                'season'                  => 'Kharif',
                'region'                  => 'Punjab, Sindh',
                'sowing_months'           => 'February-March',
                'harvesting_months'       => 'November-January',
                'water_requirement_mm'    => 1100.00,
                'soil_type_compatibility' => 'loamy, clay loam',
                'min_temp_celsius'        => '20',
                'max_temp_celsius'        => '42',
                'avg_yield_tons_per_acre' => 40.000,
                'notes'                   => 'Long-duration crop (9â€“12 months). Needs 8â€“10 irrigations.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Tomato (Kharif)',
                'season'                  => 'Kharif',
                'region'                  => 'Punjab, Sindh, KPK',
                'sowing_months'           => 'July',
                'harvesting_months'       => 'September-October',
                'water_requirement_mm'    => 650.00,
                'soil_type_compatibility' => 'loamy, sandy loam',
                'min_temp_celsius'        => '15',
                'max_temp_celsius'        => '32',
                'avg_yield_tons_per_acre' => 10.000,
                'notes'                   => 'High-value vegetable. Disease-prone; monitor weekly.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Groundnut',
                'season'                  => 'Kharif',
                'region'                  => 'Punjab, Sindh',
                'sowing_months'           => 'June',
                'harvesting_months'       => 'September',
                'water_requirement_mm'    => 500.00,
                'soil_type_compatibility' => 'sandy loam, sandy',
                'min_temp_celsius'        => '20',
                'max_temp_celsius'        => '35',
                'avg_yield_tons_per_acre' => 0.900,
                'notes'                   => 'Oil and protein crop. Nodule bacteria fix nitrogen.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Sorghum',
                'season'                  => 'Kharif',
                'region'                  => 'Punjab, Sindh, Balochistan',
                'sowing_months'           => 'June-July',
                'harvesting_months'       => 'October',
                'water_requirement_mm'    => 300.00,
                'soil_type_compatibility' => 'all types, drought tolerant',
                'min_temp_celsius'        => '15',
                'max_temp_celsius'        => '38',
                'avg_yield_tons_per_acre' => 1.500,
                'notes'                   => 'Drought-tolerant cereal and fodder crop for arid areas.',
                'is_active'               => true,
            ],

            // â”€â”€â”€ Zaid (Febâ€“May, spring / short season) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            [
                'crop_name'               => 'Mango',
                'season'                  => 'Zaid',
                'region'                  => 'Punjab, Sindh, KPK',
                'sowing_months'           => 'February-March',
                'harvesting_months'       => 'June-August',
                'water_requirement_mm'    => 500.00,
                'soil_type_compatibility' => 'loamy, deep well-drained',
                'min_temp_celsius'        => '18',
                'max_temp_celsius'        => '42',
                'avg_yield_tons_per_acre' => 5.000,
                'notes'                   => 'King of fruits. Major export crop from Punjab and Sindh.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Banana',
                'season'                  => 'Zaid',
                'region'                  => 'Sindh',
                'sowing_months'           => 'February',
                'harvesting_months'       => 'October-November',
                'water_requirement_mm'    => 1200.00,
                'soil_type_compatibility' => 'loamy, deep fertile',
                'min_temp_celsius'        => '20',
                'max_temp_celsius'        => '38',
                'avg_yield_tons_per_acre' => 15.000,
                'notes'                   => 'Year-round crop in warmer regions (Sindh). 8+ months to harvest.',
                'is_active'               => true,
            ],
            [
                'crop_name'               => 'Watermelon',
                'season'                  => 'Zaid',
                'region'                  => 'Punjab, Sindh',
                'sowing_months'           => 'February-March',
                'harvesting_months'       => 'May-June',
                'water_requirement_mm'    => 450.00,
                'soil_type_compatibility' => 'sandy loam, sandy',
                'min_temp_celsius'        => '18',
                'max_temp_celsius'        => '40',
                'avg_yield_tons_per_acre' => 8.000,
                'notes'                   => 'High demand in summer. Short duration â€“ 75-90 days.',
                'is_active'               => true,
            ],
        ];

        foreach ($crops as $crop) {
            DB::table('seasonal_data')->updateOrInsert(
                ['crop_name' => $crop['crop_name'], 'season' => $crop['season']],
                array_merge($crop, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        $this->command->info('SeasonalDataSeeder: ' . count($crops) . ' crop records seeded.');
    }
}
