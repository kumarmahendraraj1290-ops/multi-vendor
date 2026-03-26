<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private CheckoutService $checkoutService) {}

    public function store(): RedirectResponse|View
    {
        try {
            $orders = $this->checkoutService->checkout(auth()->user());
            $orders->each->load('items', 'payment', 'vendor:id,name');

            return view('checkout.success', compact('orders'));
        } catch (\RuntimeException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }
}
