<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * AdminSuperUserSeeder — always runs LAST in DatabaseSeeder.
 *
 * Guarantees:
 *  1. admin@plantixai.com exists, is active, has role='admin', role_id=NULL.
 *     role_id=NULL is the super-admin sentinel — PermissionMiddleware grants full
 *     access to any user with role='admin' AND role_id=NULL.  No permission
 *     records need to exist at all for this user to work.
 *
 *  2. Every permission row required by every route in routes/panels/admin.php
 *     is present in the permissions table (incl. the 'zone' group that was
 *     triggering the 403).
 *
 *  3. A "Super Administrator" role exists in the role table and carries ALL
 *     permissions.  Staff / secondary admins can be manually assigned this role.
 *
 *  4. Any prior seeder that accidentally set role_id on the primary admin user
 *     (RolesPermissionsSeeder, AssignAllPermissionsToAdminSeeder) is undone here.
 */
class AdminSuperUserSeeder extends Seeder
{
    // ── Complete permission manifest (every group used in admin.php routes) ────
    private array $permissions = [
        // Users
        ['name' => 'users',              'group' => 'users',               'display_name' => 'View Users'],
        ['name' => 'users.edit',         'group' => 'users',               'display_name' => 'Edit Users'],
        ['name' => 'users.create',       'group' => 'users',               'display_name' => 'Create Users'],
        ['name' => 'users.view',         'group' => 'users',               'display_name' => 'View User Detail'],
        // Admin Users
        ['name' => 'admin.users',        'group' => 'admins',              'display_name' => 'View Admin Users'],
        ['name' => 'admin.users.create', 'group' => 'admins',              'display_name' => 'Create Admin Users'],
        ['name' => 'admin.users.store',  'group' => 'admins',              'display_name' => 'Store Admin Users'],
        ['name' => 'admin.users.delete', 'group' => 'admins',              'display_name' => 'Delete Admin Users'],
        ['name' => 'admin.users.edit',   'group' => 'admins',              'display_name' => 'Edit Admin Users'],
        ['name' => 'admin.users.update', 'group' => 'admins',              'display_name' => 'Update Admin Users'],
        // Vendors
        ['name' => 'vendors',              'group' => 'vendors',            'display_name' => 'View Vendors'],
        ['name' => 'approve.vendors.list', 'group' => 'approve_vendors',    'display_name' => 'Approve Vendors'],
        ['name' => 'pending.vendors.list', 'group' => 'pending_vendors',    'display_name' => 'Pending Vendors'],
        ['name' => 'vendor.document.list', 'group' => 'vendors-document',   'display_name' => 'List Vendor Documents'],
        ['name' => 'vendor.document.edit', 'group' => 'vendors-document',   'display_name' => 'Edit Vendor Documents'],
        // Stores
        ['name' => 'stores',        'group' => 'stores',                   'display_name' => 'View Stores'],
        ['name' => 'stores.create', 'group' => 'stores',                   'display_name' => 'Create Stores'],
        ['name' => 'stores.edit',   'group' => 'stores',                   'display_name' => 'Edit Stores'],
        ['name' => 'stores.view',   'group' => 'stores',                   'display_name' => 'View Store Detail'],
        // Drivers
        ['name' => 'drivers',               'group' => 'drivers',           'display_name' => 'View Drivers'],
        ['name' => 'drivers.create',        'group' => 'drivers',           'display_name' => 'Create Drivers'],
        ['name' => 'drivers.edit',          'group' => 'drivers',           'display_name' => 'Edit Drivers'],
        ['name' => 'drivers.view',          'group' => 'drivers',           'display_name' => 'View Driver Detail'],
        ['name' => 'approve.driver.list',   'group' => 'approve_drivers',   'display_name' => 'Approve Drivers'],
        ['name' => 'pending.driver.list',   'group' => 'pending_drivers',   'display_name' => 'Pending Drivers'],
        ['name' => 'driver.document.list',  'group' => 'drivers-document',  'display_name' => 'List Driver Documents'],
        ['name' => 'driver.document.edit',  'group' => 'drivers-document',  'display_name' => 'Edit Driver Documents'],
        // Products / Items
        ['name' => 'items',        'group' => 'items',                     'display_name' => 'View Items'],
        ['name' => 'items.create', 'group' => 'items',                     'display_name' => 'Create Items'],
        ['name' => 'items.edit',   'group' => 'items',                     'display_name' => 'Edit Items'],
        // Item Attributes
        ['name' => 'attributes',        'group' => 'item-attribute',        'display_name' => 'View Attributes'],
        ['name' => 'attributes.create', 'group' => 'item-attribute',        'display_name' => 'Create Attributes'],
        ['name' => 'attributes.edit',   'group' => 'item-attribute',        'display_name' => 'Edit Attributes'],
        // Categories
        ['name' => 'categories',        'group' => 'category',              'display_name' => 'View Categories'],
        ['name' => 'categories.create', 'group' => 'category',              'display_name' => 'Create Categories'],
        ['name' => 'categories.edit',   'group' => 'category',              'display_name' => 'Edit Categories'],
        // Orders
        ['name' => 'orders',              'group' => 'orders',              'display_name' => 'View Orders'],
        ['name' => 'orders.edit',         'group' => 'orders',              'display_name' => 'Edit Orders'],
        ['name' => 'vendors.orderprint',  'group' => 'orders',              'display_name' => 'Print Orders'],
        ['name' => 'stores.booktable',    'group' => 'dinein-orders',       'display_name' => 'View Dine-In Orders'],
        ['name' => 'booktable.edit',      'group' => 'dinein-orders',       'display_name' => 'Edit Dine-In Orders'],
        // Coupons
        ['name' => 'coupons',        'group' => 'coupons',                  'display_name' => 'View Coupons'],
        ['name' => 'coupons.create', 'group' => 'coupons',                  'display_name' => 'Create Coupons'],
        ['name' => 'coupons.edit',   'group' => 'coupons',                  'display_name' => 'Edit Coupons'],
        // Payments & Payouts
        ['name' => 'payment-method',          'group' => 'payment-method',  'display_name' => 'Manage Payment Methods'],
        ['name' => 'payments',                'group' => 'payments',         'display_name' => 'View Payments'],
        ['name' => 'payoutRequests.stores',   'group' => 'payout-request',   'display_name' => 'Store Payout Requests'],
        ['name' => 'payoutRequests.drivers',  'group' => 'payout-request',   'display_name' => 'Driver Payout Requests'],
        ['name' => 'driver.driverpayments',   'group' => 'driver-payments',  'display_name' => 'View Driver Payments'],
        ['name' => 'driversPayouts',          'group' => 'driver-payouts',   'display_name' => 'View Driver Payouts'],
        ['name' => 'driversPayouts.create',   'group' => 'driver-payouts',   'display_name' => 'Create Driver Payouts'],
        ['name' => 'storesPayouts',           'group' => 'store-payouts',    'display_name' => 'View Store Payouts'],
        ['name' => 'storesPayouts.create',    'group' => 'store-payouts',    'display_name' => 'Create Store Payouts'],
        ['name' => 'walletstransaction',      'group' => 'wallet-transaction','display_name' => 'Wallet Transactions'],
        // Reports
        ['name' => 'report.index', 'group' => 'reports',                    'display_name' => 'View Reports'],
        // Banners / CMS
        ['name' => 'setting.banners',        'group' => 'banners',           'display_name' => 'View Banners'],
        ['name' => 'setting.banners.create', 'group' => 'banners',           'display_name' => 'Create Banners'],
        ['name' => 'setting.banners.edit',   'group' => 'banners',           'display_name' => 'Edit Banners'],
        ['name' => 'cms',        'group' => 'cms',                           'display_name' => 'View CMS Pages'],
        ['name' => 'cms.create', 'group' => 'cms',                           'display_name' => 'Create CMS Pages'],
        ['name' => 'cms.edit',   'group' => 'cms',                           'display_name' => 'Edit CMS Pages'],
        // Documents
        ['name' => 'documents.list',   'group' => 'documents',               'display_name' => 'View Documents'],
        ['name' => 'documents.create', 'group' => 'documents',               'display_name' => 'Create Documents'],
        ['name' => 'documents.edit',   'group' => 'documents',               'display_name' => 'Edit Documents'],
        // Email Templates
        ['name' => 'email-templates.index',  'group' => 'email-template',    'display_name' => 'View Email Templates'],
        ['name' => 'email-templates.edit',   'group' => 'email-template',    'display_name' => 'Edit Email Templates'],
        ['name' => 'email-templates.delete', 'group' => 'email-template',    'display_name' => 'Delete Email Templates'],
        // Notifications
        ['name' => 'dynamic-notification.index',  'group' => 'dynamic-notifications', 'display_name' => 'View Dynamic Notifications'],
        ['name' => 'dynamic-notification.save',   'group' => 'dynamic-notifications', 'display_name' => 'Create Dynamic Notifications'],
        ['name' => 'dynamic-notification.delete', 'group' => 'dynamic-notifications', 'display_name' => 'Delete Dynamic Notifications'],
        ['name' => 'notification',      'group' => 'general-notifications',  'display_name' => 'View Notifications'],
        ['name' => 'notification.send', 'group' => 'general-notifications',  'display_name' => 'Send Notifications'],
        // Gift Cards
        ['name' => 'gift-card.index', 'group' => 'gift-cards',               'display_name' => 'View Gift Cards'],
        ['name' => 'gift-card.save',  'group' => 'gift-cards',               'display_name' => 'Create Gift Cards'],
        ['name' => 'gift-card.edit',  'group' => 'gift-cards',               'display_name' => 'Edit Gift Cards'],
        // Settings
        ['name' => 'settings.app.languages',          'group' => 'language',              'display_name' => 'View Languages'],
        ['name' => 'settings.app.languages.create',   'group' => 'language',              'display_name' => 'Create Languages'],
        ['name' => 'settings.app.languages.edit',     'group' => 'language',              'display_name' => 'Edit Languages'],
        ['name' => 'currencies',                       'group' => 'currency',              'display_name' => 'View Currencies'],
        ['name' => 'currencies.create',               'group' => 'currency',              'display_name' => 'Create Currencies'],
        ['name' => 'currencies.edit',                 'group' => 'currency',              'display_name' => 'Edit Currencies'],
        ['name' => 'tax',                             'group' => 'tax',                   'display_name' => 'View Tax'],
        ['name' => 'tax.create',                      'group' => 'tax',                   'display_name' => 'Create Tax'],
        ['name' => 'tax.edit',                        'group' => 'tax',                   'display_name' => 'Edit Tax'],
        // ── Zone (was the missing group causing the 403) ──────────────────────
        ['name' => 'zone.list',   'group' => 'zone',                         'display_name' => 'View Zones'],
        ['name' => 'zone.create', 'group' => 'zone',                         'display_name' => 'Create Zones'],
        ['name' => 'zone.edit',   'group' => 'zone',                         'display_name' => 'Edit Zones'],
        // App-wide settings
        ['name' => 'settings.app.globals',              'group' => 'global-setting',        'display_name' => 'Global Settings'],
        ['name' => 'settings.app.deliveryCharge',       'group' => 'delivery-charge',       'display_name' => 'Delivery Charge Settings'],
        ['name' => 'settings.app.radiusConfiguration',  'group' => 'radius',                'display_name' => 'Radius Configuration'],
        ['name' => 'settings.app.adminCommission',      'group' => 'admin-commission',      'display_name' => 'Admin Commission Settings'],
        ['name' => 'settings.app.bookTable',            'group' => 'dinein',                'display_name' => 'Dine-In / Book Table Settings'],
        ['name' => 'settings.app.documentVerification', 'group' => 'document-verification', 'display_name' => 'Document Verification Settings'],
        ['name' => 'setting.specialOffer',              'group' => 'special-offer',         'display_name' => 'Special Offer Settings'],
        ['name' => 'privacyPolicy',                     'group' => 'privacy',               'display_name' => 'Privacy Policy'],
        ['name' => 'termsAndConditions',                'group' => 'terms',                 'display_name' => 'Terms & Conditions'],
        // Map / God-Eye
        ['name' => 'map', 'group' => 'god-eye',                             'display_name' => 'God-Eye Map'],
        // Review Attributes
        ['name' => 'reviewattributes',        'group' => 'review-attribute', 'display_name' => 'View Review Attributes'],
        ['name' => 'reviewattributes.create', 'group' => 'review-attribute', 'display_name' => 'Create Review Attributes'],
        ['name' => 'reviewattributes.edit',   'group' => 'review-attribute', 'display_name' => 'Edit Review Attributes'],
        // RBAC / Roles
        ['name' => 'role.index',         'group' => 'roles',                 'display_name' => 'View Roles'],
        ['name' => 'role.save',          'group' => 'roles',                 'display_name' => 'Create Role Form'],
        ['name' => 'role.store',         'group' => 'roles',                 'display_name' => 'Create Roles'],
        ['name' => 'role.edit',          'group' => 'roles',                 'display_name' => 'Edit Roles'],
        ['name' => 'role.update',        'group' => 'roles',                 'display_name' => 'Update Roles'],
        ['name' => 'role.delete',        'group' => 'roles',                 'display_name' => 'Delete Roles'],
        ['name' => 'permissions.manage', 'group' => 'roles',                 'display_name' => 'Manage Permissions'],
        // Plantix AI / Experts / Appointments
        ['name' => 'experts.index',       'group' => 'experts',              'display_name' => 'View Experts'],
        ['name' => 'experts.create',      'group' => 'experts',              'display_name' => 'Create Experts'],
        ['name' => 'experts.edit',        'group' => 'experts',              'display_name' => 'Edit Experts'],
        ['name' => 'experts.delete',      'group' => 'experts',              'display_name' => 'Delete Experts'],
        ['name' => 'appointments.index',  'group' => 'appointments',         'display_name' => 'View Appointments'],
        ['name' => 'appointments.edit',   'group' => 'appointments',         'display_name' => 'Edit Appointments'],
        ['name' => 'ai.dashboard',        'group' => 'ai-modules',           'display_name' => 'AI Dashboard'],
        ['name' => 'ai.chat.index',       'group' => 'ai-modules',           'display_name' => 'View AI Chat Sessions'],
        ['name' => 'forum.admin.index',   'group' => 'forum',                'display_name' => 'View Forum'],
        ['name' => 'forum.admin.edit',    'group' => 'forum',                'display_name' => 'Edit Forum Threads'],
        ['name' => 'forum.admin.delete',  'group' => 'forum',                'display_name' => 'Delete Forum Threads'],
        ['name' => 'crop.disease.index',  'group' => 'crop-disease',         'display_name' => 'View Crop Disease Reports'],
        ['name' => 'crop.disease.view',   'group' => 'crop-disease',         'display_name' => 'View Crop Disease Detail'],
    ];

    public function run(): void
    {
        $now = Carbon::now();

        DB::transaction(function () use ($now) {

            // ── 1. Upsert every permission row ────────────────────────────────
            foreach ($this->permissions as $p) {
                $existing = DB::table('permissions')->where('name', $p['name'])->first();
                if (!$existing) {
                    DB::table('permissions')->insert(array_merge($p, [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]));
                } else {
                    // Ensure group is set correctly (may have been wrong in prior seeders)
                    DB::table('permissions')->where('name', $p['name'])->update([
                        'group'        => $p['group'],
                        'display_name' => $p['display_name'],
                        'updated_at'   => $now,
                    ]);
                }
            }
            $totalPerms = DB::table('permissions')->count();
            $this->command->info("✓ {$totalPerms} permissions in database.");

            // ── 2. Upsert "Super Administrator" role (guard=admin) ────────────
            $superRole = DB::table('role')->where('role_name', 'Super Administrator')->first();
            if (!$superRole) {
                DB::table('role')->insert([
                    'role_name'  => 'Super Administrator',
                    'guard'      => 'admin',
                    'is_active'  => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $superRole = DB::table('role')->where('role_name', 'Super Administrator')->first();
            }
            $this->command->info("✓ 'Super Administrator' role ID: {$superRole->id}");

            // ── 3. Assign ALL permissions to Super Administrator role ─────────
            $allPermIds = DB::table('permissions')->pluck('id')->toArray();
            foreach ($allPermIds as $pid) {
                DB::table('role_permissions')->insertOrIgnore([
                    'role_id'       => $superRole->id,
                    'permission_id' => $pid,
                ]);
            }
            $assigned = DB::table('role_permissions')->where('role_id', $superRole->id)->count();
            $this->command->info("✓ {$assigned} permissions assigned to Super Administrator role.");

            // ── 4. Ensure primary admin user is correct ───────────────────────
            // CRITICAL: role='admin' + role_id=NULL = PermissionMiddleware bypass.
            // Any seeder that sets role_id on this user breaks zone/all permission checks.
            $adminUser = DB::table('users')->where('email', 'admin@plantixai.com')->first();
            if (!$adminUser) {
                DB::table('users')->insert([
                    'name'                  => 'Super Admin',
                    'email'                 => 'admin@plantixai.com',
                    'password'              => Hash::make('Admin@1234'),
                    'phone'                 => '+92-300-0000001',
                    'role'                  => 'admin',
                    'role_id'               => null,   // ← must be NULL for bypass
                    'active'                => true,
                    'is_document_verified'  => true,
                    'wallet_amount'         => 0,
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ]);
                $this->command->info('✓ admin@plantixai.com created.');
            } else {
                DB::table('users')->where('email', 'admin@plantixai.com')->update([
                    'role'     => 'admin',
                    'role_id'  => null,        // ← reset any accidental role_id assignment
                    'active'   => true,
                    'password' => Hash::make('Admin@1234'),
                ]);
                $this->command->info('✓ admin@plantixai.com verified/corrected (role_id reset to NULL).');
            }

            // ── 5. Ensure secondary admin user is also correct ────────────────
            $adminUser2 = DB::table('users')->where('email', 'khalid.admin@plantixai.com')->first();
            if (!$adminUser2) {
                DB::table('users')->insert([
                    'name'                  => 'Khalid Raheem',
                    'email'                 => 'khalid.admin@plantixai.com',
                    'password'              => Hash::make('Admin@1234'),
                    'phone'                 => '+92-300-0000002',
                    'role'                  => 'admin',
                    'role_id'               => null,
                    'active'                => true,
                    'is_document_verified'  => true,
                    'wallet_amount'         => 0,
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ]);
            } else {
                DB::table('users')->where('email', 'khalid.admin@plantixai.com')->update([
                    'role'    => 'admin',
                    'role_id' => null,
                    'active'  => true,
                ]);
            }
            $this->command->info('✓ khalid.admin@plantixai.com verified (role_id=NULL).');

            // ── 6. Verify ──────────────────────────────────────────────────────
            $u = DB::table('users')->where('email', 'admin@plantixai.com')->first();
            $this->command->newLine();
            $this->command->info('── Verification ──────────────────────────────────');
            $this->command->info("  email    : {$u->email}");
            $this->command->info("  role     : {$u->role}");
            $this->command->info("  role_id  : " . ($u->role_id ?? 'NULL ← super-admin bypass active'));
            $this->command->info("  active   : " . ($u->active ? 'true' : 'false'));
            $this->command->info("  password : Admin@1234 (re-hashed)");
            $this->command->info('  → PermissionMiddleware will bypass ALL permission checks ✓');
            $this->command->newLine();
            $this->command->info('── Credentials ───────────────────────────────────');
            $this->command->info('  URL      : /admin/login');
            $this->command->info('  Email    : admin@plantixai.com');
            $this->command->info('  Password : Admin@1234');
            $this->command->info('─────────────────────────────────────────────────');
        });
    }
}
