<?php

namespace App\Services\Vendor;

use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Vendor;
use App\Services\Shared\StockService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

/**
 * Vendor Inventory Service
 * Section 4 – Vendor Flow: Inventory Management Logic
 * Section 10 – Inventory Logic
 *
 * Provides vendor-scoped inventory overview, stock updates, and low-stock alerts.
 * All stock writes use StockService to guarantee inventory_log entries.
 */
class VendorInventoryService
{
    public function __construct(
        private readonly StockService $stock,
    ) {}

    /**
     * Return paginated inventory view scoped to this vendor.
     * Includes stock status flags for easy UI rendering.
     *
     * @param  Vendor  $vendor
     * @param  array   $filters  ['status' => 'low'|'out'|'ok', 'search' => '...']
     * @param  int     $perPage
     * @return LengthAwarePaginator
     */
    public function getInventory(Vendor $vendor, array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = Product::with(['stock', 'category'])
            ->where('vendor_id', $vendor->id)
            ->where('track_stock', true);

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (! empty($filters['status'])) {
            match ($filters['status']) {
                'out' => $query->where('stock_quantity', '<=', 0),
                'low' => $query->whereHas(
                    'stock',
                    fn ($q) => $q->whereColumn('quantity', '<=', 'low_stock_threshold')
                              ->where('quantity', '>', 0)
                ),
                'ok'  => $query->whereHas(
                    'stock',
                    fn ($q) => $q->whereColumn('quantity', '>', 'low_stock_threshold')
                ),
                default => null,
            };
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Summary stats for the inventory dashboard widget.
     *
     * @return array{total_products: int, out_of_stock: int, low_stock: int, well_stocked: int}
     */
    public function getSummary(Vendor $vendor): array
    {
        $products = Product::where('vendor_id', $vendor->id)
            ->where('track_stock', true)
            ->with('stock')
            ->get();

        $out  = $products->filter(fn ($p) => ($p->stock?->quantity ?? $p->stock_quantity) <= 0)->count();
        $low  = $products->filter(fn ($p) => ($p->stock?->isLow() ?? false))->count();
        $ok   = $products->count() - $out - $low;

        return [
            'total_products' => $products->count(),
            'out_of_stock'   => $out,
            'low_stock'      => $low,
            'well_stocked'   => max(0, $ok),
        ];
    }

    /**
     * Set absolute stock quantity for a product (full override).
     * Records an adjustment log entry.
     *
     * @param  Vendor   $vendor
     * @param  int      $productId
     * @param  int      $quantity            New absolute quantity
     * @param  int|null $lowStockThreshold   Optional new threshold
     * @return ProductStock
     * @throws ValidationException
     */
    public function setStock(
        Vendor $vendor,
        int    $productId,
        int    $quantity,
        ?int   $lowStockThreshold = null,
    ): ProductStock {
        $product = Product::where('vendor_id', $vendor->id)
            ->findOrFail($productId);

        if ($quantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Stock quantity cannot be negative.',
            ]);
        }

        $this->stock->setStock($product, $quantity, $vendor->id, $vendor->author_id);

        // Update low stock threshold if provided
        if ($lowStockThreshold !== null) {
            ProductStock::where('product_id', $product->id)
                ->where('vendor_id', $vendor->id)
                ->update(['low_stock_threshold' => max(0, $lowStockThreshold)]);
        }

        return ProductStock::firstOrNew([
            'product_id' => $product->id,
            'vendor_id'  => $vendor->id,
        ])->refresh();
    }

    /**
     * Add stock on top of existing quantity (restock event).
     *
     * @param  Vendor  $vendor
     * @param  int     $productId
     * @param  int     $qty       Amount to add
     * @return ProductStock
     */
    public function restock(Vendor $vendor, int $productId, int $qty): ProductStock
    {
        $product = Product::where('vendor_id', $vendor->id)->findOrFail($productId);

        if ($qty <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Restock quantity must be greater than zero.',
            ]);
        }

        $this->stock->restock($product, $qty, $vendor->id, $vendor->author_id);

        return ProductStock::where('product_id', $product->id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();
    }

    /**
     * Return the last N inventory log entries for a specific product.
     *
     * @param  Vendor  $vendor
     * @param  int     $productId
     * @param  int     $limit
     * @return \Illuminate\Database\Eloquent\Collection<InventoryLog>
     */
    public function getProductLogs(Vendor $vendor, int $productId, int $limit = 20)
    {
        Product::where('vendor_id', $vendor->id)->findOrFail($productId); // ownership check

        return InventoryLog::where('product_id', $productId)
            ->where('vendor_id', $vendor->id)
            ->with('createdBy')
            ->latest()
            ->take($limit)
            ->get();
    }
}
