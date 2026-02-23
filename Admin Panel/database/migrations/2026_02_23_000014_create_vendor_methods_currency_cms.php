<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Withdraw method configurations per vendor
        // Replaces Firestore embedded objects (stripe, paypal, razorpay, flutterwave keys)
        Schema::create('vendor_withdraw_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('method', 50)->notNull();  // stripe, paypal, razorpay, flutterwave
            $table->json('credentials')->notNull();   // encrypted key/value pairs
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['vendor_id', 'method']);
            $table->index('vendor_id');
        });

        // Currencies
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->string('symbol', 10);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->decimal('exchange_rate', 12, 6)->default(1.000000);
            $table->string('firebase_doc_id', 191)->nullable();
            $table->timestamps();
        });

        // CMS pages (privacy, terms, etc.)
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Payout requests (vendors request payouts to admin)
        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->notNull();
            $table->string('method', 50)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('firebase_doc_id', 191)->nullable();
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('vendor_withdraw_methods');
    }
};
