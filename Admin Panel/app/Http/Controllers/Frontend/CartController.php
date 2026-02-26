<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckoutRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartCheckoutService $checkout,
    ) {}

    // ── Cart management ────────────────────────────────────────────────────────

    public function index(): View
    {
        $cart = $this->getOrCreateCart();
        return view('pages.cart', ['cart' => $cart->load('items.product.primaryImage')]);
    }

    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $product = Product::active()->inStock()->findOrFail($request->product_id);
        $cart    = $this->getOrCreateCart($product->vendor_id);

        // Enforce single-vendor cart
        if ($cart->vendor_id !== $product->vendor_id) {
            $message = 'Your cart contains items from another store. Clear it first.';
            return $request->expectsJson()
                ? response()->json(['error' => $message], 409)
                : back()->withErrors(['cart' => $message]);
        }

        $existing = CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $product->id)
                            ->first();

        if ($existing) {
            $existing->increment('quantity', $request->quantity);
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'unit_price' => $product->effective_price,
            ]);
        }

        $totalItems = $cart->fresh()->total_items;

        return $request->expectsJson()
            ? response()->json(['success' => true, 'cart_count' => $totalItems])
            : back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, int $itemId): JsonResponse|RedirectResponse
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:99']);

        $item = CartItem::findOrFail($itemId);

        if ($request->quantity === 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $request->quantity]);
        }

        return $request->expectsJson()
            ? response()->json(['success' => true])
            : back()->with('success', 'Cart updated.');
    }

    public function remove(int $itemId): JsonResponse|RedirectResponse
    {
        CartItem::findOrFail($itemId)->delete();

        return request()->expectsJson()
            ? response()->json(['success' => true])
            : back()->with('success', 'Item removed.');
    }

    public function clear(): RedirectResponse
    {
        $user = auth('web')->user();
        Cart::where('user_id', $user->id)->with('items')->get()
            ->each(function ($cart) {
                $cart->items()->delete();
                $cart->delete();
            });

        return redirect()->route('cart')->with('success', 'Cart cleared.');
    }

    // ── Checkout ──────────────────────────────────────────────────────────────

    public function checkout(): View
    {
        $user = auth('web')->user();
        $cart = Cart::with('items.product')->where('user_id', $user->id)->firstOrFail();

        return view('pages.checkout', [
            'cart'      => $cart,
            'addresses' => $user->addresses,
        ]);
    }

    public function placeOrder(CheckoutRequest $request): RedirectResponse
    {
        $user  = auth('web')->user();
        $order = $this->checkout->placeOrder($user, $request->validated());

        return redirect()->route('order.success', ['order' => $order->id])
                         ->with('success', 'Order placed successfully!');
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function getOrCreateCart(?int $vendorId = null): Cart
    {
        $user = auth('web')->user();

        $cart = Cart::where('user_id', $user->id)->first();

        if (! $cart && $vendorId) {
            $cart = Cart::create([
                'user_id'   => $user->id,
                'vendor_id' => $vendorId,
            ]);
        }

        return $cart ?? new Cart();
    }
}
