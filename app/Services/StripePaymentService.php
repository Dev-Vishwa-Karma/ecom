<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Order;

class StripePaymentService
{
    /**
     * Get Stripe Balance
     */
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

    /**
     * Get Stripe Transactions
     */
    public function getTransactions($accountId, $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $params = [
            'limit' => 100,
            'expand' => ['data.source']
        ];

        /**
         * Date Filters
         */
        if ($request->from_date || $request->to_date) {

            $params['created'] = [];

            if ($request->from_date) {
                $params['created']['gte'] = strtotime($request->from_date);
            }

            if ($request->to_date) {
                $params['created']['lte'] = strtotime($request->to_date . ' 23:59:59');
            }
        }

        /**
         * Fetch Balance Transactions
         */
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

            $availableOn = $txn->available_on
                ? date('d M Y', $txn->available_on)
                : '-';

            $source = $txn->source;

            /**
             * Transaction Type
             */
            $transactionType = $txn->type ?? '-';
            $reportingCategory = $txn->reporting_category ?? '-';

            /**
             * Human Readable Status
             */
            $displayStatus = 'unknown';

            switch ($transactionType) {

                case 'charge':
                case 'payment':
                    $displayStatus = 'payment_received';
                    break;

                case 'refund':
                case 'payment_refund':
                    $displayStatus = 'refunded';
                    break;

                case 'payout':
                    $displayStatus = 'withdrawal';
                    break;

                case 'transfer':
                    $displayStatus = 'transfer';
                    break;

                case 'stripe_fee':
                    $displayStatus = 'stripe_fee';
                    break;

                case 'adjustment':
                    $displayStatus = 'adjustment';
                    break;

                default:
                    $displayStatus = $transactionType;
                    break;
            }

            /**
             * Get Order ID from Charge
             */
            if ($source && $source->object === 'charge') {

                $paymentStatus = $source->status ?? '-';

                /**
                 * Direct Metadata
                 */
                $orderId = $source->metadata->order_id ?? '-';

                /**
                 * Transfer Metadata
                 */
                $transferId = $source->source_transfer ?? null;

                if ($transferId) {

                    try {

                        $transfer = \Stripe\Transfer::retrieve(
                            $transferId,
                            [],
                            ['stripe_account' => $accountId]
                        );

                        $orderId = $transfer->metadata->order_id ?? $orderId;

                    } catch (\Exception $e) {
                        // ignore
                    }
                }
            }

            /**
             * Payment Intent
             */
            if ($source && $source->object === 'payment_intent') {

                $paymentStatus = $source->status ?? '-';

                $orderId = $source->metadata->order_id ?? '-';
            }

            /**
             * Refund Object
             */
            if ($source && $source->object === 'refund') {

                $displayStatus = 'refunded';

                $chargeId = $source->charge ?? null;

                if ($chargeId) {

                    try {

                        $charge = \Stripe\Charge::retrieve(
                            $chargeId,
                            ['stripe_account' => $accountId]
                        );

                        $orderId = $charge->metadata->order_id ?? '-';

                    } catch (\Exception $e) {
                        // ignore
                    }
                }
            }

            /**
             * Load Order Details
             */
            if ($orderId !== '-') {

                $order = Order::find($orderId);

                if ($order) {

                    $orderDate = date(
                        'd M Y',
                        strtotime($order->order_date)
                    );

                    $paymentStatus = $order->payment_status
                        ?? $paymentStatus;
                }
            }

            /**
             * Order Filter
             */
            if ($request->order_id && $orderId != $request->order_id) {
                continue;
            }

            /**
             * Final Response
             */
            $txns[] = [

                'transaction_id' => $txn->id,

                'order_id' => $orderId,

                'txn_type' => $transactionType,

                'reporting_category' => $reportingCategory,

                'display_status' => $displayStatus,

                'amount' => $amount,

                'fee' => $fee,

                'net' => $net,

                'currency' => strtoupper($txn->currency),

                'payment_status' => $paymentStatus,

                'balance_status' => $txn->status,

                'order_date' => $orderDate,

                'available_on' => $availableOn,

                'created_at' => date('d M Y h:i A', $txn->created),
            ];
        }

        return $txns;
    }
}