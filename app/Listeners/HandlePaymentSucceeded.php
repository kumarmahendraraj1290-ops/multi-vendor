<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use Illuminate\Support\Facades\Log;

class HandlePaymentSucceeded
{
    public function handle(PaymentSucceeded $event): void
    {
        $event->payment->order->update(['status' => 'confirmed']);

        Log::info('Payment succeeded', [
            'payment_id' => $event->payment->id,
            'order_id' => $event->payment->order_id,
            'amount' => $event->payment->amount,
        ]);
    }
}
