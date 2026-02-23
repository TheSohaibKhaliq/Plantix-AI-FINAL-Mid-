<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * php artisan firebase:import-users
 *
 * Reads firebase_export/users.json (produced by export_firebase.js) and
 * inserts/upserts records into the MySQL `users` table.
 *
 * Run AFTER: php artisan migrate
 */
class MigrateFirebaseUsers extends Command
{
    protected $signature   = 'firebase:import-users
                                {--file=firebase_export/users.json : Path to the exported JSON}
                                {--dry-run : Preview without writing to the database}';

    protected $description = 'Import users from Firebase export JSON into MySQL';

    public function handle(): int
    {
        $file = base_path($this->option('file'));

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            $this->line("Run: node export_firebase.js  first.");
            return self::FAILURE;
        }

        $users   = json_decode(file_get_contents($file), true);
        $dryRun  = $this->option('dry-run');
        $count   = 0;
        $skipped = 0;

        $this->info('Importing ' . count($users) . ' users' . ($dryRun ? ' [DRY RUN]' : '') . '...');
        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        foreach ($users as $doc) {
            $firebase_uid = $doc['_id'];

            $existing = User::where('firebase_uid', $firebase_uid)
                           ->orWhere('email', $doc['email'] ?? null)
                           ->first();

            if ($existing) {
                // Update firebase_uid linkage if missing
                if (!$existing->firebase_uid) {
                    if (!$dryRun) {
                        $existing->update(['firebase_uid' => $firebase_uid]);
                    }
                }
                $skipped++;
                $bar->advance();
                continue;
            }

            $role = match(true) {
                isset($doc['type']) && $doc['type'] === 'admin'  => 'admin',
                isset($doc['type']) && $doc['type'] === 'vendor' => 'vendor',
                isset($doc['type']) && $doc['type'] === 'driver' => 'driver',
                default => 'user',
            };

            $payload = [
                'name'         => $doc['name'] ?? ($doc['first_name'] ?? 'Unknown'),
                'email'        => $doc['email'] ?? ($firebase_uid . '@firebase.local'),
                'password'     => Hash::make(Str::random(24)), // force reset on first login
                'phone'        => $doc['phone'] ?? $doc['mobile'] ?? null,
                'role'         => $role,
                'firebase_uid' => $firebase_uid,
                'active'       => $doc['active'] ?? true,
                'fcm_token'    => $doc['fcm_token'] ?? $doc['token'] ?? null,
                'wallet_amount'=> $doc['wallet_amount'] ?? 0,
                'profile_photo'=> $doc['profile_photo'] ?? $doc['image'] ?? null,
                'vendor_id'    => null, // linked in firebase:import-vendors
                'must_reset_password' => true,
                'email_verified_at'   => now(),
                'created_at'   => $doc['created_at'] ?? now(),
                'updated_at'   => $doc['updated_at'] ?? now(),
            ];

            if (!$dryRun) {
                User::create($payload);
            }

            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Imported: $count users | Skipped (already exist): $skipped");

        return self::SUCCESS;
    }
}
