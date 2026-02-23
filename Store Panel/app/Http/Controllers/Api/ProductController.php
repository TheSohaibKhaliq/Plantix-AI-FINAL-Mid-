<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * ProductController (Store Panel API)
 *
 * Replaces all database.collection('vendor_products')... Firestore JS calls in:
 *   - foods/index.blade.php
 *   - foods/create.blade.php
 *   - foods/edit.blade.php
 */
class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    private function vendorId(): int
    {
        return Auth::user()->vendor_id
            ?? abort(403, 'No vendor account linked to this user.');
    }

    /**
     * GET /api/products
     * Replaces: database.collection('vendor_products').where('vendor_id','==',id).get()
     */
    public function index(Request $request): JsonResponse
    {
        $vendorId = $this->vendorId();

        $products = Product::where('vendor_id', $vendorId)
            ->with(['category', 'attributes'])
            ->when($request->active, fn($q) => $q->where('active', true))
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%"))
            ->when($request->category_id, fn($q, $c) => $q->where('category_id', $c))
            ->skip((int) $request->get('skip', 0))
            ->take((int) $request->get('limit', 50))
            ->get();

        return response()->json($products);
    }

    /**
     * POST /api/products
     * Replaces: database.collection('vendor_products').add({...})
     */
    public function store(Request $request): JsonResponse
    {
        $vendorId = $this->vendorId();

        $validated = $request->validate([
            'name'           => 'required|string|max:200',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'category_id'    => 'required|exists:categories,id',
            'image'          => 'nullable|image|max:2048',
            'veg'            => 'boolean',
            'active'         => 'boolean',
            'tax_id'         => 'nullable|exists:taxes,id',
            'attributes'     => 'nullable|array',
            'attributes.*.name'  => 'required_with:attributes|string',
            'attributes.*.price' => 'required_with:attributes|numeric|min:0',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['vendor_id'] = $vendorId;
        $product = Product::create($validated);

        if (!empty($validated['attributes'])) {
            foreach ($validated['attributes'] as $attr) {
                $product->attributes()->create($attr);
            }
        }

        return response()->json($product->load('attributes'), 201);
    }

    /**
     * GET /api/products/{product}
     */
    public function show(Product $product): JsonResponse
    {
        $this->authorizeProduct($product);
        return response()->json($product->load(['category', 'attributes']));
    }

    /**
     * PUT /api/products/{product}
     * Replaces: database.collection('vendor_products').doc(id).update({...})
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorizeProduct($product);

        $validated = $request->validate([
            'name'           => 'sometimes|string|max:200',
            'description'    => 'nullable|string',
            'price'          => 'sometimes|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'category_id'    => 'sometimes|exists:categories,id',
            'image'          => 'nullable|image|max:2048',
            'veg'            => 'boolean',
            'active'         => 'boolean',
            'tax_id'         => 'nullable|exists:taxes,id',
            'attributes'     => 'nullable|array',
            'attributes.*.name'  => 'required_with:attributes|string',
            'attributes.*.price' => 'required_with:attributes|numeric|min:0',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        if (isset($validated['attributes'])) {
            $product->attributes()->delete();
            foreach ($validated['attributes'] as $attr) {
                $product->attributes()->create($attr);
            }
        }

        return response()->json($product->load('attributes'));
    }

    /**
     * DELETE /api/products/{product}
     * Replaces: database.collection('vendor_products').doc(id).delete()
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorizeProduct($product);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return response()->json(null, 204);
    }

    /**
     * PATCH /api/products/{product}/toggle-active
     */
    public function toggleActive(Product $product): JsonResponse
    {
        $this->authorizeProduct($product);
        $product->update(['active' => !$product->active]);
        return response()->json(['active' => $product->active]);
    }

    private function authorizeProduct(Product $product): void
    {
        if ($product->vendor_id !== $this->vendorId() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }
    }
}
