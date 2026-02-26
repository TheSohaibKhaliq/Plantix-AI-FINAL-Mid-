<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Order Status History ─────────────────────────────────────────────
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('status', 60);
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('order_id');
            $table->index('created_at');
        });

        // ── Stocks ──────────────────────────────────────────────────────────
        // Extend products with stock tracking (separate table for multi-variant)
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->string('sku', 100)->nullable()->unique();
            $table->timestamps();

            $table->index(['product_id', 'vendor_id']);
            $table->index('quantity');
        });

        // ── Product Images ───────────────────────────────────────────────────
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('path', 500);
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('product_id');
        });

        // ── Brands ──────────────────────────────────────────────────────────
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug', 191)->unique()->nullable();
            $table->string('logo', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Add brand_id to products
        if (!Schema::hasColumn('products', 'brand_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('brand_id')->nullable()->after('category_id')
                      ->constrained('brands')->nullOnDelete();
                $table->string('slug', 191)->nullable()->after('name');
                $table->integer('stock_quantity')->default(0)->after('discount_price');
                $table->boolean('track_stock')->default(true)->after('stock_quantity');
                $table->index('brand_id');
            });
        }

        // ── Cart ─────────────────────────────────────────────────────────────
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_id', 191)->nullable()->index();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2); // price snapshot
            $table->json('addons')->nullable();
            $table->timestamps();

            $table->index('cart_id');
            $table->index('product_id');
        });

        // ── Forum Posts ──────────────────────────────────────────────────────
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 191)->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('forum_category_id')->nullable()->constrained('forum_categories')->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('forum_category_id');
            $table->index('created_at');
        });

        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('forum_threads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('thread_id');
            $table->index('user_id');
        });

        // ── Wishlist ─────────────────────────────────────────────────────────
        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->primary(['user_id', 'product_id']);
                $table->timestamps();
            });
        }

        // ── Addresses ────────────────────────────────────────────────────────
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('label', 60)->default('Home'); // Home, Work, Other
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city', 100);
            $table->string('state', 100)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('country', 60)->default('Pakistan');
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_threads');
        Schema::dropIfExists('forum_categories');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['brand_id', 'slug', 'stock_quantity', 'track_stock']);
        });
        Schema::dropIfExists('brands');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_stocks');
        Schema::dropIfExists('order_status_history');
    }
};
