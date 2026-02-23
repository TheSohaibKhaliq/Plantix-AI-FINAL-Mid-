<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booked_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('booking_date')->notNull();
            $table->time('booking_time')->notNull();
            $table->integer('party_size')->notNull();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('firebase_doc_id', 191)->nullable();
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('user_id');
            $table->index('booking_date');
        });

        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('media_url', 500)->notNull();
            $table->enum('type', ['image', 'video'])->default('image');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('firebase_doc_id', 191)->nullable();
            $table->timestamps();

            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
        Schema::dropIfExists('booked_tables');
    }
};
