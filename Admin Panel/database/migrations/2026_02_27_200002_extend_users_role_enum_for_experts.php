<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Extend users.role enum to include expert roles.
 *
 * MySQL enum values are additive — existing data is unaffected.
 */
return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires listing ALL enum values when modifying the column
        DB::statement("
            ALTER TABLE `users`
            MODIFY COLUMN `role`
            ENUM('admin','vendor','driver','user','expert','agency_expert')
            NOT NULL DEFAULT 'user'
        ");
    }

    public function down(): void
    {
        // Revert — rows with expert/agency_expert role must be removed first
        DB::statement("
            ALTER TABLE `users`
            MODIFY COLUMN `role`
            ENUM('admin','vendor','driver','user')
            NOT NULL DEFAULT 'user'
        ");
    }
};
