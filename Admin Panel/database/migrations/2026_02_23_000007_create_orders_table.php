<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->enum('status', [
                'pending', 'accepted', 'preparing', 'ready',
                'driver_assigned', 'picked_up', 'delivered', 'rejected', 'cancelled',
            ])->default('pending');
            $table->decimal('subtotal', 10, 2)->notNull();
            $table->decimal('delivery_fee', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->notNull();
            $table->string('payment_method', 50)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->text('delivery_address')->nullable();
            $table->decimal('delivery_lat', 10, 8)->nullable();
            $table->decimal('delivery_lng', 11, 8)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('estimated_delivery')->nullable();
            $table->string('firebase_doc_id', 191)->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('vendor_id');
            $table->index('driver_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
            $table->index(['vendor_id', 'status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->string('product_name');      // snapshot at time of order
            $table->integer('quantity')->notNull();
            $table->decimal('unit_price', 10, 2)->notNull();
            $table->decimal('total_price', 10, 2)->notNull();
            $table->json('addons')->nullable();  // snapshotted add-on selections
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
