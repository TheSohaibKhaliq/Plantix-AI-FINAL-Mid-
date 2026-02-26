<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Experts ──────────────────────────────────────────────────────────
        Schema::create('experts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('specialty')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar', 500)->nullable();
            $table->boolean('is_available')->default(true);
            $table->decimal('hourly_rate', 8, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('is_available');
        });

        // ── Appointments ─────────────────────────────────────────────────────
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('expert_id')->nullable()->constrained('experts')->nullOnDelete();
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->decimal('fee', 8, 2)->default(0.00);
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('expert_id');
            $table->index('status');
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('experts');
    }
};
