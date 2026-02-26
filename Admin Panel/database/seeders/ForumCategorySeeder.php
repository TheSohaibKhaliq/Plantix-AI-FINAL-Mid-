<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForumCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Crop Management',         'slug' => 'crop-management',        'description' => 'Tips and questions about managing different crops.',           'is_active' => true, 'sort_order' => 1],
            ['name' => 'Pest & Disease Control',   'slug' => 'pest-disease-control',   'description' => 'Identify and treat pests, fungi, bacteria and viruses.',       'is_active' => true, 'sort_order' => 2],
            ['name' => 'Fertilizers & Nutrition',  'slug' => 'fertilizers-nutrition',  'description' => 'Soil health, fertilizer schedules and micronutrient advice.',  'is_active' => true, 'sort_order' => 3],
            ['name' => 'Irrigation & Water',       'slug' => 'irrigation-water',       'description' => 'Irrigation techniques, water management and conservation.',     'is_active' => true, 'sort_order' => 4],
            ['name' => 'Farm Equipment',           'slug' => 'farm-equipment',         'description' => 'Machinery reviews, maintenance and buying advice.',             'is_active' => true, 'sort_order' => 5],
            ['name' => 'Weather & Climate',        'slug' => 'weather-climate',        'description' => 'Impact of weather on agriculture and seasonal planning.',       'is_active' => true, 'sort_order' => 6],
            ['name' => 'Market & Pricing',         'slug' => 'market-pricing',         'description' => 'Commodity prices, trading tips and market analysis.',          'is_active' => true, 'sort_order' => 7],
            ['name' => 'Success Stories',          'slug' => 'success-stories',        'description' => 'Farmers sharing their experiences and achievements.',          'is_active' => true, 'sort_order' => 8],
            ['name' => 'General Discussion',       'slug' => 'general-discussion',     'description' => 'Open floor for any farming topic.',                            'is_active' => true, 'sort_order' => 9],
        ];

        foreach ($categories as $cat) {
            DB::table('forum_categories')->updateOrInsert(
                ['slug' => $cat['slug']],
                array_merge($cat, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Forum categories seeded: ' . count($categories));
    }
}
