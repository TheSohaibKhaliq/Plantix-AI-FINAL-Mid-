<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Engro Fertilizers',  'slug' => 'engro-fertilizers',  'is_active' => true],
            ['name' => 'FFC (Fauji)',          'slug' => 'ffc-fauji',           'is_active' => true],
            ['name' => 'Syngenta',             'slug' => 'syngenta',            'is_active' => true],
            ['name' => 'Bayer CropScience',    'slug' => 'bayer-cropscience',   'is_active' => true],
            ['name' => 'BASF AgriSolutions',   'slug' => 'basf-agrisolutions',  'is_active' => true],
            ['name' => 'Corteva Agriscience',  'slug' => 'corteva-agriscience', 'is_active' => true],
            ['name' => 'National Foods',       'slug' => 'national-foods',      'is_active' => true],
            ['name' => 'Agri Tech Pakistan',   'slug' => 'agri-tech-pk',        'is_active' => true],
        ];

        foreach ($brands as $brand) {
            DB::table('brands')->updateOrInsert(
                ['slug' => $brand['slug']],
                array_merge($brand, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Brands seeded: ' . count($brands));
    }
}
