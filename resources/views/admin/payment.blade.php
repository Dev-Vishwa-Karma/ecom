@extends('layouts.app')

@section('title', 'Stripe Dashboard')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.dashboard-cards {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}
.dashboard-card {
    flex: 1;
    min-width: 250px;
    background: #1e1e1e;
    border-radius: 12px;
    padding: 20px;
}
.card-pending { border-left: 5px solid #f39c12; }
.card-available { border-left: 5px solid #2ecc71; }

.table-dark-custom {
    background: #1e1e1e;
    border-radius: 10px;
    overflow: hidden;
}

.table-dark-custom th {
    background: #111;
    color: #ff8c00;
}

.badge-status {
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    color: #fff;
}
.success { background: #2ecc71; }
.processing { background: #f39c12; }
.failed { background: #e74c3c; }

.pending-text { color: #f39c12; font-weight: bold; }
.available-text { color: #2ecc71; font-weight: bold; }
</style>

<div class="container-fluid">

    <!-- TOP CARDS -->
    <div class="dashboard-cards">
        <div class="dashboard-card card-pending">
            <h6>Pending Balance</h6>
            <h2>₹{{ number_format($pending,2) }}</h2>
        </div>

        <div class="dashboard-card card-available">
            <h6>Available Balance</h6>
            <h2>₹{{ number_format($available,2) }}</h2>
        </div>
    </div>

   
    <!-- TABLE -->
    <div class="mt-4 table-responsive">
<form method="GET" action="{{ route('admin.transactions') }}" class="mb-3 d-flex gap-2">

    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">

    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">

    <input type="text" name="order_id"
        placeholder="Order ID"
        value="{{ request('order_id') }}"
        class="form-control">

    <button type="submit" class="rounded">Apply</button>

    <!-- DOWNLOAD BUTTON -->
    <button type="button" class="rounded" data-bs-toggle="modal" data-bs-target="#downloadModal">
        Download
    </button>

</form>
        <table class="table table-dark table-hover table-dark-custom">

            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Amount</th>
                    <th>Stripe Fee</th>
                    <th>Net Amount</th>
                    <th>Payment</th>
                    <th>Balance</th>
                    <th>Order Date</th>
                    <th>Available On</th>
                </tr>
            </thead>

            <tbody>
                @forelse($txns as $txn)
                <tr>
                    <!-- <td>#{{ $txn['order_id'] }}</td> -->
                     <td>#<a href="{{ route('seller-invoice', $txn['order_id']) }}" >{{ $txn['order_id'] }}</a></td>

                    <td>₹{{ number_format($txn['amount'],2) }}</td>

                    <td style="color:#e74c3c;">
                        -₹{{ number_format($txn['fee'],2) }}
                    </td>

                    <td style="color:#2ecc71;">
                        ₹{{ number_format($txn['net'],2) }}
                    </td>

                    <!-- PAYMENT STATUS -->
                    <td>
                         {{ strtoupper($txn['payment_status']) }}
                    </td>

                    <!-- BALANCE STATUS -->
                    <td class="{{ $txn['balance_status'] == 'pending' ? 'pending-text' : 'available-text' }}">
                        {{ strtoupper($txn['balance_status']) }}
                    </td>

                    <td>{{ $txn['order_date'] }}</td>

                    <td>{{ $txn['available_on'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">No transactions found</td>
                </tr>
                @endforelse
            </tbody>

        </table>
        <div class=" pagination">
    {{ $txns->links() }}
</div>
    </div>

</div>

<!-- DOWNLOAD MODAL -->
<div class="modal fade" id="downloadModal" tabindex="-1">
    <div class="modal-dialog">
<form method="GET" action="{{ route('admin.stripe.export') }}" target="_blank">
                <div class="modal-content bg-dark text-white">

                <div class="modal-header">
                    <h5 class="modal-title">Download Statement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control mb-3" required>

                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control mb-3" required>

                    <!-- <label>Order ID (Optional)</label>
                    <input type="text" name="order_id" class="form-control"> -->

                </div>

                <div class="modal-footer">
                    <button class="rounded">Download CSV</button>
                </div>

            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection