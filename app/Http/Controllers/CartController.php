<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index(): JsonResponse
    {
        $grouped = $this->cartService->getCartGroupedByVendor(auth()->user());

        $result = $grouped->map(function ($items, $vendorId) {
            $vendor = $items->first()->product->vendor;
            return [
                'vendor' => ['id' => $vendor->id, 'name' => $vendor->name],
                'items' => $items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => number_format($item->quantity * $item->product->price, 2, '.', ''),
                ]),
            ];
        })->values();

        return response()->json(['cart' => $result]);
    }

    public function store(AddToCartRequest $request): JsonResponse
    {
        try {
            $product = Product::findOrFail($request->product_id);
            $item = $this->cartService->addItem(auth()->user(), $product, $request->quantity);

            return response()->json([
                'message' => 'Item added to cart.',
                'item' => $item->load('product'),
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart(auth()->user());
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        try {
            $item = $this->cartService->updateItemQuantity(auth()->user(), $cartItem, $request->quantity);
            return response()->json(['message' => 'Cart updated.', 'item' => $item->load('product')]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(CartItem $cartItem): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart(auth()->user());
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $this->cartService->removeItem(auth()->user(), $cartItem);

        return response()->json(['message' => 'Item removed from cart.']);
    }
}
