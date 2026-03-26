@extends('layouts.app')
@section('title', 'Products')
@section('content')
<h3 class="mb-4">Products</h3>
<div class="row">
    @forelse($products as $product)
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-secondary vendor-badge mb-2">{{ $product->vendor->name }}</span>
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="text-success fw-bold">${{ number_format($product->price, 2) }}</p>
                    <p class="text-muted small">Stock: {{ $product->stock }}</p>
                    @auth
                        @if($product->stock > 0)
                            <form action="{{ route('cart.store') }}" method="POST" class="mt-auto">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control" aria-label="Quantity for {{ $product->name }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-cart-plus"></i> Add
                                    </button>
                                </div>
                            </form>
                        @else
                            <span class="badge bg-danger mt-auto">Out of Stock</span>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm mt-auto">Login to Buy</a>
                    @endauth
                </div>
            </div>
        </div>
    @empty
        <p>No products available.</p>
    @endforelse
</div>
{{ $products->links() }}
@endsection
