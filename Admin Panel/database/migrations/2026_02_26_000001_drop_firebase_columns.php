<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop all firebase_uid and firebase_doc_id columns.
     * Firebase has been removed from the application.
     */
    public function up(): void
    {
        $tables = [
            'users'                  => ['firebase_uid'],
            'categories'             => ['firebase_doc_id'],
            'zones'                  => ['firebase_doc_id'],
            'vendors'                => ['firebase_doc_id'],
            'products'               => ['firebase_doc_id'],
            'coupons'                => ['firebase_doc_id'],
            'taxes'                  => ['firebase_doc_id'],
            'orders'                 => ['firebase_doc_id'],
            'payouts'                => ['firebase_doc_id'],
            'wallet_transactions'    => ['firebase_doc_id'],
            'reviews'                => ['firebase_doc_id'],
            'bookmarked_items'       => ['firebase_doc_id'],
            'booked_tables'          => ['firebase_doc_id'],
            'stories'                => ['firebase_doc_id'],
            'document_verifications' => ['firebase_doc_id'],
            'document_types'         => ['firebase_doc_id'],
            'gift_cards'             => ['firebase_doc_id'],
            'store_filters'          => ['firebase_doc_id'],
            'settings'               => ['firebase_doc_id'],
            'payout_requests'        => ['firebase_doc_id'],
            'vendor_withdraw_methods' => ['firebase_doc_id'],
            'notifications'          => ['firebase_doc_id'],
        ];

        foreach ($tables as $table => $columns) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($columns) {
                    foreach ($columns as $col) {
                        if (Schema::hasColumn($t->getTable(), $col)) {
                            $t->dropColumn($col);
                        }
                    }
                });
            }
        }
    }

    /**
     * Don't restore firebase columns on rollback.
     */
    public function down(): void
    {
        // Intentionally left empty - firebase removal is permanent
    }
};
