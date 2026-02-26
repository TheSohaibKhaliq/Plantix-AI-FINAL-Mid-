<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Return Reasons ───────────────────────────────────────────────────
        Schema::create('return_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Returns ──────────────────────────────────────────────────────────
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('return_reason_id')->nullable()->constrained('return_reasons')->nullOnDelete();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'refunded'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->json('images')->nullable(); // uploaded proof images
            $table->timestamps();
            $table->softDeletes();

            $table->index('order_id');
            $table->index('user_id');
            $table->index('status');
        });

        // ── Refunds ──────────────────────────────────────────────────────────
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->notNull();
            $table->enum('method', ['wallet', 'original_payment', 'bank_transfer'])->default('wallet');
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->string('transaction_ref', 191)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('return_id');
            $table->index('order_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('returns');
        Schema::dropIfExists('return_reasons');
    }
};
