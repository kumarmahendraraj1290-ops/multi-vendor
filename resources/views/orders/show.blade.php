@extends('layouts.app')
@section('title', 'Order #' . $order->id)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Order #{{ $order->id }}</h3>
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header bg-white"><strong>Items</strong></div>
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
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header bg-white"><strong>Details</strong></div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Vendor</span><strong>{{ $order->vendor->name }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Status</span>
                    @if($order->status === 'confirmed')
                        <span class="badge bg-success">Confirmed</span>
                    @elseif($order->status === 'cancelled')
                        <span class="badge bg-danger">Cancelled</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </li>
                @if($order->payment)
                <li class="list-group-item d-flex justify-content-between">
                    <span>Payment</span>
                    <span class="badge {{ $order->payment->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ ucfirst($order->payment->status) }}
                    </span>
                </li>
                @endif
                <li class="list-group-item d-flex justify-content-between">
                    <span>Date</span><span>{{ $order->created_at->format('M d, Y H:i') }}</span>
                </li>
                @if($order->user)
                <li class="list-group-item d-flex justify-content-between">
                    <span>Customer</span><span>{{ $order->user->name }}</span>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection
