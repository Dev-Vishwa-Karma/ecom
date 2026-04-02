<!-- resources/views/pdf/invoice.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Order #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #ff8c00; margin-bottom: 20px; }
        .details, .product-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details td { padding: 8px; vertical-align: top; border: 1px solid #444; }
        .product-table th, .product-table td { border: 1px solid #444; padding: 8px; text-align: left; }
        .product-table th { background: #2a2a2a; color: white; }
        .section-title { font-weight: bold; background: #2a2a2a; padding: 5px; color: #ff8c00; margin-bottom: 5px; }
        .total { text-align: right; font-weight: bold; }
    </style>
</head>
<body>

<h1>Invoice - Order #{{ $order->id }}</h1>

<!-- Supplier and Customer Details -->
<table class="details">
    <tr>
        <td>
            <div class="section-title">Supplier Details</div>
            <p><strong>Name:</strong> {{ $supplier->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $supplier->email ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $supplier->address ?? 'N/A' }}</p>
        </td>
        <td>
            <div class="section-title">Customer Details</div>
            <p><strong>Name:</strong> {{ $order->customer_name }}</p>
            <p><strong>Email:</strong> {{ $order->email }}</p>
            <p><strong>Address:</strong> {{ $order->address }}</p>
            <p><strong>Payment Mode:</strong> {{ ucfirst($order->payment_mode) }}</p>
        </td>
    </tr>
</table>

<!-- Product Table -->
<table class="product-table">
    <tr>
        <th>#</th>
        <th>Product</th>
        <th>Color</th>
        <th>Size</th>
        <th>Gender</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
    </tr>

    @php
        $product = $order->product; // Access the product relationship
        $variant = $order->variant;
        $total = $variant->price * $order->quantity; 
    @endphp

    <tr>
        <td>1</td>
        <td>{{ $product->name }}</td>
        <td>{{ $variant->color ?? '-' }}</td>
        <td>{{ $variant->size ?? '-' }}</td>
        <td>{{ $variant->gender ?? '-' }}</td>
        <td>{{ $order->quantity }}</td>
        <td>{{ number_format($variant->price, 2) }}</td>
        <td>{{ number_format($total, 2) }}</td>
    </tr>

    <!-- Grand Total -->
    <tr>
        <td colspan="7" class="total">Grand Total</td>
        <td>{{ number_format($total, 2) }}</td>
    </tr>
</table>

<!-- Footer Message -->
<p style="text-align:center; margin-top:20px; color:#ff8c00;">Thank you for your purchase!</p>

</body>
</html>