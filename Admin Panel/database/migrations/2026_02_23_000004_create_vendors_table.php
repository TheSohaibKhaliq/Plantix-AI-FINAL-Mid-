<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('image', 500)->nullable();
            $table->string('cover_photo', 500)->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('review_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->decimal('delivery_fee', 10, 2)->default(0.00);
            $table->decimal('min_order_amount', 10, 2)->default(0.00);
            $table->integer('preparation_time')->nullable()->comment('minutes');
            $table->decimal('commission_rate', 5, 2)->default(0.00);
            $table->string('stripe_account_id')->nullable();
            $table->string('firebase_doc_id', 191)->nullable();
            $table->timestamps();

            $table->index('author_id');
            $table->index('zone_id');
            $table->index('is_approved');
            $table->index(['latitude', 'longitude']);
            $table->fullText(['title', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
