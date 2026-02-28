<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // First, change status column to string to allow updating values outside the old enum
            $table->string('status')->change();
        });

        // Convert old food-delivery statuses to new e-commerce statuses
        DB::table('orders')->where('status', 'accepted')->update(['status' => 'confirmed']);
        DB::table('orders')->where('status', 'preparing')->update(['status' => 'processing']);
        DB::table('orders')->where('status', 'ready')->update(['status' => 'processing']);
        DB::table('orders')->where('status', 'driver_assigned')->update(['status' => 'shipped']);
        DB::table('orders')->where('status', 'picked_up')->update(['status' => 'shipped']);

        Schema::table('orders', function (Blueprint $table) {
            // Timestamp when the order was marked as delivered
            // Required for the return-window enforcement (PLANTIX_RETURN_WINDOW_DAYS)
            $table->timestamp('delivered_at')->nullable()->after('estimated_delivery');

            // Now change status back to enum with new e-commerce states
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
                'rejected',
                'return_requested',
                'returned',
            ])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivered_at');

            $table->enum('status', [
                'pending', 'accepted', 'preparing', 'ready',
                'driver_assigned', 'picked_up', 'delivered', 'rejected', 'cancelled',
            ])->default('pending')->change();
        });
    }
};
