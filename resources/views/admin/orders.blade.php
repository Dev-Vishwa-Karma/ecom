@extends('layouts.app')

@section('title', 'All Orders')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

@section('content')
<style>

</style>

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
        <button type="submit" style="padding:8px 16px; background:#ff8c00; color:black; border:none; border-radius:6px; cursor:pointer;">Filter</button>
    </form>
</div>

<!-- Orders Table -->
<table class="orders-table">
    <thead>
        <tr>
            <!-- <th>ID</th> -->
            <th>Customer</th>
            <th>Email</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Order Date</th>
            <th>Invoice</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
        <tr>
            <!-- <td>{{ $order->id }}</td> -->
            <td>{{ $order->customer_name }}</td>
            <td>{{ $order->email }}</td>
            <td>₹{{ number_format($order->total_price,2) }}</td>
            <td>
                <select class="status-select" data-order-id="{{ $order->id }}">
                    <option value="pending" {{ $order->status=='pending'?'selected':'' }}>Pending</option>
                    <option value="processing" {{ $order->status=='processing'?'selected':'' }}>Processing</option>
                    <option value="dispatched" {{ $order->status=='dispatched'?'selected':'' }}>Dispatched</option>
                    <option value="delivered" {{ $order->status=='delivered'?'selected':'' }}>Delivered</option>
                    <option value="cancelled" {{ $order->status=='cancelled'?'selected':'' }}>Cancelled</option>
                </select>
            </td>
            <td>{{ $order->order_date->format('d M Y h:i A') }}</td>
            <td>
                @if(auth()->check())
<a href="{{ route('invoice', $order->id) }}" class="view-invoice-btn">View Invoice</a>   
@endif
</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center; color:#aaa;">No orders found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination">
    {{ $orders->links() }}
</div>

</div>

<script>
    // Change order status via dropdown
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const orderId = this.dataset.orderId;
            const newStatus = this.value;

            fetch('{{ route("admin.orders.update-status") }}', {
                method: 'POST',
                headers: {
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify({ order_id: orderId, status: newStatus })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                location.reload();
            })
            .catch(err => alert('Error updating status'));
        });
    });
</script>

@endsection