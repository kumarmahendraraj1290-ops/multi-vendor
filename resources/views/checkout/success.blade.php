@extends('layouts.app')
@section('title', 'Checkout Successful')
@section('content')
<div class="text-center py-5">
    <i class="bi bi-check-circle text-success" style="font-size:4rem"></i>
    <h3 class="mt-3">Checkout Successful!</h3>
    <p class="text-muted">Your cart has been split into {{ $orders->count() }} order(s) by vendor.</p>
</div>

@foreach($orders as $order)
    <div class="card mb-3">
        <div class="card-header bg-white d-flex justify-content-between">
            <strong>Order #{{ $order->id }} — {{ $order->vendor->name }}</strong>
            <span class="badge bg-success">Paid</span>
        </div>
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>${{ number_format($item->product_price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr><td colspan="3" class="text-end fw-bold">Total</td><td class="fw-bold">${{ number_format($order->total, 2) }}</td></tr>
            </tfoot>
        </table>
    </div>
@endforeach

<div class="text-center mt-4">
    <a href="{{ route('orders.index') }}" class="btn btn-primary">View My Orders</a>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary ms-2">Continue Shopping</a>
</div>
@endsection
