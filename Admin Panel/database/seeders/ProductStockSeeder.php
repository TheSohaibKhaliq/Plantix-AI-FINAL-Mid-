<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductStockSeeder extends Seeder
{
    public function run(): void
    {
        $products = DB::table('products')->get();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Run ProductsSeeder first.');
            return;
        }

        $now = Carbon::now();
        $stocks  = [];
        $images  = [];
        $counter = [];

        // Map vendor_id → short prefix for SKU
        $vendorPrefixes = [
            1  => 'PSC',  // Punjab Seeds Centre
            2  => 'SFS',  // Sona Fertilizer Store
            3  => 'ARA',  // Al-Rehman Agro
            4  => 'KAM',  // Kisaan Agri Mart
            5  => 'GLS',  // Green Land Seeds
            6  => 'MAA',  // Multan Agro Appliances
            7  => 'RAS',  // Rashid Agri Supplies
            8  => 'PAK',  // Pak Agri Hub
            9  => 'GFC',  // Green Field Crop
            10 => 'FHS',  // Frontier Herb & Seed
        ];

        // Category-level image stub paths (placeholder URLs)
        $imageStubs = [
            'seeds'          => 'products/seeds/',
            'fertilizers'    => 'products/fertilizers/',
            'pesticides'     => 'products/pesticides/',
            'insecticides'   => 'products/insecticides/',
            'herbicides'     => 'products/herbicides/',
            'fungicides'     => 'products/fungicides/',
            'pgr'            => 'products/pgr/',
            'bio-pesticides' => 'products/bio/',
            'irrigation'     => 'products/irrigation/',
            'farm-tools'     => 'products/tools/',
        ];

        foreach ($products as $product) {
            $vid    = $product->vendor_id ?? 1;
            $prefix = $vendorPrefixes[$vid] ?? 'AGR';

            if (!isset($counter[$vid])) {
                $counter[$vid] = 1;
            }
            $seq = str_pad($counter[$vid]++, 4, '0', STR_PAD_LEFT);
            $sku = $prefix . '-' . $seq;

            // Realistic stock quantities based on category
            $qty       = rand(30, 600);
            $threshold = max(10, (int) ($qty * 0.12));

            $stocks[] = [
                'product_id'          => $product->id,
                'vendor_id'           => $vid,
                'quantity'            => $qty,
                'low_stock_threshold' => $threshold,
                'sku'                 => $sku,
                'created_at'          => $now,
                'updated_at'          => $now,
            ];

            // Primary product image
            $folderKey = 'seeds'; // default
            $images[] = [
                'product_id' => $product->id,
                'path'       => $imageStubs[$folderKey] . $product->id . '.jpg',
                'alt_text'   => $product->name,
                'sort_order' => 1,
                'is_primary' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // 1-2 extra gallery images
            $extra = rand(1, 2);
            for ($i = 2; $i <= $extra + 1; $i++) {
                $images[] = [
                    'product_id' => $product->id,
                    'path'       => $imageStubs[$folderKey] . $product->id . '_' . $i . '.jpg',
                    'alt_text'   => $product->name . ' - view ' . $i,
                    'sort_order' => $i,
                    'is_primary' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert in chunks to stay within MySQL packet limit
        foreach (array_chunk($stocks, 100) as $chunk) {
            DB::table('product_stocks')->insertOrIgnore($chunk);
        }
        foreach (array_chunk($images, 100) as $chunk) {
            DB::table('product_images')->insertOrIgnore($chunk);
        }

        $this->command->info('ProductStockSeeder: ' . count($stocks) . ' stock records, ' . count($images) . ' images seeded.');
    }
}
