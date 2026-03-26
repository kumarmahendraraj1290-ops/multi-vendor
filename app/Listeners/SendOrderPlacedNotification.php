<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Support\Facades\Log;

class SendOrderPlacedNotification
{
    public function handle(OrderPlaced $event): void
    {
        Log::info('Order placed notification', [
            'order_id' => $event->order->id,
            'user_id' => $event->order->user_id,
            'vendor_id' => $event->order->vendor_id,
            'total' => $event->order->total,
        ]);
    }
}
