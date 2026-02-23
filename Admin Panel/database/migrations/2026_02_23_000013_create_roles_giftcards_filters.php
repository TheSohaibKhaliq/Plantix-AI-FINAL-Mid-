<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // roles: admin-panel granular role definitions
        if (!Schema::hasTable('role')) {
            Schema::create('role', function (Blueprint $table) {
                $table->id();
                $table->string('role_name')->unique();
                $table->string('guard', 50)->default('web');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            Schema::table('role', function (Blueprint $table) {
                if (!Schema::hasColumn('role', 'guard')) {
                    $table->string('guard', 50)->default('web')->after('role_name');
                }
                if (!Schema::hasColumn('role', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('guard');
                }
            });
        }

        // permissions table
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name');          // e.g., 'stores.edit'
                $table->string('group');         // e.g., 'stores'
                $table->string('display_name')->nullable();
                $table->timestamps();

                $table->unique(['name']);
            });
        }

        // role_permissions pivot
        if (!Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained('role')->cascadeOnDelete();
                $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
                $table->primary(['role_id', 'permission_id']);
            });
        }

        // gift_cards
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 60)->unique();
            $table->decimal('amount', 10, 2)->notNull();
            $table->decimal('remaining_amount', 10, 2)->notNull();
            $table->foreignId('purchased_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('redeemed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('firebase_doc_id', 191)->nullable();
            $table->timestamps();
        });

        // on-boarding slides
        Schema::create('on_board_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('firebase_doc_id', 191)->nullable();
            $table->timestamps();
        });

        // store_filters
        Schema::create('store_filters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('firebase_doc_id', 191)->nullable();
            $table->timestamps();
        });

        // vendor_store_filters pivot
        Schema::create('vendor_store_filters', function (Blueprint $table) {
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('store_filter_id')->constrained('store_filters')->cascadeOnDelete();
            $table->primary(['vendor_id', 'store_filter_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_store_filters');
        Schema::dropIfExists('store_filters');
        Schema::dropIfExists('on_board_slides');
        Schema::dropIfExists('gift_cards');
    }
};
