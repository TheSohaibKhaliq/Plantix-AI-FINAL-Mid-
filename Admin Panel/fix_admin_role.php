<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Clear role_id for all admin users (super-admins should have NULL role_id)
    $updated = \App\Models\User::where('role', 'admin')->update(['role_id' => null]);
    echo "Updated $updated admin users: role_id set to NULL\n";
    
    // Verify the change
    $admin = \App\Models\User::where('role', 'admin')->first();
    echo "\nAdmin user after fix:\n";
    echo "- Name: " . $admin->name . "\n";
    echo "- Email: " . $admin->email . "\n";
    echo "- role_id: " . ($admin->role_id === null ? 'null (CORRECT)' : $admin->role_id) . "\n";
    echo "- active: " . ($admin->active ? 'yes' : 'no') . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
