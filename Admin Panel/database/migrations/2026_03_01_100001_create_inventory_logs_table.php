<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();

            // What changed
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();

            // Why it changed (nullable — manual adjustments have no order)
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('return_id')->nullable()->index();  // soft-ref, return_requests table
            $table->unsignedBigInteger('initiated_by')->nullable()->index(); // user_id

            // Type of mutation
            $table->enum('type', [
                'sale',       // stock decremented after checkout
                'restock',    // stock increased by vendor/admin
                'cancel',     // stock restored after order cancel/reject
                'return',     // stock restored after return approval
                'manual',     // manual admin adjustment
                'adjustment', // correction/reconciliation
            ]);

            // Snapshot quantities (denormalised for audit integrity)
            $table->integer('quantity_before');
            $table->integer('quantity_change'); // negative = reduced, positive = increased
            $table->integer('quantity_after');

            $table->string('notes', 500)->nullable();

            $table->timestamp('created_at')->useCurrent();
            // No updated_at — logs are immutable

            // Indexes for fast lookups
            $table->index(['product_id', 'created_at']);
            $table->index('order_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
