<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Services\Shared\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * VendorInventoryController
 *
 * Lets vendors monitor and adjust stock levels for their own products.
 */
class VendorInventoryController extends Controller
{
    public function __construct(
        private readonly StockService $stockService,
    ) {}

    private function vendorId(): int
    {
        return auth('vendor')->user()->vendor->id;
    }

    /**
     * Show inventory report for all vendor products.
     * Route: GET /vendor/inventory
     */
    public function index(Request $request): View
    {
        $vendorId = $this->vendorId();

        $query = ProductStock::with(['product'])
            ->where('vendor_id', $vendorId);

        if ($request->filled('search')) {
            $query->whereHas('product', fn ($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        if ($request->filled('stock_status')) {
            match ($request->stock_status) {
                'low'      => $query->whereRaw('quantity > 0 AND quantity <= low_stock_threshold'),
                'out'      => $query->where('quantity', '<=', 0),
                'in_stock' => $query->where('quantity', '>', 0),
                default    => null,
            };
        }

        $stocks = $query->latest()->paginate(25)->withQueryString();

        $summary = [
            'total_products'    => ProductStock::where('vendor_id', $vendorId)->count(),
            'out_of_stock'      => ProductStock::where('vendor_id', $vendorId)->where('quantity', '<=', 0)->count(),
            'low_stock'         => ProductStock::where('vendor_id', $vendorId)->whereRaw('quantity > 0 AND quantity <= low_stock_threshold')->count(),
            'total_stock_value' => ProductStock::where('vendor_id', $vendorId)
                ->join('products', 'product_stocks.product_id', '=', 'products.id')
                ->selectRaw('SUM(product_stocks.quantity * products.price) as total')
                ->value('total') ?? 0,
        ];

        return view('vendor.inventory.index', compact('stocks', 'summary'));
    }

    /**
     * Update the stock quantity for a product.
     * Route: POST /vendor/inventory/{id}/update
     */
    public function update(Request $request, int $productId): RedirectResponse
    {
        $request->validate([
            'quantity'            => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        $stock = ProductStock::where('product_id', $productId)
                             ->where('vendor_id', $this->vendorId())
                             ->firstOrFail();

        $stock->update(array_filter([
            'quantity'            => $request->quantity,
            'low_stock_threshold' => $request->low_stock_threshold,
        ], fn ($v) => $v !== null));

        return back()->with('success', 'Stock updated successfully.');
    }
}
