<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SellerPayout;
use Stripe\Stripe;
use Stripe\BalanceTransaction;

class SuperAdminTransactionController extends Controller
{
    public function index(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $transactions = BalanceTransaction::all([
            'limit' => 100,
        ]);

        $data = [];

        $totalInflow = 0;
        $totalTransfer = 0;
        $totalRefund = 0;

        foreach ($transactions->data as $txn) {

            $type = $txn->type;

            $orderId = $txn->source->metadata->order_id ?? '-';
            $sellerId = null;
            $sellerName = '-';

            // DEFAULT STATUS
            $label = 'unknown';

            // PAYMENT (customer → platform)
            if ($type === 'charge' || $type === 'payment') {
                $label = 'Payment Received';
                $totalInflow += $txn->amount / 100;
            }

            // TRANSFER (platform → seller)
            if ($type === 'transfer') {
                $label = 'Seller Transfer';
                $totalTransfer += abs($txn->amount / 100);

                $sellerId = $txn->source->metadata->seller_id ?? null;

                if ($sellerId) {
                    $seller = \App\Models\User::find($sellerId);
                    $sellerName = $seller?->name ?? '-';
                }
            }

            // REFUND (platform → customer)
            if ($type === 'refund') {
                $label = 'Refund Issued';
                $totalRefund += abs($txn->amount / 100);
            }

            $data[] = [
                'id' => $txn->id,
                'order_id' => $orderId,
                'type' => $label,
                'amount' => $txn->amount / 100,
                'fee' => $txn->fee / 100,
                'net' => $txn->net / 100,
                'currency' => strtoupper($txn->currency),
                'seller' => $sellerName,
                'created' => date('d M Y H:i', $txn->created),
            ];
        }

        return view('super.transaction', [
            'transactions' => $data,
            'totalInflow' => $totalInflow,
            'totalTransfer' => $totalTransfer,
            'totalRefund' => $totalRefund,
            'net' => $totalInflow - $totalTransfer - $totalRefund,
        ]);
    }
}