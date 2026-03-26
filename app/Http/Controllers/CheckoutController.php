<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function __construct(private CheckoutService $checkoutService) {}

    public function store(): JsonResponse
    {
        try {
            $orders = $this->checkoutService->checkout(auth()->user());

            return response()->json([
                'message' => 'Checkout successful.',
                'orders' => $orders->load('items', 'payment', 'vendor:id,name'),
            ], 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
