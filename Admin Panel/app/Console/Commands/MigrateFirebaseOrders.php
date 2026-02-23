<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * php artisan firebase:import-orders
 *
 * Reads firebase_export/orders.json and inserts records into `orders` + `order_items`.
 *
 * Run AFTER: firebase:import-users, firebase:import-vendors
 */
class MigrateFirebaseOrders extends Command
{
    protected $signature   = 'firebase:import-orders
                                {--file=firebase_export/orders.json}
                                {--dry-run}';

    protected $description = 'Import orders from Firebase export JSON into MySQL';

    private array $statusMap = [
        'placed'       => 'placed',
        'accepted'     => 'accepted',
        'cooking'      => 'cooking',
        'ready'        => 'ready',
        'on_the_way'   => 'on_the_way',
        'delivered'    => 'delivered',
        'cancelled'    => 'cancelled',
        'rejected'     => 'rejected',
        // Firebase variants
        'Order Placed'    => 'placed',
        'Order Accepted'  => 'accepted',
        'Order Rejected'  => 'rejected',
        'Order Completed' => 'delivered',
    ];

    public function handle(): int
    {
        $file = base_path($this->option('file'));

        if (!file_exists($file)) {
            $this->error("File not found: $file  —  Run: node export_firebase.js");
            return self::FAILURE;
        }

        $orders  = json_decode(file_get_contents($file), true);
        $dryRun  = $this->option('dry-run');
        $count   = 0; $skipped = 0; $errors = 0;

        $this->info('Importing ' . count($orders) . ' orders' . ($dryRun ? ' [DRY RUN]' : '') . '...');
        $bar = $this->output->createProgressBar(count($orders));
        $bar->start();

        foreach ($orders as $doc) {
            $firebaseId = $doc['_id'];

            if (Order::where('firebase_doc_id', $firebaseId)->exists()) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $user   = User::where('firebase_uid',   $doc['user_id']   ?? null)->first();
            $vendor = Vendor::where('firebase_doc_id', $doc['vendor_id'] ?? null)->first();

            if (!$user || !$vendor) {
                $this->newLine();
                $this->warn("Skipping order $firebaseId: user or vendor not found.");
                $errors++;
                $bar->advance();
                continue;
            }

            $coupon = !empty($doc['coupon_id'])
                ? Coupon::where('firebase_doc_id', $doc['coupon_id'])->first()
                : null;

            $status = $this->statusMap[$doc['status'] ?? 'placed'] ?? 'placed';

            if (!$dryRun) {
                DB::transaction(function () use ($doc, $user, $vendor, $coupon, $status, $firebaseId) {
                    $order = Order::create([
                        'firebase_doc_id'        => $firebaseId,
                        'order_number'           => $doc['order_number'] ?? strtoupper(Str::random(8)),
                        'user_id'                => $user->id,
                        'vendor_id'              => $vendor->id,
                        'driver_id'              => null,
                        'coupon_id'              => $coupon?->id,
                        'status'                 => $status,
                        'sub_total'              => $doc['sub_total']    ?? $doc['total']   ?? 0,
                        'discount_amount'        => $doc['discount']     ?? 0,
                        'delivery_charge'        => $doc['delivery_fee'] ?? $doc['delivery_charge'] ?? 0,
                        'tax_amount'             => $doc['tax']          ?? 0,
                        'total_amount'           => $doc['total_amount'] ?? $doc['total']   ?? 0,
                        'payment_method'         => $doc['payment_method'] ?? 'cash',
                        'payment_status'         => $doc['payment_status'] ?? 'pending',
                        'delivery_address'       => $doc['delivery_address'] ?? null,
                        'delivery_latitude'      => $doc['delivery_location']['lat'] ?? null,
                        'delivery_longitude'     => $doc['delivery_location']['lng'] ?? null,
                        'notes'                  => $doc['notes'] ?? $doc['note'] ?? null,
                        'created_at'             => $doc['created_at'] ?? now(),
                        'updated_at'             => $doc['updated_at'] ?? now(),
                    ]);

                    // Import order items
                    $items = $doc['cart'] ?? $doc['items'] ?? [];
                    foreach ($items as $item) {
                        OrderItem::create([
                            'order_id'       => $order->id,
                            'product_id'     => null, // resolved separately in import-products
                            'product_name'   => $item['name']  ?? $item['title'] ?? 'Unknown',
                            'quantity'       => $item['qty']   ?? $item['quantity'] ?? 1,
                            'unit_price'     => $item['price'] ?? 0,
                            'total_price'    => ($item['price'] ?? 0) * ($item['qty'] ?? $item['quantity'] ?? 1),
                            'attributes'     => json_encode($item['add_ons'] ?? $item['attributes'] ?? []),
                        ]);
                    }
                });
            }

            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Imported: $count orders | Skipped (exist): $skipped | Errors: $errors");

        return self::SUCCESS;
    }
}
