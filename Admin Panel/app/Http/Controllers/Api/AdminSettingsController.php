<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    /**
     * Get currency settings
     */
    public function currency(Request $request)
    {
        try {
            $setting = \DB::table('settings')->where('key', 'currency')->first();
            $currencyData = $setting ? json_decode($setting->value, true) : [];

            return response()->json([
                'success' => true,
                'data' => [
                    'symbol' => $currencyData['symbol'] ?? '$',
                    'symbolAtRight' => $currencyData['symbolAtRight'] ?? false,
                    'decimal_degits' => $currencyData['decimal_digits'] ?? 2,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching currency settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get placeholder image
     */
    public function placeholder(Request $request)
    {
        try {
            $setting = \DB::table('settings')->where('key', 'placeholder_image')->first();
            $image = $setting ? $setting->value : null;

            return response()->json([
                'success' => true,
                'data' => [
                    'image' => $image ?? asset('images/placeholder.png'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching placeholder image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get global settings
     */
    public function global(Request $request)
    {
        try {
            $settings = \DB::table('settings')->pluck('value', 'key');

            return response()->json([
                'success' => true,
                'data' => [
                    'application_name' => $settings['application_name'] ?? 'Plantix AI',
                    'meta_title' => $settings['meta_title'] ?? 'Plantix AI',
                    'favicon' => $settings['favicon'] ?? null,
                    'admin_panel_color' => $settings['admin_panel_color'] ?? '#2EC7D9',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching global settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment methods
     */
    public function paymentMethods(Request $request)
    {
        try {
            $paymentMethods = \DB::table('settings')
                ->where('key', 'like', '%_settings')
                ->pluck('value', 'key');

            return response()->json([
                'success' => true,
                'data' => [
                    'cod' => [
                        'is_enabled' => boolval(\DB::table('settings')->where('key', 'cod_enabled')->value('value') ?? false),
                    ],
                    'stripe' => [
                        'is_enabled' => boolval(\DB::table('settings')->where('key', 'stripe_enabled')->value('value') ?? false),
                    ],
                    'paypal' => [
                        'is_enabled' => boolval(\DB::table('settings')->where('key', 'paypal_enabled')->value('value') ?? false),
                    ],
                    'razorpay' => [
                        'is_enabled' => boolval(\DB::table('settings')->where('key', 'razorpay_enabled')->value('value') ?? false),
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching payment methods: ' . $e->getMessage()
            ], 500);
        }
    }
}
