@extends('layouts.app')

@section('title', 'Seller Invoice - Order #' . $order->id)

@section('content')

<style>
h1 { text-align:center; color:#ff8c00; margin-bottom:20px; }

.details, .product-table {
    width:100%;
    border-collapse:collapse;
    margin-bottom:20px;
}

.details td, .product-table th, .product-table td {
    border:1px solid #444;
    padding:8px;
}

.product-table th {
    background:#2a2a2a;
    color:#fff;
}

.section-title {
    font-weight:bold;
    background:#2a2a2a;
    color:#ff8c00;
    padding:5px;
}

.total {
    text-align:right;
    font-weight:bold;
}
.downloadBtn {
    display: block;
    padding: 10px 20px;
    background: #ff8c00;
    color: black;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    right: 20px;
    position: absolute;
}
.downloadDropdown {
    width: 100px;
    display: none;
    margin-top: 50px;
    background: #2a2a2a;
    position: absolute ;
    right: 25px;
    border: 1px solid #444;
    border-radius: 6px;
    padding: 5px 2px;
}
.downloadDropdown a {
    display: block;
    padding: 5px;
    color: white;
    text-decoration: none;
}
</style>
<button id="downloadBtn" class="downloadBtn">Download</button>

<div id="dropdown" class="downloadDropdown">
    <a href="{{ route('invoice.download', ['order' => $order->id]) }}" style="border-bottom:1px solid #444;">
       Excel ➞
    </a>
    <a href="{{ route('invoice.download', ['order' => $order->id, 'type' => 'pdf']) }}">
       PDF ➞
    </a>
</div>

<h1>Seller Invoice - Order #{{ $order->id }}</h1>

{{-- SELLER + CUSTOMER INFO --}}
<table class="details">
    <tr>
        <td>
            <div class="section-title">Seller Details</div>
            <p><strong>Name:</strong> {{ $supplier->name }}</p>
            <p><strong>Email:</strong> {{ $supplier->email }}</p>
            <p><strong>Address:</strong> {{ $supplier->address }}</p>
        </td>

        <td>
            <div class="section-title">Customer Details</div>
            <p><strong>Name:</strong> {{ $order->customer_name }}</p>
            <p><strong>Email:</strong> {{ $order->email }}</p>
            <p><strong>Address:</strong> {{ $order->address }}</p>
        </td>
    </tr>
</table>

{{-- ITEMS TABLE --}}
<table class="product-table">
    <tr>
        <th>Product</th>
        <th>Color</th>
        <th>Size</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Status</th>
        <th>Total</th>
    </tr>

    @php $grand = 0; @endphp

    @foreach($items as $item)

        @php
            $grand += $item->total_price;
        @endphp

        <tr>
            <td>{{ $item->product->name ?? 'N/A' }}</td>
            <td>{{ $item->variant->color ?? '-' }}</td>
            <td>{{ $item->variant->size ?? '-' }}</td>
            <td>{{ $item->quantity }}</td>
            <td>₹{{ number_format($item->price,2) }}</td>
            <td>{{ $item->status }}
            <td>₹{{ number_format($item->total_price,2) }}</td>
        </tr>

    @endforeach

    <tr>
        <td colspan="5" class="total">Grand Total</td>
        <td>₹{{ number_format($grand,2) }}</td>
    </tr>
</table>
<p style="text-align:center; margin-top:20px; color:#ff8c00;">Thank you for your purchase!</p>

<div class="d-flex">
    <button onclick="window.history.back()" style="display:block; margin: 0 auto 20px; padding:10px 20px; background:#ff8c00; color:black; border:none; border-radius:6px; cursor:pointer;">Back</button>

    <button onclick="window.print()" style="display:block; margin: 0 auto 20px; padding:10px 20px; background:#ff8c00; color:black; border:none; border-radius:6px; cursor:pointer;">Print</button>
</div>

<script>
const downloadBtn = document.getElementById('downloadBtn');
const dropdown = document.getElementById('dropdown');

downloadBtn.addEventListener('click', function () {
    dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
});

const links = dropdown.getElementsByTagName('a');
for (let link of links) {
    link.addEventListener('click', function () {
        dropdown.style.display = "none";
    });
}
</script>

@endsection