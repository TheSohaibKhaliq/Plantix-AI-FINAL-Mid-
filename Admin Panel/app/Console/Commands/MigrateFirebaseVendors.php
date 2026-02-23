<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * php artisan firebase:import-vendors
 *
 * Reads firebase_export/vendors.json and inserts records into `vendors`.
 * Also links the owner's User record (vendor_id FK on users table).
 *
 * Run AFTER: firebase:import-users
 */
class MigrateFirebaseVendors extends Command
{
    protected $signature   = 'firebase:import-vendors
                                {--file=firebase_export/vendors.json}
                                {--dry-run}';

    protected $description = 'Import vendors from Firebase export JSON into MySQL';

    public function handle(): int
    {
        $file = base_path($this->option('file'));

        if (!file_exists($file)) {
            $this->error("File not found: $file  —  Run: node export_firebase.js");
            return self::FAILURE;
        }

        $vendors = json_decode(file_get_contents($file), true);
        $dryRun  = $this->option('dry-run');
        $count   = 0; $skipped = 0;

        $this->info('Importing ' . count($vendors) . ' vendors' . ($dryRun ? ' [DRY RUN]' : '') . '...');
        $bar = $this->output->createProgressBar(count($vendors));
        $bar->start();

        foreach ($vendors as $doc) {
            $firebaseId = $doc['_id'];

            if (Vendor::where('firebase_doc_id', $firebaseId)->exists()) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // Resolve the owning user by firebase_uid
            $owner = User::where('firebase_uid', $doc['author_id'] ?? $doc['user_id'] ?? null)->first()
                  ?? User::where('firebase_uid', $firebaseId)->first(); // fallback: vendor IS the user doc

            // Resolve category
            $category = Category::where('name', $doc['categories'] ?? $doc['category'] ?? '')
                                 ->first();

            $payload = [
                'firebase_doc_id'   => $firebaseId,
                'author_id'         => $owner?->id,
                'zone_id'           => null, // linked in firebase:import-zones
                'category_id'       => $category?->id,
                'name'              => $doc['name'] ?? $doc['title'] ?? 'Unknown Vendor',
                'description'       => $doc['description'] ?? null,
                'address'           => $doc['address'] ?? null,
                'latitude'          => $this->geoLat($doc),
                'longitude'         => $this->geoLng($doc),
                'phone'             => $doc['phone'] ?? null,
                'email'             => $doc['email'] ?? null,
                'logo'              => $doc['logo'] ?? $doc['image'] ?? null,
                'cover_photo'       => $doc['cover_photo'] ?? $doc['banner'] ?? null,
                'is_open'           => $doc['open'] ?? $doc['is_open'] ?? false,
                'active'            => $doc['active'] ?? true,
                'approved'          => $doc['approved'] ?? $doc['verify'] ?? false,
                'rating'            => (float) ($doc['rating'] ?? 0),
                'rating_count'      => (int)   ($doc['rating_count'] ?? 0),
                'delivery_time'     => $doc['delivery_time'] ?? null,
                'minimum_order'     => $doc['min_order'] ?? $doc['minimum_order'] ?? 0,
                'delivery_charge'   => $doc['delivery_charge'] ?? $doc['delivery_fee'] ?? 0,
                'wallet_amount'     => $doc['wallet_amount'] ?? 0,
                'commission'        => $doc['commission'] ?? 0,
                'tax'               => $doc['tax'] ?? 0,
                'created_at'        => $doc['created_at'] ?? now(),
                'updated_at'        => $doc['updated_at'] ?? now(),
            ];

            if (!$dryRun) {
                $vendor = Vendor::create($payload);

                // Back-link the owner user to this vendor
                if ($owner && !$owner->vendor_id) {
                    $owner->update(['vendor_id' => $vendor->id, 'role' => 'vendor']);
                }
            }

            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Imported: $count vendors | Skipped: $skipped");

        return self::SUCCESS;
    }

    private function geoLat(array $doc): ?float
    {
        if (isset($doc['location']['lat'])) return (float) $doc['location']['lat'];
        if (isset($doc['latitude']))         return (float) $doc['latitude'];
        return null;
    }

    private function geoLng(array $doc): ?float
    {
        if (isset($doc['location']['lng'])) return (float) $doc['location']['lng'];
        if (isset($doc['longitude']))        return (float) $doc['longitude'];
        return null;
    }
}
