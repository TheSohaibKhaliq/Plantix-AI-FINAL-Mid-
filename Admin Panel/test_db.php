<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $adminCount = \App\Models\User::where('role', 'admin')->count();
    $permCount = \Illuminate\Support\Facades\DB::table('permissions')->count();
    $roleCount = \Illuminate\Support\Facades\DB::table('role')->count();
    
    echo "Admin users: $adminCount\n";
    echo "Permissions: $permCount\n";
    echo "Roles: $roleCount\n";
    
    if ($adminCount > 0) {
        $admin = \App\Models\User::where('role', 'admin')->first();
        echo "\nAdmin user: " . $admin->name . " (" . $admin->email . ")\n";
        echo "- role_id: " . ($admin->role_id ?? 'null') . "\n";
        echo "- active: " . ($admin->active ? 'yes' : 'no') . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
