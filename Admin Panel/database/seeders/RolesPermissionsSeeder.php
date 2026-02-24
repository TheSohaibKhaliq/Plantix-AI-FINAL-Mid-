<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ── Roles ─────────────────────────────────────────────────
        $roles = [
            ['role_name' => 'Super Admin',    'guard' => 'web', 'is_active' => true],
            ['role_name' => 'Store Manager',  'guard' => 'web', 'is_active' => true],
            ['role_name' => 'Order Manager',  'guard' => 'web', 'is_active' => true],
            ['role_name' => 'Product Manager','guard' => 'web', 'is_active' => true],
            ['role_name' => 'Report Viewer',  'guard' => 'web', 'is_active' => true],
        ];

        foreach ($roles as $r) {
            DB::table('role')->insertOrIgnore(array_merge($r, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // ── Permissions ──────────────────────────────────────────
        $permissions = [
            // Vendors / Stores
            ['name' => 'vendors.view',   'group' => 'vendors',  'display_name' => 'View Stores'],
            ['name' => 'vendors.create', 'group' => 'vendors',  'display_name' => 'Create Store'],
            ['name' => 'vendors.edit',   'group' => 'vendors',  'display_name' => 'Edit Store'],
            ['name' => 'vendors.delete', 'group' => 'vendors',  'display_name' => 'Delete Store'],
            // Products
            ['name' => 'products.view',  'group' => 'products', 'display_name' => 'View Products'],
            ['name' => 'products.create','group' => 'products', 'display_name' => 'Create Product'],
            ['name' => 'products.edit',  'group' => 'products', 'display_name' => 'Edit Product'],
            ['name' => 'products.delete','group' => 'products', 'display_name' => 'Delete Product'],
            // Orders
            ['name' => 'orders.view',    'group' => 'orders',   'display_name' => 'View Orders'],
            ['name' => 'orders.update',  'group' => 'orders',   'display_name' => 'Update Order Status'],
            ['name' => 'orders.delete',  'group' => 'orders',   'display_name' => 'Delete Order'],
            // Users
            ['name' => 'users.view',     'group' => 'users',    'display_name' => 'View Users'],
            ['name' => 'users.create',   'group' => 'users',    'display_name' => 'Create User'],
            ['name' => 'users.edit',     'group' => 'users',    'display_name' => 'Edit User'],
            ['name' => 'users.delete',   'group' => 'users',    'display_name' => 'Delete User'],
            // Categories
            ['name' => 'categories.view',   'group' => 'categories', 'display_name' => 'View Categories'],
            ['name' => 'categories.create', 'group' => 'categories', 'display_name' => 'Create Category'],
            ['name' => 'categories.edit',   'group' => 'categories', 'display_name' => 'Edit Category'],
            ['name' => 'categories.delete', 'group' => 'categories', 'display_name' => 'Delete Category'],
            // Coupons
            ['name' => 'coupons.view',   'group' => 'coupons',  'display_name' => 'View Coupons'],
            ['name' => 'coupons.create', 'group' => 'coupons',  'display_name' => 'Create Coupon'],
            ['name' => 'coupons.edit',   'group' => 'coupons',  'display_name' => 'Edit Coupon'],
            ['name' => 'coupons.delete', 'group' => 'coupons',  'display_name' => 'Delete Coupon'],
            // Reports
            ['name' => 'reports.view',   'group' => 'reports',  'display_name' => 'View Reports'],
            ['name' => 'reports.export', 'group' => 'reports',  'display_name' => 'Export Reports'],
            // Settings
            ['name' => 'settings.view',  'group' => 'settings', 'display_name' => 'View Settings'],
            ['name' => 'settings.edit',  'group' => 'settings', 'display_name' => 'Edit Settings'],
            // Payouts
            ['name' => 'payouts.view',   'group' => 'payouts',  'display_name' => 'View Payouts'],
            ['name' => 'payouts.approve','group' => 'payouts',  'display_name' => 'Approve Payouts'],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->insertOrIgnore(array_merge($p, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // ── Role-Permission Assignments ──────────────────────────
        $allPerms       = DB::table('permissions')->pluck('id')->toArray();
        $superAdminId   = DB::table('role')->where('role_name', 'Super Admin')->value('id');
        $storeManagerId = DB::table('role')->where('role_name', 'Store Manager')->value('id');
        $orderManagerId = DB::table('role')->where('role_name', 'Order Manager')->value('id');
        $prodManagerId  = DB::table('role')->where('role_name', 'Product Manager')->value('id');
        $reportViewerId = DB::table('role')->where('role_name', 'Report Viewer')->value('id');

        // Super Admin → all permissions
        foreach ($allPerms as $pid) {
            DB::table('role_permissions')->insertOrIgnore([
                'role_id'       => $superAdminId,
                'permission_id' => $pid,
            ]);
        }

        // Store Manager → vendor, product, category read/write
        $storePerms = DB::table('permissions')
            ->whereIn('group', ['vendors', 'products', 'categories'])
            ->pluck('id');
        foreach ($storePerms as $pid) {
            DB::table('role_permissions')->insertOrIgnore([
                'role_id'       => $storeManagerId,
                'permission_id' => $pid,
            ]);
        }

        // Order Manager → orders + reports view
        $orderPerms = DB::table('permissions')
            ->whereIn('group', ['orders', 'reports'])
            ->pluck('id');
        foreach ($orderPerms as $pid) {
            DB::table('role_permissions')->insertOrIgnore([
                'role_id'       => $orderManagerId,
                'permission_id' => $pid,
            ]);
        }

        // Product Manager → products + categories
        $prodPerms = DB::table('permissions')
            ->whereIn('group', ['products', 'categories'])
            ->pluck('id');
        foreach ($prodPerms as $pid) {
            DB::table('role_permissions')->insertOrIgnore([
                'role_id'       => $prodManagerId,
                'permission_id' => $pid,
            ]);
        }

        // Report Viewer → reports view only
        $reportViewPerms = DB::table('permissions')
            ->where('name', 'reports.view')
            ->pluck('id');
        foreach ($reportViewPerms as $pid) {
            DB::table('role_permissions')->insertOrIgnore([
                'role_id'       => $reportViewerId,
                'permission_id' => $pid,
            ]);
        }

        // Assign Super Admin role to admin user
        DB::table('users')
            ->where('role', 'admin')
            ->update(['role_id' => $superAdminId]);

        $this->command->info('RolesPermissionsSeeder: ' . DB::table('role')->count() . ' roles, ' . DB::table('permissions')->count() . ' permissions inserted.');
    }
}
