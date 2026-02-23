<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Re-create zones with a clean schema that replaces the JSON coordinates column.
        // zone_points table stores each GeoPoint as an individual row.
        if (!Schema::hasTable('zones')) {
            Schema::create('zones', function (Blueprint $table) {
                $table->id();
                $table->string('zone_name')->unique();
                $table->string('description')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->string('firebase_doc_id', 191)->nullable();
                $table->timestamps();
            });
        } else {
            // Migrate existing zones table: swap JSON coordinates for status enum
            Schema::table('zones', function (Blueprint $table) {
                if (Schema::hasColumn('zones', 'coordinates')) {
                    $table->dropColumn('coordinates');
                }
                if (!Schema::hasColumn('zones', 'firebase_doc_id')) {
                    $table->string('firebase_doc_id', 191)->nullable()->after('status');
                }
                if (Schema::hasColumn('zones', 'status')) {
                    // status column already exists — okay
                }
            });
        }

        Schema::create('zone_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 8)->notNull();
            $table->decimal('longitude', 11, 8)->notNull();
            $table->integer('sort_order')->default(0);
            $table->index('zone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zone_points');
    }
};
