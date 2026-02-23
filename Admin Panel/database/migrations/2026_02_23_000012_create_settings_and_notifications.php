<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // settings: replaces the Firestore 'settings' document
        // Each Firestore top-level key becomes a row
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 191)->unique();
            $table->longText('value')->nullable();
            $table->enum('type', ['string', 'json', 'boolean', 'integer'])->default('string');
            $table->timestamps();

            $table->index('key');
        });

        // notifications: Laravel standard notifications table (replaces Firestore notifications)
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // dynamic_notifications: push notification templates (admin configurable)
        Schema::create('dynamic_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('type', 50)->default('general');       // general, order, promo
            $table->string('target_role')->nullable();             // null = all roles
            $table->boolean('is_active')->default(true);
            $table->string('firebase_doc_id', 191)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_notifications');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('settings');
    }
};
