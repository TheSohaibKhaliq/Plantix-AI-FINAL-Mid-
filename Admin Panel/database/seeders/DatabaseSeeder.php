<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Each seeder is called in dependency order:
     *  1. Users            – required by all other seeders
     *  2. Zones            – required by vendors
     *  3. Categories       – required by vendors & products
     *  4. Roles/Perms      – assigns role_id to admin users
     *  5. Vendors          – required by products, coupons, orders
     *  6. Products         – required by orders
     *  7. Taxes & Coupons  – coupons reference vendors
     *  8. Orders           – references users, vendors, products, coupons
     *  9. Wallet/Payouts   – references users, vendors, orders
     * 10. Misc             – gift cards, on-board slides, store filters, currencies, CMS
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            ZonesSeeder::class,
            CategoriesSeeder::class,
            RolesPermissionsSeeder::class,
            VendorsSeeder::class,
            ProductsSeeder::class,
            TaxesAndCouponsSeeder::class,
            OrdersSeeder::class,
            WalletAndPayoutsSeeder::class,
            MiscSeeder::class,
        ]);
    }
}
