<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Events\PaymentSucceeded;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(private CartService $cartService) {}

    /**
     * Process checkout: validate stock, split by vendor, create orders + payments.
     * Uses pessimistic locking to prevent inventory race conditions.
     *
     * @return Collection<int, Order>
     */
    public function checkout(User $user): Collection
    {
        $cart = $user->cart;

        if (!$cart || $cart->items()->count() === 0) {
            throw new \RuntimeException('Cart is empty.');
        }

        return DB::transaction(function () use ($user, $cart) {
            $items = $cart->items()->with('product.vendor')->get();

            // Validate stock with pessimistic locking
            foreach ($items as $item) {
                $product = $item->product()->lockForUpdate()->first();
                if ($item->quantity > $product->stock) {
                    throw new \RuntimeException(
                        "Insufficient stock for \"{$product->name}\". Available: {$product->stock}, Requested: {$item->quantity}"
                    );
                }
            }

            // Group by vendor
            $grouped = $items->groupBy(fn ($item) => $item->product->vendor_id);

            $orders = collect();

            foreach ($grouped as $vendorId => $vendorItems) {
                $total = $vendorItems->sum(fn ($item) => $item->quantity * $item->product->price);

                $order = Order::create([
                    'user_id' => $user->id,
                    'vendor_id' => $vendorId,
                    'total' => $total,
                    'status' => 'pending',
                ]);

                foreach ($vendorItems as $item) {
                    $order->items()->create([
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'product_price' => $item->product->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->quantity * $item->product->price,
                    ]);

                    // Deduct stock
                    $item->product->decrement('stock', $item->quantity);
                }

                // Create payment marked as paid
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'amount' => $total,
                    'status' => 'paid',
                    'method' => 'simulated',
                ]);

                OrderPlaced::dispatch($order);
                PaymentSucceeded::dispatch($payment);

                $orders->push($order);
            }

            // Clear cart
            $this->cartService->clearCart($user);

            return $orders;
        });
    }
}
