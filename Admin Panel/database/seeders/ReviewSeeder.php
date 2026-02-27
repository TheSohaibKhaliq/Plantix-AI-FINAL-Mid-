<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Only review orders with status "delivered" — matches real usage
        $orders = DB::table('orders')
            ->where('status', 'delivered')
            ->get(['id', 'user_id', 'vendor_id']);

        if ($orders->isEmpty()) {
            // Fallback: include all orders if no delivered ones exist yet
            $orders = DB::table('orders')->get(['id', 'user_id', 'vendor_id']);
        }

        if ($orders->isEmpty()) {
            $this->command->warn('No orders found. Run OrdersSeeder first.');
            return;
        }

        $now = Carbon::now();

        // Realistic agricultural product review comments (Urdu/English mix)
        $positiveComments = [
            // Seeds reviews
            'Super Basmati seeds ki germination rate zabardast thi. 95% se zyada sprouting in first week. Very happy with this purchase.',
            'AARI-2011 wheat variety ne is season excellent yield diya. 60+ mann per acre. Highly recommend for Punjab zone.',
            'Fauji Hybrid Maize seed quality aur packaging dono behtareen. Dealer delivered on time bhi. 5 stars deservingly.',
            'Cotton seeds phool bahut achi aaye. Kheti ki shuraat se hi plants healthy dikhay. Koi disease nahi aaya pehle 6 hafton tak.',
            // Fertilizer reviews
            'Engro Urea ka quality consistent hai. Granule size uniform aur no caking issue. Delivery condition bhi perfect thi.',
            'DAP fertilizer applied at sowing stage — wheat plants showed strong root development visible after 2 weeks. Quality product.',
            'SOP fertilizer ne cotton boll retention improve kiya is saal. Comparison kari thi last year ki same field se — clear difference.',
            'FFC fertilizer aur Engro ka jo bhi main comparison karta hoon, Engro ki quality superior lagti hai consistently.',
            // Pesticide reviews
            'Tilt 250EC spot on for wheat rust control. Applied at first sign and disease stopped within 7 days. Genuine product.',
            'Confidor 200SL for whitefly worked well in first application. Gave 3-week protection easily.',
            'Spray machine bhi liya saath mein — both products quality checked and confirmed. Value for money.',
            'Syngenta products are always consistent. Never had counterfeit issue from this vendor.',
            // General
            'Packaging was excellent, bottle sealed properly, no leakage. Delivery was 2 days faster than expected.',
            'Price is slightly higher than local market but quality is guaranteed original here. Worth the premium.',
            'Second order already placed. Pehle order ki delivery aur quality dono perfect thi.',
            'Is vendor se pehli dafa order kiya — impressed with professionalism. Will order again next season.',
        ];

        $moderateComments = [
            'Product quality theek thi but delivery mein 2 din zyada lage. Overall kaam aya.',
            'Seed germination good thi but packaging thori damaged aayi. Product itself genuine laga.',
            'Fertilizer ka quality okay hai but price thora ziada lagta hai compared to local dealer.',
            'Acha product hai. Delivery on time thi. Bas quantity wali bag thori choti lagi.',
            'First time order kiya. Quality theek rahi. Aagla experience ka wait karunga before 5 stars.',
            'Pesticide ne kaam kiya but 2 sprays lagay ek mein problem solve karne ke liye.',
        ];

        $negativeComments = [
            'Seed germination rate sirf 60% rahi. Expected much better quality from premium brand.',
            'Fertilizer bag partially wet when received. Quality might have been compromised.',
            'Wrong product delivered initially. After complaint, correct item came after 5 days. Inconvenienced.',
        ];

        // Realistic rating distribution (positively skewed — satisfied farmers review more)
        $ratingPool = [5, 5, 5, 5, 4, 4, 4, 4, 3, 3, 2, 1]; // weighted

        $reviews = [];
        $seenPairs = []; // Track unique (user_id, order_id) to respect constraint

        foreach ($orders as $order) {
            $pairKey = $order->user_id . '_' . $order->id;
            if (isset($seenPairs[$pairKey])) {
                continue; // Skip duplicate
            }
            $seenPairs[$pairKey] = true;

            // ~80% of delivered orders get reviewed
            if (rand(1, 10) > 8) {
                continue;
            }

            $rating = $ratingPool[array_rand($ratingPool)];
            $comment = match (true) {
                $rating >= 4 => $positiveComments[array_rand($positiveComments)],
                $rating == 3 => $moderateComments[array_rand($moderateComments)],
                default      => $negativeComments[array_rand($negativeComments)],
            };

            // Try to get a product from this order for product-specific review
            $orderItem = DB::table('order_items')->where('order_id', $order->id)->first(['product_id']);
            $productId = $orderItem ? $orderItem->product_id : null;

            $reviews[] = [
                'user_id'    => $order->user_id,
                'vendor_id'  => $order->vendor_id,
                'product_id' => $productId,
                'order_id'   => $order->id,
                'rating'     => $rating,
                'comment'    => $comment,
                'is_active'  => true,
                'created_at' => Carbon::now()->subDays(rand(1, 60)),
                'updated_at' => $now,
            ];
        }

        if (empty($reviews)) {
            $this->command->warn('No reviews to insert (no eligible orders found).');
            return;
        }

        foreach (array_chunk($reviews, 100) as $chunk) {
            DB::table('reviews')->insertOrIgnore($chunk);
        }

        $this->command->info('ReviewSeeder: ' . count($reviews) . ' reviews seeded across ' . $orders->count() . ' eligible orders.');
    }
}
