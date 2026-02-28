<?php

namespace App\Services\Shared;

use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use App\Notifications\LowStockAlertNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * StockService
 *
 * Single responsibility: all stock mutation & validation logic lives here.
 * Controllers and the checkout service delegate to this class.
 */
class StockService
{
    /**
     * Assert that the product has enough stock for the requested quantity.
     * Throws a ValidationException if stock is insufficient.
     */
    public function assertSufficientStock(Product $product, int $requestedQty): void
    {
        if (! $product->track_stock) {
            return; // unlimited stock products
        }

        $available = $product->stock_quantity;

        if ($available < $requestedQty) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$available} unit(s) of \"{$product->name}\" are available.",
            ]);
        }
    }

    /**
     * Decrement stock after a confirmed sale.
     * Must be called inside a DB::transaction().
     */
    public function decrementStock(
        Product $product,
        int     $qty,
        ?int    $orderId    = null,
        ?int    $initiatedBy = null
    ): void {
        if (! $product->track_stock) {
            return;
        }

        $before = $product->stock_quantity;

        // Decrement on the products table (denormalised fast read)
        $product->decrement('stock_quantity', $qty);
        $after = $before - $qty;

        // Also decrement on product_stocks (vendor-level tracking)
        $stock = ProductStock::where('product_id', $product->id)
                             ->where('vendor_id', $product->vendor_id)
                             ->first();

        if ($stock) {
            $stock->decrement('quantity', $qty);
            $this->checkLowStockAlert($product, $stock->fresh());
        } else {
            $product->refresh();
            $threshold = (int) config('plantix.low_stock_threshold', 10);
            if ($product->stock_quantity >= 0 && $product->stock_quantity <= $threshold) {
                try {
                    User::where('role', 'admin')->get()
                        ->each(fn ($admin) => $admin->notify(
                            new LowStockAlertNotification($product, $product->stock_quantity)
                        ));
                } catch (\Throwable $e) {
                    Log::warning('Low-stock alert (products table) notification failed: ' . $e->getMessage());
                }
            }
        }

        $this->log([
            'product_id'      => $product->id,
            'vendor_id'       => $product->vendor_id,
            'order_id'        => $orderId,
            'initiated_by'    => $initiatedBy,
            'type'            => InventoryLog::TYPE_SALE,
            'quantity_before' => $before,
            'quantity_change' => -$qty,
            'quantity_after'  => $after,
            'notes'           => "Sale: {$qty} unit(s) deducted" . ($orderId ? " for order #{$orderId}" : ''),
        ]);
    }

    /**
     * Restore stock when an order is cancelled or rejected.
     * Must be called inside a DB::transaction().
     */
    public function restoreStock(
        Product $product,
        int     $qty,
        string  $reason      = 'cancel',  // 'cancel' | 'return'
        ?int    $orderId     = null,
        ?int    $returnId    = null,
        ?int    $initiatedBy = null
    ): void {
        if (! $product->track_stock) {
            return;
        }

        $before = $product->stock_quantity;

        $product->increment('stock_quantity', $qty);
        $after = $before + $qty;

        $stock = ProductStock::where('product_id', $product->id)
                             ->where('vendor_id', $product->vendor_id)
                             ->first();
        if ($stock) {
            $stock->increment('quantity', $qty);
        }

        $type = $reason === 'return' ? InventoryLog::TYPE_RETURN : InventoryLog::TYPE_CANCEL;

        $this->log([
            'product_id'      => $product->id,
            'vendor_id'       => $product->vendor_id,
            'order_id'        => $orderId,
            'return_id'       => $returnId,
            'initiated_by'    => $initiatedBy,
            'type'            => $type,
            'quantity_before' => $before,
            'quantity_change' => $qty,
            'quantity_after'  => $after,
            'notes'           => ucfirst($reason) . ": {$qty} unit(s) restored" . ($orderId ? " for order #{$orderId}" : ''),
        ]);
    }

    /**
     * Restock a product (admin or vendor).
     */
    public function restock(Product $product, int $qty, int $vendorId, ?int $initiatedBy = null): void
    {
        $before = $product->stock_quantity;
        $product->increment('stock_quantity', $qty);

        ProductStock::updateOrCreate(
            ['product_id' => $product->id, 'vendor_id' => $vendorId],
            ['quantity'   => \DB::raw("quantity + {$qty}")]
        );

        $this->log([
            'product_id'      => $product->id,
            'vendor_id'       => $vendorId,
            'initiated_by'    => $initiatedBy,
            'type'            => InventoryLog::TYPE_RESTOCK,
            'quantity_before' => $before,
            'quantity_change' => $qty,
            'quantity_after'  => $before + $qty,
            'notes'           => "Manual restock: {$qty} unit(s) added",
        ]);
    }

    /**
     * Full stock set (e.g. after manual inventory count).
     */
    public function setStock(Product $product, int $qty, int $vendorId, ?int $initiatedBy = null): void
    {
        $before = $product->stock_quantity;
        $product->update(['stock_quantity' => $qty]);

        ProductStock::updateOrCreate(
            ['product_id' => $product->id, 'vendor_id' => $vendorId],
            ['quantity'   => $qty]
        );

        $this->log([
            'product_id'      => $product->id,
            'vendor_id'       => $vendorId,
            'initiated_by'    => $initiatedBy,
            'type'            => InventoryLog::TYPE_ADJUSTMENT,
            'quantity_before' => $before,
            'quantity_change' => $qty - $before,
            'quantity_after'  => $qty,
            'notes'           => "Manual adjustment: set to {$qty} unit(s)",
        ]);
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function checkLowStockAlert(Product $product, ProductStock $stock): void
    {
        if ($stock->isLow() || $stock->isOutOfStock()) {
            try {
                User::where('role', 'admin')->get()
                    ->each(fn ($admin) => $admin->notify(new LowStockAlertNotification($product, $stock->quantity)));
            } catch (\Throwable $e) {
                Log::warning('Low-stock alert notification failed: ' . $e->getMessage());
            }
        }
    }

    private function log(array $data): void
    {
        try {
            InventoryLog::create($data);
        } catch (\Throwable $e) {
            // Never let logging break the main flow
            Log::error('InventoryLog write failed: ' . $e->getMessage(), $data);
        }
    }
}



