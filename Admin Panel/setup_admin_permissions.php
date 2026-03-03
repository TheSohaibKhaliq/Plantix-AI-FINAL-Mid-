<?php
/**
 * setup_admin_permissions.php — Plantix AI Admin Panel
 *
 * Creates or updates admin@plantixai.com with password 12345678
 * and assigns all permissions to the user.
 * 
 * Run via:  php setup_admin_permissions.php
 */
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $email = 'admin@plantixai.com';
    $password = '12345678';
    
    // Check if user exists, if not create it
    $user = \App\Models\User::where('email', $email)->first();
    
    if (!$user) {
        echo "Creating new user: $email\n";
        $user = \App\Models\User::create([
            'name'     => 'Admin',
            'email'    => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role'     => 'admin',
            'role_id'  => null, // Super admin - no restrictions
            'active'   => true,
        ]);
        echo "✓ User created: $email\n";
    } else {
        echo "User already exists: $email\n";
    }
    
    // Update password
    echo "Setting password to: $password\n";
    $user->update([
        'password' => \Illuminate\Support\Facades\Hash::make($password),
    ]);
    echo "✓ Password updated\n";
    
    // Get all permissions
    $allPermissions = \App\Models\Permission::pluck('id')->toArray();
    
    if (empty($allPermissions)) {
        echo "⚠ No permissions found in the database!\n";
    } else {
        echo "\nFound " . count($allPermissions) . " total permissions\n";
        
        // Assign all permissions to the user
        // Check if there's a user_permissions table
        $permissionCount = 0;
        if (\Illuminate\Support\Facades\Schema::hasTable('user_permissions')) {
            // Clear existing permissions first
            \App\Models\User::find($user->id)->permissions()->detach();
            
            // Assign all permissions
            \App\Models\User::find($user->id)->permissions()->attach($allPermissions);
            $permissionCount = count($allPermissions);
            echo "✓ Assigned " . $permissionCount . " permissions directly to user\n";
        } else {
            // If no user_permissions table, try assigning via role
            $adminRole = \App\Models\Role::where('slug', 'admin')->orWhere('role_name', 'admin')->first();
            
            if ($adminRole) {
                // Clear existing permissions from role
                $adminRole->permissions()->detach();
                
                // Assign all permissions to the admin role
                $adminRole->permissions()->attach($allPermissions);
                $permissionCount = count($allPermissions);
                
                // Set user's role_id to admin role
                $user->update(['role_id' => $adminRole->id]);
                echo "✓ Assigned " . $permissionCount . " permissions to admin role\n";
                echo "✓ User assigned to admin role (role_id=" . $adminRole->id . ")\n";
            } else {
                // Create admin role if it doesn't exist
                echo "Creating admin role...\n";
                $adminRole = \App\Models\Role::create([
                    'role_name' => 'admin',
                    'slug'      => 'admin',
                    'guard'     => 'web',
                    'is_active' => true,
                ]);
                
                // Assign all permissions to the new admin role
                $adminRole->permissions()->attach($allPermissions);
                $permissionCount = count($allPermissions);
                
                // Set user's role to admin
                $user->update(['role_id' => $adminRole->id]);
                echo "✓ Admin role created\n";
                echo "✓ Assigned " . $permissionCount . " permissions to admin role\n";
                echo "✓ User assigned to admin role (role_id=" . $adminRole->id . ")\n";
            }
        }
    }
    
    echo "\n=== Admin Setup Complete ===\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
    echo "Role: admin (unrestricted)\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
