<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdersSeeder extends Seeder
{
    public function run(): void
    {
        $now      = Carbon::now();
        $customers = DB::table('users')->where('role', 'user')->pluck('id')->toArray();
        $drivers  = DB::table('users')->where('role', 'driver')->pluck('id')->toArray();
        $vendors  = DB::table('vendors')->get();
        $coupons  = DB::table('coupons')->pluck('id')->toArray();

        $statuses   = ['pending', 'accepted', 'preparing', 'ready', 'driver_assigned', 'picked_up', 'delivered', 'rejected', 'cancelled'];
        $payMethods = ['cash_on_delivery', 'easypaisa', 'jazzcash', 'bank_transfer', 'wallet'];
        $payStatuses = ['pending', 'paid', 'refunded', 'failed'];

        $addresses = [
            'House 12, Street 4, Model Town, Lahore',
            'Flat 3B, Al-Habib Apartments, Gulshan-e-Iqbal, Karachi',
            'Chak 45/RB, District Faisalabad',
            'Near Grain Market, Gujranwala',
            'Village Alipur, Tehsil Mailsi, Vehari',
            'Mohallah Hussain Agahi, Multan',
            'House 88, Satellite Town, Rawalpindi',
            'Chak 120/9-R, Sahiwal',
            'Near Cantt Board Office, Peshawar',
            'Khasra No. 456, Mouza Khanpur, Bahawalpur',
            'Ibrahim Hydari Colony, Karachi',
            'Qasimabad, Hyderabad, Sindh',
        ];

        $orderCount = 0;

        foreach ($vendors as $vendor) {
            $products = DB::table('products')
                ->where('vendor_id', $vendor->id)
                ->get()
                ->toArray();

            if (count($products) === 0) continue;

            // Generate 3–5 orders per vendor
            $numOrders = rand(3, 5);
            for ($i = 0; $i < $numOrders; $i++) {
                $status      = $statuses[array_rand($statuses)];
                $payMethod   = $payMethods[array_rand($payMethods)];
                $payStatus   = in_array($status, ['delivered', 'picked_up']) ? 'paid' : $payStatuses[array_rand($payStatuses)];
                $customerId  = $customers[array_rand($customers)];
                $driverId    = in_array($status, ['driver_assigned', 'picked_up', 'delivered']) ? $drivers[array_rand($drivers)] : null;
                $couponId    = rand(0, 3) === 0 ? $coupons[array_rand($coupons)] : null;
                $address     = $addresses[array_rand($addresses)];
                $orderDate   = $now->copy()->subDays(rand(0, 60))->subHours(rand(0, 23));

                // Pick 1–3 random products for this order
                $pickedProducts = collect($products)->shuffle()->take(rand(1, min(3, count($products))))->toArray();

                $subtotal      = 0.0;
                $itemsPayload  = [];

                foreach ($pickedProducts as $prod) {
                    $qty       = rand(1, 5);
                    $unitPrice = (float) $prod->price;
                    $lineTotal = $unitPrice * $qty;
                    $subtotal += $lineTotal;

                    $itemsPayload[] = [
                        'product_id'   => $prod->id,
                        'product_name' => $prod->name,
                        'quantity'     => $qty,
                        'unit_price'   => $unitPrice,
                        'total_price'  => $lineTotal,
                    ];
                }

                $deliveryFee    = (float) $vendor->delivery_fee;
                $taxAmount      = round($subtotal * 0.05, 2);         // 5% agri tax
                $discountAmount = $couponId ? round($subtotal * 0.10, 2) : 0.00;
                $total          = round($subtotal + $deliveryFee + $taxAmount - $discountAmount, 2);

                $orderNumber = 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8));

                $orderId = DB::table('orders')->insertGetId([
                    'order_number'      => $orderNumber,
                    'user_id'           => $customerId,
                    'vendor_id'         => $vendor->id,
                    'driver_id'         => $driverId,
                    'coupon_id'         => $couponId,
                    'status'            => $status,
                    'subtotal'          => $subtotal,
                    'delivery_fee'      => $deliveryFee,
                    'tax_amount'        => $taxAmount,
                    'discount_amount'   => $discountAmount,
                    'total'             => $total,
                    'payment_method'    => $payMethod,
                    'payment_status'    => $payStatus,
                    'delivery_address'  => $address,
                    'delivery_lat'      => (float) $vendor->latitude + (rand(-100, 100) / 10000),
                    'delivery_lng'      => (float) $vendor->longitude + (rand(-100, 100) / 10000),
                    'notes'             => rand(0, 2) === 0 ? 'Please deliver before noon.' : null,
                    'estimated_delivery'=> $orderDate->copy()->addHours(rand(1, 48)),
                    'created_at'        => $orderDate,
                    'updated_at'        => $orderDate,
                ]);

                foreach ($itemsPayload as $item) {
                    DB::table('order_items')->insert([
                        'order_id'     => $orderId,
                        'product_id'   => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'quantity'     => $item['quantity'],
                        'unit_price'   => $item['unit_price'],
                        'total_price'  => $item['total_price'],
                        'addons'       => null,
                        'created_at'   => $orderDate,
                        'updated_at'   => $orderDate,
                    ]);
                }

                $orderCount++;
            }
        }

        $this->command->info('OrdersSeeder: ' . $orderCount . ' orders with ' . DB::table('order_items')->count() . ' items inserted.');
    }
}
