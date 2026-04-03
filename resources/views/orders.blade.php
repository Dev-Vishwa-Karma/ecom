@extends('layouts.app')

@section('title', 'My Orders')
<style>
.star-rating {
    display: flex;
    gap: 5px;
    font-size: 28px;
    cursor: pointer;
    color: #ccc; /* empty stars */
}

.star-rating .star.filled {
    color: #ffcc00; /* filled stars */
}
</style>

<link rel="stylesheet" href="{{ asset('css/order-table.css') }}">

@section('content')



<div class="orders-container">

<h2 style="color:#ff8c00;text-align:center;margin-bottom:30px;">My Orders</h2>

@if(session('success'))<div style="background:#1a3a1a;color:#4dff88;padding:12px;border-radius:8px;margin-bottom:20px;text-align:center;">
{{ session('success') }}</div>
@endif


@forelse($orders as $order)

<div class="order-card">

<div class="order-header">
<h4 style="color:#fff;margin:0;">Order #{{ $order->id }}</h4>
<span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
<div class="d-flex flex-column gap-2">
    <a href="{{ route('invoice', $order->id) }}" class="view-invoice-btn">View Invoice</a>  
               
@if ($order->status === 'delivered')
    <a href="#" class="view-invoice-btn" data-bs-toggle="modal" data-bs-target="#rateProductModal-{{ $order->product->id }}">
        Rate Product
    </a>
@endif
</div>

</div>


<div class="order-info">

<p><strong>Product:</strong>{{ $order->product->name ?? 'N/A' }}</p>

<p><strong>Variant:</strong>{{ $order->variant->color ?? '-' }},{{ $order->variant->size ?? '-' }},{{ $order->variant->gender ?? '-' }}</p>

<p><strong>Price Per Item:</strong>₹ {{ number_format($order->price,2) }}</p>

<p><strong>Quantity:</strong>{{ $order->quantity }}</p>

<p><strong>Total Price:</strong>₹ {{ number_format($order->total_price,2) }}</p>

<p><strong>Payment Mode:</strong>{{ ucfirst($order->payment_mode) }}</p>

<p><strong>Order Date:</strong>{{ $order->order_date?->format('d M Y h:i A') }}</p>

<p><strong>Dispatch Date:</strong>{{ $order->dispatch_date?->format('d M Y') ?? 'Not yet dispatched' }}</p>

<p><strong>Customer Name:</strong>{{ $order->customer_name }}</p>

<p><strong>Mobile:</strong>{{ $order->mobile }}</p>

<p><strong>Email:</strong>{{ $order->email }}</p>

</div>


<div class="address-box"><strong>Delivery Address:</strong><br>{{ $order->address }}</div>

</div>

@empty

<div style="text-align:center;padding:60px;background:#1e1e1e;border-radius:12px;color:#aaa;">
<p style="font-size:1.3em;margin-bottom:15px;">No orders found yet</p>
<a href="{{ route('all-products') }}" style="color:#ff8c00;font-weight:bold;text-decoration:none;">Start Shopping →</a>
</div>

@endforelse
@foreach ($orders as $order)
    @if ($order->status === 'delivered')
        @include('product-rate-model', [
            'productId' => $order->product->id,
            'variantId' => $order->variant->id ?? null,
            'ratingValue' => 0, 
            'commentValue' => '', 
        ])
    @endif
@endforeach

<div style="" class="pagination">
{{ $orders->links() }}
</div>

</div>


@endsection



