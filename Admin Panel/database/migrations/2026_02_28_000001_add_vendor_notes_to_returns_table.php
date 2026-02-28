<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the vendor_notes column to the returns table so vendors can
 * annotate return requests before the admin makes a final decision.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->text('vendor_notes')->nullable()->after('admin_notes');
        });
    }

    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn('vendor_notes');
        });
    }
};
