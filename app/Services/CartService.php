<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

class CartService
{
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    public function addItem(User $user, Product $product, int $quantity = 1): CartItem
    {
        $cart = $this->getOrCreateCart($user);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        $newQuantity = $item ? $item->quantity + $quantity : $quantity;

        if ($newQuantity > $product->stock) {
            throw new \InvalidArgumentException(
                "Requested quantity ({$newQuantity}) exceeds available stock ({$product->stock})."
            );
        }

        if ($item) {
            $item->update(['quantity' => $newQuantity]);
            return $item->fresh();
        }

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);
    }

    public function updateItemQuantity(User $user, CartItem $cartItem, int $quantity): CartItem
    {
        if ($quantity > $cartItem->product->stock) {
            throw new \InvalidArgumentException(
                "Requested quantity ({$quantity}) exceeds available stock ({$cartItem->product->stock})."
            );
        }

        $cartItem->update(['quantity' => $quantity]);
        return $cartItem->fresh();
    }

    public function removeItem(User $user, CartItem $cartItem): void
    {
        $cartItem->delete();
    }

    public function getCartGroupedByVendor(User $user): Collection
    {
        $cart = $this->getOrCreateCart($user);

        return $cart->items()
            ->with('product.vendor')
            ->get()
            ->groupBy(fn (CartItem $item) => $item->product->vendor_id);
    }

    public function clearCart(User $user): void
    {
        $cart = $user->cart;
        if ($cart) {
            $cart->items()->delete();
        }
    }
}
