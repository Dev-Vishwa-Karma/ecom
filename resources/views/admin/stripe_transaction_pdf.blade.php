<!DOCTYPE html>
<html>
<head>
    <title>Stripe Statement</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #eee; }
        tfoot td { font-weight: bold; }
    </style>
</head>
<body>

<h3>Stripe Statement</h3>
<p>Seller Name : {{ $sellername ?? 'N/A' }}</p>
<p>Seller Email: {{ $selleremail ?? 'N/A' }}</p>
<p>From: {{ $from_date ?? 'N/A' }} To: {{ $to_date ?? 'N/A' }}</p>

<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Amount (₹)</th>
            <th>Fee (₹)</th>
            <th>Net (₹)</th>
            <th>Payment Status</th>
            <th>Balance Status</th>
            <th>Order Date</th>
            <th>Available On</th>
        </tr>
    </thead>

    <tbody>
        @foreach($txns as $txn)
        <tr>
            <td>{{ $txn['order_id'] ?? 'SYSTEM / ADJUSTMENT' }}</td>
            <td>{{ number_format($txn['amount'] ?? 0, 2) }}</td>
            <td>{{ number_format($txn['fee'] ?? 0, 2) }}</td>
            <td>{{ number_format($txn['net'] ?? 0, 2) }}</td>
            <td>{{ strtoupper($txn['payment_status'] ?? 'N/A') }}</td>
            <td>{{ strtoupper($txn['balance_status'] ?? 'N/A') }}</td>
            <td>{{ $txn['order_date'] ?? 'N/A' }}</td>
            <td>{{ $txn['available_on'] ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td>Total</td>
            <td>{{ number_format($totals['amount'], 2) }}</td>
            <td>{{ number_format($totals['fee'], 2) }}</td>
            <td>{{ number_format($totals['net'], 2) }}</td>
            <td colspan="4"></td>
        </tr>
    </tfoot>
</table>

</body>
</html>