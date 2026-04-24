<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Order;

class StripePaymentService
{
    public function getBalance($accountId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('STRIPE_SECRET'),
            'Stripe-Account' => $accountId,
        ])->get('https://api.stripe.com/v1/balance');

        $data = $response->json();

        $pending = 0;
        $available = 0;

        foreach ($data['pending'] ?? [] as $bal) {
            if ($bal['currency'] === 'usd') {
                $pending += $bal['amount'];
            }
        }

        foreach ($data['available'] ?? [] as $bal) {
            if ($bal['currency'] === 'usd') {
                $available += $bal['amount'];
            }
        }

        return [
            'pending' => $pending / 100,
            'available' => $available / 100
        ];
    }

    public function getTransactions($accountId, $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $params = [
            'limit' => 100,
            'expand' => ['data.source']
        ];

        if ($request->from_date || $request->to_date) {
            $params['created'] = [];

            if ($request->from_date) {
                $params['created']['gte'] = strtotime($request->from_date);
            }

            if ($request->to_date) {
                $params['created']['lte'] = strtotime($request->to_date . ' 23:59:59');
            }
        }

        $transactions = \Stripe\BalanceTransaction::all(
            $params,
            ['stripe_account' => $accountId]
        );

        $txns = [];

        foreach ($transactions->data as $txn) {

            $orderId = '-';
            $orderDate = '-';
            $paymentStatus = '-';

            $amount = $txn->amount / 100;
            $fee = $txn->fee / 100;
            $net = $txn->net / 100;
            $availableOn = date('d M Y', $txn->available_on);

            $source = $txn->source;

            if ($source && $source->object === 'charge') {

                $transferId = $source->source_transfer ?? null;

                if ($transferId) {
                    try {
                        $transfer = \Stripe\Transfer::retrieve(
                            $transferId,
                            [],
                            ['stripe_account' => $accountId]
                        );

                        $orderId = $transfer->metadata->order_id ?? '-';

                    } catch (\Exception $e) {}
                }
            }

            if ($source && $source->object === 'payment_intent') {
                $paymentStatus = $source->status;
                $orderId = $source->metadata->order_id ?? '-';
            }

            if ($orderId !== '-') {
                $order = Order::find($orderId);

                if ($order) {
                    $orderDate = date('d M Y', strtotime($order->order_date));
                    $paymentStatus = $order->payment_status ?? $paymentStatus;
                }
            }

            if ($request->order_id && $orderId != $request->order_id) {
                continue;
            }

            $txns[] = [
                'order_id' => $orderId,
                'amount' => $amount,
                'fee' => $fee,
                'net' => $net,
                'payment_status' => $paymentStatus,
                'balance_status' => $txn->status,
                'order_date' => $orderDate,
                'available_on' => $availableOn,
            ];
        }

        return $txns;
    }
}