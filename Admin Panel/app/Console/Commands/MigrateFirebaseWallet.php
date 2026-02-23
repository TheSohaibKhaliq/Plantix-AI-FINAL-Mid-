<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use App\Models\WalletTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * php artisan firebase:import-wallet
 *
 * Reads firebase_export/wallet.json and inserts records into `wallet_transactions`.
 * Also recalculates the running wallet balance for each user/vendor.
 *
 * Run AFTER: firebase:import-users, firebase:import-vendors
 */
class MigrateFirebaseWallet extends Command
{
    protected $signature   = 'firebase:import-wallet
                                {--file=firebase_export/wallet.json}
                                {--transactions-file=firebase_export/transactions.json : Also import transactions collection}
                                {--dry-run}';

    protected $description = 'Import wallet transactions from Firebase export JSON into MySQL';

    public function handle(): int
    {
        $files  = [
            $this->option('file'),
            $this->option('transactions-file'),
        ];

        $allDocs = [];
        foreach ($files as $file) {
            $path = base_path($file);
            if (file_exists($path)) {
                $decoded = json_decode(file_get_contents($path), true) ?? [];
                $allDocs = array_merge($allDocs, $decoded);
                $this->line("Loaded " . count($decoded) . " records from $file");
            }
        }

        if (empty($allDocs)) {
            $this->error("No wallet/transaction export files found. Run: node export_firebase.js");
            return self::FAILURE;
        }

        $dryRun  = $this->option('dry-run');
        $count   = 0; $skipped = 0; $errors = 0;

        $this->info('Importing ' . count($allDocs) . ' wallet transactions' . ($dryRun ? ' [DRY RUN]' : '') . '...');
        $bar = $this->output->createProgressBar(count($allDocs));
        $bar->start();

        foreach ($allDocs as $doc) {
            $firebaseId = $doc['_id'];

            if (WalletTransaction::where('firebase_doc_id', $firebaseId)->exists()) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // Resolve user
            $user   = User::where('firebase_uid', $doc['user_id'] ?? null)->first();
            $vendor = !empty($doc['vendor_id'])
                ? Vendor::where('firebase_doc_id', $doc['vendor_id'])->first()
                : null;

            if (!$user) {
                $errors++;
                $bar->advance();
                continue;
            }

            $type   = strtolower($doc['type'] ?? $doc['transaction_type'] ?? 'credit');
            $amount = abs((float) ($doc['amount'] ?? 0));

            if (!$dryRun) {
                WalletTransaction::create([
                    'firebase_doc_id'  => $firebaseId,
                    'user_id'          => $user->id,
                    'vendor_id'        => $vendor?->id,
                    'order_id'         => null, // can be updated separately
                    'type'             => in_array($type, ['credit', 'debit']) ? $type : 'credit',
                    'amount'           => $amount,
                    'balance_after'    => (float) ($doc['balance_after'] ?? $doc['balance'] ?? 0),
                    'description'      => $doc['message'] ?? $doc['description'] ?? null,
                    'reference'        => $doc['reference'] ?? null,
                    'created_at'       => $doc['created_at'] ?? now(),
                    'updated_at'       => $doc['updated_at'] ?? now(),
                ]);
            }

            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Imported: $count transactions | Skipped: $skipped | Errors: $errors");

        if (!$dryRun) {
            $this->info("Recalculating wallet balances...");
            $this->recalculateBalances();
        }

        return self::SUCCESS;
    }

    private function recalculateBalances(): void
    {
        $userIds = WalletTransaction::distinct()->pluck('user_id');

        foreach ($userIds as $userId) {
            $balance = WalletTransaction::where('user_id', $userId)
                ->selectRaw("SUM(CASE WHEN type='credit' THEN amount ELSE -amount END) as balance")
                ->value('balance') ?? 0;

            User::where('id', $userId)->update(['wallet_amount' => $balance]);
        }

        $vendorIds = WalletTransaction::whereNotNull('vendor_id')->distinct()->pluck('vendor_id');
        foreach ($vendorIds as $vendorId) {
            $balance = WalletTransaction::where('vendor_id', $vendorId)
                ->selectRaw("SUM(CASE WHEN type='credit' THEN amount ELSE -amount END) as balance")
                ->value('balance') ?? 0;

            Vendor::where('id', $vendorId)->update(['wallet_amount' => $balance]);
        }

        $this->info("✅ Wallet balances recalculated.");
    }
}
