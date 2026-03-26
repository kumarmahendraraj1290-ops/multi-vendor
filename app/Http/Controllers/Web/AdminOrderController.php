<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('items', 'payment', 'vendor:id,name', 'user:id,name,email');

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer')) {
            $search = $request->customer;
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $orders = $query->latest()->paginate(20);
        $vendors = Vendor::select('id', 'name')->get();

        return view('admin.orders.index', compact('orders', 'vendors'));
    }

    public function show(Order $order): View
    {
        $order->load('items', 'payment', 'vendor:id,name', 'user:id,name,email');

        return view('orders.show', compact('order'));
    }
}
