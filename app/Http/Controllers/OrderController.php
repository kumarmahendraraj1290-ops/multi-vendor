<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /** Customer: view own orders */
    public function myOrders(): JsonResponse
    {
        $orders = auth()->user()->orders()
            ->with('items', 'payment', 'vendor:id,name')
            ->latest()
            ->paginate(15);

        return response()->json($orders);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return response()->json(
            $order->load('items', 'payment', 'vendor:id,name', 'user:id,name,email')
        );
    }
}
