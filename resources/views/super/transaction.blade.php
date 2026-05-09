@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">💰 Super Admin Transactions</h2>

    {{-- SUMMARY CARDS --}}
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card p-3 bg-success text-white">
                <h5>Inflow</h5>
                <h3>{{ $totalInflow }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 bg-primary text-white">
                <h5>Transfers</h5>
                <h3>${{ $totalTransfer }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 bg-danger text-white">
                <h5>Refunds</h5>
                <h3>{{ $totalRefund }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 bg-dark text-white">
                <h5>Net</h5>
                <h3>{{ $net }}</h3>
            </div>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Order</th>
                        <th>Type</th>
                        <th>Seller</th>
                        <th>Amount</th>
                        <th>Fee</th>
                        <th>Net</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($transactions as $txn)
                        <tr>
                            <td>{{ $txn['id'] }}</td>
                            <td>{{ $txn['order_id'] }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $txn['type'] }}
                                </span>
                            </td>
                            <td>{{ $txn['seller'] }}</td>
                            <td>{{ $txn['amount'] }}</td>
                            <td>{{ $txn['fee'] }}</td>
                            <td>{{ $txn['net'] }}</td>
                            <td>{{ $txn['created'] }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection