<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - Order #{{ $order->id }}</title>

    <style>
        body {
            font-family: Arial;
            font-size: 12px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #ff8c00;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #444;
            padding: 8px;
        }

        th {
            background: #222;
            color: #fff;
        }

        .section-title {
            background: #222;
            color: #ff8c00;
            padding: 6px;
            font-weight: bold;
        }

        .total {
            text-align: right;
            font-weight: bold;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>

<h1>Invoice - Order #{{ $order->id }}</h1>

{{-- ================= HEADER ================= --}}
<table>
    <tr>
        <td>
            <div class="section-title">Supplier</div>
            <p><strong>Name:</strong> {{ $supplier->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $supplier->email ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $supplier->phone ?? 'N/A' }}</p>
        </td>

        <td>
            <div class="section-title">Customer</div>
            <p><strong>Name:</strong> {{ $order->customer_name }}</p>
            <p><strong>Email:</strong> {{ $order->email }}</p>
            <p><strong>Address:</strong> {{ $order->address }}</p>
        </td>
    </tr>
</table>

{{-- ================= ITEMS ================= --}}
<table>
    <tr>
        <th>#</th>
        <th>Product</th>
        <th>Variant</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Total</th>
    </tr>

    @php $grandTotal = 0; @endphp

    @foreach($items as $i => $item)

        @php
            $product = $item->product;
            $variant = $item->variant;

            $price = $item->price ?? 0;
            $qty   = $item->quantity ?? 0;
            $total = $item->total_price ?? ($price * $qty);

            $grandTotal += $total;
        @endphp

        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $product->name ?? 'N/A' }}</td>
            <td>
                {{ $variant->color ?? '-' }} |
                {{ $variant->size ?? '-' }} |
                {{ $variant->gender ?? '-' }}
            </td>
            <td>{{ $qty }}</td>
            <td>{{ number_format($price, 2) }}</td>
            <td>{{ number_format($total, 2) }}</td>
        </tr>

    @endforeach

    <tr>
        <td colspan="5" class="total">Grand Total</td>
        <td><strong>{{ number_format($grandTotal, 2) }}</strong></td>
    </tr>
</table>

{{-- ================= FOOTER ================= --}}
<p style="text-align:center; margin-top:20px; color:#ff8c00;">
    Thank you for your purchase!
</p>

</body>
</html>