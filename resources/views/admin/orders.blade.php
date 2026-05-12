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

@php
    $cancelledItem = $order->items
        ->where('seller_id', auth()->id())
        ->where('status', 'cancelled')
        ->first();

    $cancelledBy = $cancelledItem?->cancellation?->cancelled_by_type;
@endphp

<div>
    <span style="font-size:14px ; color: red">
        Cancelled by
    </span>


        <strong style="color : red">

            @if($cancelledBy === 'seller')
                You

            @elseif($cancelledBy === 'customer')
                Customer
           

            @else
                System
            @endif

        </strong>
</br>
        @if($cancelledItem?->cancellation?->created_at)
            <div style="font-size:11px;">
                on {{ $cancelledItem->cancellation->created_at->format('d M Y h:i A') }}
                 </br>
           Reason : {{ $cancelledItem?->cancellation?->reason }}
            </div>
        @endif

</div>


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

<!-- CANCEL MODAL -->
<div id="cancelModal" style="
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.6);
z-index:9999;
align-items:center;
justify-content:center;
">

    <div style="
        background:#1e1e1e;
        width:400px;
        padding:20px;
        border-radius:10px;
    ">

        <h3 style="color:#ff8c00;margin-bottom:15px;">
            Cancellation Reason
        </h3>

        <textarea
            id="cancelReason"
            placeholder="Write reason..."
            style="
                width:100%;
                height:120px;
                padding:10px;
                border-radius:8px;
                border:none;
                resize:none;
            "
        ></textarea>

        <div style="margin-top:15px;display:flex;gap:10px;justify-content:end;">

            <button id="closeModalBtn"
                style="
                    padding:8px 16px;
                    border:none;
                    border-radius:6px;
                    cursor:pointer;
                ">
                Close
            </button>

            <button id="confirmCancelBtn"
                style="
                    padding:8px 16px;
                    background:red;
                    color:white;
                    border:none;
                    border-radius:6px;
                    cursor:pointer;
                ">
                Confirm Cancel
            </button>

        </div>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    let selectedOrderId = null;
    let selectedSelect = null;

    const modal = document.getElementById('cancelModal');
    const closeBtn = document.getElementById('closeModalBtn');
    const confirmBtn = document.getElementById('confirmCancelBtn');

    document.querySelectorAll('.status-select').forEach(select => {

        select.addEventListener('change', function() {

            const orderId = this.dataset.orderId;
            const newStatus = this.value;

            // IF CANCELLED -> OPEN MODAL
            if(newStatus === 'cancelled')
            {
                selectedOrderId = orderId;
                selectedSelect = this;

                modal.style.display = 'flex';

                return;
            }

            // NORMAL STATUS UPDATE
            updateOrderStatus(orderId, newStatus);
        });

    });

    // CLOSE MODAL
    closeBtn.addEventListener('click', function(){

        modal.style.display = 'none';

        if(selectedSelect){
            selectedSelect.value = 'pending';
        }

    });

    // CONFIRM CANCEL
    confirmBtn.addEventListener('click', function(){

        const reason = document.getElementById('cancelReason').value;

        if(!reason){
            alert('Please write cancellation reason');
            return;
        }

        fetch('{{ route("admin.orders.update-status") }}', {

            method: 'POST',

            headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}',
                'Accept':'application/json'
            },

            body: JSON.stringify({
                order_id: selectedOrderId,
                status: 'cancelled',
                reason: reason
            })

        })
        .then(res => res.json())
        .then(data => {

            alert(data.message);

            modal.style.display = 'none';

            location.reload();

        })
        .catch(() => {

            alert('Error updating status');

        });

    });

    // COMMON FUNCTION
    function updateOrderStatus(orderId, status)
    {
        fetch('{{ route("admin.orders.update-status") }}', {

            method: 'POST',

            headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}',
                'Accept':'application/json'
            },

            body: JSON.stringify({
                order_id: orderId,
                status: status
            })

        })
        .then(res => res.json())
        .then(data => {

            alert(data.message);

            location.reload();

        })
        .catch(() => {

            alert('Error updating status');

        });
    }

});


</script>

@endsection