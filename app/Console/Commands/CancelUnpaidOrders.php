<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CancelUnpaidOrders extends Command
{
    protected $signature = 'orders:cancel-unpaid {--minutes=30 : Minutes after which unpaid orders are cancelled}';
    protected $description = 'Cancel orders that remain unpaid after a given time';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');

        $orders = Order::where('status', 'pending')
            ->whereDoesntHave('payment', fn ($q) => $q->where('status', 'paid'))
            ->where('created_at', '<', now()->subMinutes($minutes))
            ->get();

        $count = 0;
        foreach ($orders as $order) {
            $order->update(['status' => 'cancelled']);

            // Restore stock
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            $count++;
        }

        $this->info("Cancelled {$count} unpaid order(s).");

        return self::SUCCESS;
    }
}
