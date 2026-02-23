<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add columns if they don't already exist
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'vendor', 'driver', 'user'])
                      ->default('user')
                      ->after('phone')
                      ->index();
            }
            if (!Schema::hasColumn('users', 'active')) {
                $table->boolean('active')->default(true)->after('role');
            }
            if (!Schema::hasColumn('users', 'is_document_verified')) {
                $table->boolean('is_document_verified')->default(false)->after('active');
            }
            if (!Schema::hasColumn('users', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('is_document_verified')->index();
            }
            if (!Schema::hasColumn('users', 'wallet_amount')) {
                $table->decimal('wallet_amount', 12, 2)->default(0.00)->after('vendor_id');
            }
            if (!Schema::hasColumn('users', 'fcm_token')) {
                $table->string('fcm_token', 500)->nullable()->after('wallet_amount');
            }
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo', 500)->nullable()->after('fcm_token');
            }
            if (!Schema::hasColumn('users', 'firebase_uid')) {
                $table->string('firebase_uid', 128)->nullable()->unique()->after('profile_photo');
            }
            if (!Schema::hasColumn('users', 'must_reset_password')) {
                $table->boolean('must_reset_password')->default(false)->after('firebase_uid');
            }
            if (!Schema::hasColumn('users', 'role_id')) {
                // role_id for admin panel granular roles — nullable, only used for admin users
                $table->unsignedBigInteger('role_id')->nullable()->after('must_reset_password')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'role', 'active', 'is_document_verified',
                'vendor_id', 'wallet_amount', 'fcm_token', 'profile_photo',
                'firebase_uid', 'must_reset_password', 'role_id',
            ]);
        });
    }
};
