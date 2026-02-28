<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Surgical cleanup migration — drops all tables that are not part of the
 * Plantix AI required feature set.
 *
 * Removed feature domains:
 *  - Restaurant / Food delivery (booked_tables, stories)
 *  - Delivery zones  (zones, zone_areas, zone_points)
 *  - Driver management (drivers table removed via user role cleanup)
 *  - Wallet / payouts  (payouts, payout_requests, wallet_transactions, vendor_withdraw_methods)
 *  - Gift cards        (gift_cards)
 *  - Store filters     (store_filters)
 *  - Brands            (brands)
 *  - GPS / locations   (user_locations)
 *  - Document verif.   (document_types, document_verifications, vendor_documents)
 *  - Multi-currency    (currencies)
 *  - CMS / languages   (cms_pages, languages, language_lines)
 *  - On-board screens  (on_boards)
 *  - Banners / menus   (menu_items)
 *  - Tax               (taxes)
 *  - Review Attributes (review_attributes)
 *  - Favourites        (favourites)
 *  - Firebase push     (real_time_notifications)
 */
return new class extends Migration
{
    // Tables to remove — ordered so FK constraints don't block drops
    private array $tables = [
        // Restaurant / dine-in
        'booked_tables',
        'stories',

        // Zones / delivery
        'zone_points',
        'zone_areas',
        'zones',

        // Wallet & payouts
        'wallet_transactions',
        'payout_requests',
        'payouts',
        'vendor_withdraw_methods',

        // Gift cards
        'gift_cards',

        // Store filters
        'store_filters',

        // Brands
        'brands',

        // GPS
        'user_locations',

        // Document verification
        'vendor_documents',
        'document_verifications',
        'document_types',

        // Currency
        'currencies',

        // CMS & language
        'cms_pages',
        'language_lines',
        'languages',

        // On-board
        'on_boards',

        // Banners
        'menu_items',

        // Tax
        'taxes',

        // Review attributes
        'review_attributes',

        // Favourites (not in requirements)
        'favourites',

        // Firebase push notifications
        'real_time_notifications',
    ];

    public function up(): void
    {
        // Disable FK checks for a clean drop sequence
        Schema::disableForeignKeyConstraints();

        foreach ($this->tables as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Down is intentionally left empty — these tables contained
        // non-required features and should not be recreated.
        // Restore from a pre-cleanup DB snapshot if needed.
    }
};
