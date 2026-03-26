<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Events\PaymentSucceeded;
use App\Listeners\HandlePaymentSucceeded;
use App\Listeners\SendOrderPlacedNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(OrderPlaced::class, SendOrderPlacedNotification::class);
        Event::listen(PaymentSucceeded::class, HandlePaymentSucceeded::class);
    }
}
