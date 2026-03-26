@extends('layouts.app')
@section('title', 'My Orders')
@section('content')
<h3 class="mb-4">My Orders</h3>

@forelse($orders as $order)
    <div class="card mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <strong>Order #{{ $order->id }}</strong>
                <span class="badge bg-secondary ms-2">{{ $order->vendor->name }}</span>
                @if($order->status === 'confirmed')
                    <span class="badge bg-success ms-1">Confirmed</span>
                @elseif($order->status === 'cancelled')
                    <span class="badge bg-danger ms-1">Cancelled</span>
                @else
                    <span class="badge bg-warning text-dark ms-1">Pending</span>
                @endif
            </div>
            <span class="text-muted">{{ $order->created_at->format('M d, Y H:i') }}</span>
        </div>
        <div class="card-body">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View Details</a>
            <span class="float-end fw-bold text-success">${{ number_format($order->total, 2) }}</span>
        </div>
    </div>
@empty
    <div class="alert alert-info">No orders yet. <a href="{{ route('products.index') }}">Start shopping</a></div>
@endforelse

{{ $orders->links() }}
@endsection
