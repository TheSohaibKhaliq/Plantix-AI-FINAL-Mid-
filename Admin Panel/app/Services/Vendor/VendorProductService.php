<?php

namespace App\Services\Vendor;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Vendor;
use App\Services\Shared\StockService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Vendor Product Service
 * Section 4 – Vendor Flow: Product CRUD Lifecycle
 *
 * Handles the full product lifecycle with image management and stock tracking.
 * All operations are scoped to the authenticated vendor.
 */
class VendorProductService
{
    public function __construct(
        private readonly StockService $stock,
    ) {}

    /**
     * Return paginated products scoped to this vendor.
     */
    public function getProducts(Vendor $vendor, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Product::with(['category', 'stock', 'primaryImage'])
            ->where('vendor_id', $vendor->id)
            ->withTrashed(false); // only active (not soft-deleted)

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (isset($filters['low_stock'])) {
            $query->whereHas('stock', fn ($q) => $q->whereColumn('quantity', '<=', 'low_stock_threshold'));
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new product with primary image, gallery, and initial stock.
     *
     * @param  Vendor          $vendor
     * @param  array           $data        Validated request data
     * @param  UploadedFile|null $primaryImage
     * @param  UploadedFile[]  $galleryImages
     * @return Product
     */
    public function createProduct(
        Vendor        $vendor,
        array         $data,
        ?UploadedFile $primaryImage  = null,
        array         $galleryImages = [],
    ): Product {
        return DB::transaction(function () use ($vendor, $data, $primaryImage, $galleryImages) {
            // Auto-generate unique slug
            $slug = $this->generateSlug($data['name']);

            $product = Product::create([
                'vendor_id'      => $vendor->id,
                'category_id'    => $data['category_id'] ?? null,
                'name'           => $data['name'],
                'slug'           => $slug,
                'description'    => $data['description'] ?? null,
                'price'          => $data['price'],
                'discount_price' => $data['discount_price'] ?? null,
                'is_active'      => $data['is_active'] ?? true,
                'is_featured'    => $data['is_featured'] ?? false,
                'track_stock'    => $data['track_stock'] ?? true,
                'stock_quantity' => $data['stock_quantity'] ?? 0,
                'sort_order'     => $data['sort_order'] ?? 0,
            ]);

            // Primary image
            if ($primaryImage) {
                $path = $primaryImage->store('products', 'public');
                $product->update(['image' => $path]);
                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $path,
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }

            // Gallery images
            foreach ($galleryImages as $idx => $file) {
                $gPath = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $gPath,
                    'is_primary' => false,
                    'sort_order' => $idx + 1,
                ]);
            }

            // Initial stock
            if ($product->track_stock) {
                $this->stock->setStock(
                    product:     $product,
                    qty:         (int) ($data['stock_quantity'] ?? 0),
                    vendorId:    $vendor->id,
                    initiatedBy: $vendor->author_id,
                );
            }

            return $product->fresh(['stock', 'images']);
        });
    }

    /**
     * Update an existing product with optional image replacement.
     *
     * @param  Product         $product
     * @param  Vendor          $vendor
     * @param  array           $data
     * @param  UploadedFile|null $primaryImage
     * @param  UploadedFile[]  $galleryImages
     * @return Product
     */
    public function updateProduct(
        Product       $product,
        Vendor        $vendor,
        array         $data,
        ?UploadedFile $primaryImage  = null,
        array         $galleryImages = [],
    ): Product {
        abort_unless((int) $product->vendor_id === (int) $vendor->id, 403, 'Unauthorized.');

        return DB::transaction(function () use ($product, $vendor, $data, $primaryImage, $galleryImages) {
            $updates = array_filter([
                'category_id'    => $data['category_id'] ?? null,
                'name'           => $data['name'] ?? null,
                'description'    => $data['description'] ?? null,
                'price'          => $data['price'] ?? null,
                'discount_price' => $data['discount_price'] ?? null,
                'is_active'      => isset($data['is_active']) ? (bool) $data['is_active'] : null,
                'is_featured'    => isset($data['is_featured']) ? (bool) $data['is_featured'] : null,
                'track_stock'    => isset($data['track_stock']) ? (bool) $data['track_stock'] : null,
                'sort_order'     => $data['sort_order'] ?? null,
            ], fn ($v) => $v !== null);

            // Regenerate slug if name changed
            if (! empty($data['name']) && $data['name'] !== $product->name) {
                $updates['slug'] = $this->generateSlug($data['name'], $product->id);
            }

            $product->update($updates);

            // Replace primary image
            if ($primaryImage) {
                // Delete old
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $path = $primaryImage->store('products', 'public');
                $product->update(['image' => $path]);

                ProductImage::where('product_id', $product->id)
                    ->where('is_primary', true)
                    ->update(['image' => $path]);
            }

            // Append new gallery images
            if (! empty($galleryImages)) {
                $maxSort = ProductImage::where('product_id', $product->id)->max('sort_order') ?? 0;
                foreach ($galleryImages as $idx => $file) {
                    $gPath = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image'      => $gPath,
                        'is_primary' => false,
                        'sort_order' => $maxSort + $idx + 1,
                    ]);
                }
            }

            // Update stock if stock_quantity supplied
            if (isset($data['stock_quantity']) && $product->track_stock) {
                $this->stock->setStock(
                    product:     $product->fresh(),
                    qty:         (int) $data['stock_quantity'],
                    vendorId:    $vendor->id,
                    initiatedBy: $vendor->author_id,
                );
            }

            return $product->fresh(['stock', 'images', 'category']);
        });
    }

    /**
     * Soft-delete a product. Active orders that reference it still retain snapshots.
     * Sets stock to 0 to prevent new cart additions.
     */
    public function deleteProduct(Product $product, Vendor $vendor): void
    {
        abort_unless((int) $product->vendor_id === (int) $vendor->id, 403, 'Unauthorized.');

        DB::transaction(function () use ($product, $vendor) {
            // Zero out stock to block add-to-cart immediately
            if ($product->track_stock) {
                $this->stock->setStock(
                    product:     $product,
                    qty:         0,
                    vendorId:    $vendor->id,
                    initiatedBy: $vendor->author_id,
                );
            }

            $product->update(['is_active' => false]);
            $product->delete(); // SoftDelete – sets deleted_at
        });
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function generateSlug(string $name, ?int $excludeId = null): string
    {
        $base  = Str::slug($name);
        $slug  = $base;
        $count = 2;

        while (
            Product::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-{$count}";
            $count++;
        }

        return $slug;
    }
}
