<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssignAllPermissionsToAdminSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ── 1. Create or get "Super Administrator" role ──────────────────────
        $superAdminRole = DB::table('role')->where('role_name', 'Super Administrator')->first();
        
        if (!$superAdminRole) {
            DB::table('role')->insert([
                'role_name' => 'Super Administrator',
                'guard'     => 'admin',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $superAdminRole = DB::table('role')->where('role_name', 'Super Administrator')->first();
        }

        echo "✓ Super Administrator role ID: {$superAdminRole->id}\n";

        // ── 2. Get ALL permissions from the permissions table ────────────────
        $allPermissions = DB::table('permissions')->pluck('id')->toArray();
        echo "✓ Found " . count($allPermissions) . " permissions\n";

        // ── 3. Assign all permissions to the Super Administrator role ───────
        foreach ($allPermissions as $permissionId) {
            DB::table('role_permissions')->insertOrIgnore([
                'role_id'       => $superAdminRole->id,
                'permission_id' => $permissionId,
            ]);
        }
        echo "✓ Assigned " . count($allPermissions) . " permissions to Super Administrator role\n";

        // ── 4. Assign the Super Administrator role to admin users ────────────
        $adminUsersUpdated = DB::table('users')
            ->where('role', 'admin')
            ->update(['role_id' => $superAdminRole->id]);

        echo "✓ Assigned Super Administrator role to {$adminUsersUpdated} admin user(s)\n";

        // ── 5. Verify ──────────────────────────────────────────────────────────
        $adminUser = DB::table('users')
            ->where('email', 'admin@plantixai.com')
            ->first();

        if ($adminUser) {
            $permCount = DB::table('role_permissions')
                ->where('role_id', $adminUser->role_id)
                ->count();

            echo "\n✓ Admin user verification:\n";
            echo "  - Name: {$adminUser->name}\n";
            echo "  - Email: {$adminUser->email}\n";
            echo "  - role_id: {$adminUser->role_id}\n";
            echo "  - Permissions assigned: {$permCount}\n";
        }
    }
}
