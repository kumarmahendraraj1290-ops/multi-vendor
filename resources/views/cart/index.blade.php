@extends('layouts.app')
@section('title', 'My Cart')
@section('content')
<h3 class="mb-4"><i class="bi bi-cart3"></i> My Cart</h3>

@if($grouped->isEmpty())
    <div class="alert alert-info">Your cart is empty. <a href="{{ route('products.index') }}">Browse products</a></div>
@else
    @php $grandTotal = 0; @endphp
    @foreach($grouped as $vendorId => $items)
        @php
            $vendor = $items->first()->product->vendor;
            $vendorTotal = $items->sum(fn($i) => $i->quantity * $i->product->price);
            $grandTotal += $vendorTotal;
        @endphp
        <div class="card mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-shop-window"></i> {{ $vendor->name }}</strong>
                <span class="text-muted">Subtotal: ${{ number_format($vendorTotal, 2) }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th style="width:140px">Qty</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>${{ number_format($item->product->price, 2) }}</td>
                            <td>
                                <form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex gap-1">
                                    @csrf @method('PUT')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control form-control-sm" style="width:70px" aria-label="Quantity">
                                    <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-arrow-repeat"></i></button>
                                </form>
                            </td>
                            <td>${{ number_format($item->quantity * $item->product->price, 2) }}</td>
                            <td>
                                <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <div class="d-flex justify-content-between align-items-center mt-3">
        <h5>Grand Total: <span class="text-success">${{ number_format($grandTotal, 2) }}</span></h5>
        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <button class="btn btn-success btn-lg">
                <i class="bi bi-bag-check"></i> Checkout
            </button>
        </form>
    </div>
@endif
@endsection
