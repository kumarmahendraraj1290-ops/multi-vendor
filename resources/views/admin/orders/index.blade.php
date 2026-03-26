@extends('layouts.app')
@section('title', 'Admin - All Orders')
@section('content')
<h3 class="mb-4"><i class="bi bi-shield-lock"></i> Admin - All Orders</h3>

<form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 mb-4">
    <div class="col-md-3">
        <select name="vendor_id" class="form-select form-select-sm" aria-label="Filter by vendor">
            <option value="">All Vendors</option>
            @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select form-select-sm" aria-label="Filter by status">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="text" name="customer" class="form-control form-control-sm" placeholder="Customer name or email" value="{{ request('customer') }}" aria-label="Filter by customer">
    </div>
    <div class="col-md-3">
        <button class="btn btn-sm btn-primary" type="submit">Filter</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Vendor</th>
                <th>Total</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->user->name }}<br><small class="text-muted">{{ $order->user->email }}</small></td>
                <td>{{ $order->vendor->name }}</td>
                <td>${{ number_format($order->total, 2) }}</td>
                <td>
                    @if($order->status === 'confirmed')
                        <span class="badge bg-success">Confirmed</span>
                    @elseif($order->status === 'cancelled')
                        <span class="badge bg-danger">Cancelled</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </td>
                <td>
                    @if($order->payment)
                        <span class="badge {{ $order->payment->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ ucfirst($order->payment->status) }}
                        </span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>{{ $order->created_at->format('M d, Y') }}</td>
                <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $orders->withQueryString()->links() }}</div>
@endsection
