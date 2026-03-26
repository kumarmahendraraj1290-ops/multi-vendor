<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index(): View
    {
        $grouped = $this->cartService->getCartGroupedByVendor(auth()->user());

        return view('cart.index', compact('grouped'));
    }

    public function store(AddToCartRequest $request): RedirectResponse
    {
        try {
            $product = Product::findOrFail($request->product_id);
            $this->cartService->addItem(auth()->user(), $product, $request->quantity);

            return back()->with('success', "{$product->name} added to cart.");
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): RedirectResponse
    {
        $cart = $this->cartService->getOrCreateCart(auth()->user());
        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }

        try {
            $this->cartService->updateItemQuantity(auth()->user(), $cartItem, $request->quantity);
            return back()->with('success', 'Cart updated.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        $cart = $this->cartService->getOrCreateCart(auth()->user());
        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }

        $this->cartService->removeItem(auth()->user(), $cartItem);

        return back()->with('success', 'Item removed.');
    }
}
