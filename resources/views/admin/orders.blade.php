@extends('layouts.app')

@section('title', 'All Orders')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

@section('content')

<div class="table-container">

<h2 style="color:#ff8c00; text-align:center; margin-bottom:30px;">All Orders</h2>

<!-- Filters -->
<div class="filters">
    <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search orders..." class="filter-input">

        <select name="status" class="filter-select">
            <option value="">All Status</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
            <option value="processing" {{ request('status')=='processing'?'selected':'' }}>Processing</option>
            <option value="dispatched" {{ request('status')=='dispatched'?'selected':'' }}>Dispatched</option>
            <option value="delivered" {{ request('status')=='delivered'?'selected':'' }}>Delivered</option>
            <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
        </select>

        <button type="submit" style="padding:8px 16px; background:#ff8c00; color:black; border:none; border-radius:6px; cursor:pointer;">
            Filter
        </button>
    </form>
</div>

<!-- Orders Table -->
<table class="orders-table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Payment Mode</th>
            <th>Payment Status</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Order Date</th>
            <th>Invoice</th>
        </tr>
    </thead>

    <tbody>
        @forelse($orders as $order)

     @php
    $total = $order->items
        ->where('seller_id', auth()->id())
        ->sum(function($item){
            return ($item->price ?? 0) * ($item->quantity ?? 1);
        });
@endphp

        <tr>
            <!-- CUSTOMER -->
             <td>{{ $order->id }}</td>
            <td>{{ $order->customer_name ?? 'N/A' }}</td>

            <!-- EMAIL -->
            <td>{{ $order->email ?? 'N/A' }}</td>

            <!-- PAYMENT MODE -->
            <td>
                <span style="
                    padding:5px 10px;
                    font-weight:bold;
                ">
                
                {{ strtoupper($order->payment_mode ?? 'N/A') }}
                    
                </span>
                
            </td>
            <td>
                <span style=" padding:5px 10px; font-weight:bold; text-align:center;">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </td>

            <!-- TOTAL -->
            <td>₹{{ number_format($total,2) }}</td>

            <!-- STATUS -->
           @php
    $sellerOrder = $order->sellerOrders
        ->where('seller_id', auth()->id())
        ->first();
@endphp

<td>
@if($sellerOrder)

    @if($sellerOrder->status === 'cancelled')
        
        <span style="color:red; font-weight:bold;">
            Cancelled
        </span>

    @else

        <select class="status-select"
                data-order-id="{{ $order->id }}"
                style="padding:5px;">

            <option value="pending" {{ $sellerOrder->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ $sellerOrder->status == 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="dispatched" {{ $sellerOrder->status == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
            <option value="delivered" {{ $sellerOrder->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled" {{ $sellerOrder->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>

        </select>

    @endif

@else
    <span>No Items</span>
@endif</td>


            <!-- DATE -->
            <td>
                {{ optional($order->order_date)->format('d M Y h:i A') ?? 'N/A' }}
            </td>

            <!-- INVOICE -->
            <td>
                @if(auth()->check())
                    <a href="{{ route('seller-invoice', $order->id) }}" class="view-invoice-btn">
                        View Invoice
                    </a>
                @endif
            </td>
        </tr>

        @empty
        <tr>
            <td colspan="7" style="text-align:center; color:#aaa;">
                No orders found.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- PAGINATION -->
<div class="pagination">
    {{ $orders->links() }}
</div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {

            const orderId = this.dataset.orderId;
            const newStatus = this.value;

            fetch('{{ route("admin.orders.update-status") }}', {
                method: 'POST',
                headers: {
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}',
                    'Accept':'application/json'
                },
                body: JSON.stringify({
                    order_id:  orderId,
                    status: newStatus
                })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                location.reload();
            })
            .catch(() => alert('Error updating status'));

        });
    });

});
</script>

@endsection