<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            [
                'name'        => 'Seeds',
                'description' => 'Certified & hybrid vegetable, cereal, and oilseed seeds for Pakistani farmers',
                'sort_order'  => 1,
            ],
            [
                'name'        => 'Fertilizers',
                'description' => 'Urea, DAP, SOP, NP and organic fertilizers for all crops',
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Pesticides',
                'description' => 'Broad-spectrum and selective pesticides for crop protection',
                'sort_order'  => 3,
            ],
            [
                'name'        => 'Insecticides',
                'description' => 'Chemical and biological insecticides to control insects in crops',
                'sort_order'  => 4,
            ],
            [
                'name'        => 'Herbicides',
                'description' => 'Pre and post-emergence weed control chemicals',
                'sort_order'  => 5,
            ],
            [
                'name'        => 'Fungicides',
                'description' => 'Systemic and contact fungicides to control fungal diseases',
                'sort_order'  => 6,
            ],
            [
                'name'        => 'Plant Growth Regulators',
                'description' => 'Hormones and growth regulators to improve yield and fruit quality',
                'sort_order'  => 7,
            ],
            [
                'name'        => 'Bio Pesticides',
                'description' => 'Organic and bio-rationale pest control solutions',
                'sort_order'  => 8,
            ],
            [
                'name'        => 'Irrigation Equipment',
                'description' => 'Drip, sprinkler, and furrow irrigation tools and fittings',
                'sort_order'  => 9,
            ],
            [
                'name'        => 'Farm Tools & Machinery',
                'description' => 'Hand tools, sprayers, and small machinery for agriculture',
                'sort_order'  => 10,
            ],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->insert([
                'name'        => $cat['name'],
                'description' => $cat['description'],
                'image'       => null,
                'active'      => true,
                'sort_order'  => $cat['sort_order'],
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        $this->command->info('CategoriesSeeder: ' . DB::table('categories')->count() . ' categories inserted.');
    }
}
