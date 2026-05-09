<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderItemRefund;
use App\Services\StripePaymentService;
use App\Services\StripeService;

class StripePaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripePaymentService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function stripeTransaction(Request $request)

    {

    
        $accountId = auth()->user()->stripe_account_id;

        if (!$accountId) {
            abort(404, 'Stripe account not connected');
        }

        $balance = $this->stripeService->getBalance($accountId);
        $txns = $this->stripeService->getTransactions($accountId, $request);
            $refunds = OrderItemRefund::where('seller_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($r) {
                    return [
                        'order_id' => $r->order_id,
                        'amount' => $r->refund_amount,
                        'fee' => 0,
                        'net' => $r->refund_amount,
                        'display_status' => 'refunded',
                        'balance_status' => 'available',
                        'order_date' => $r->created_at->format('Y-m-d H:i:s'),
                        'available_on' => $r->created_at->format('Y-m-d H:i:s'),
                    ];
                })
                ->toArray();

    // 🔹 Merge Stripe transactions + Refunds
    $txns = array_merge($txns, $refunds);

    // Sort by order_date descending
    usort($txns, function ($a, $b) {
        return strtotime($b['order_date']) <=> strtotime($a['order_date']);
    });


        $page = $request->page ?? 1;
        $perPage = 10;

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            array_slice($txns, ($page - 1) * $perPage, $perPage),
            count($txns),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );
            return view('admin.payment', [
            'txns' => $paginated,
            'pending' => $balance['pending'],
            'available' => $balance['available'],
        ]);
    }

public function export(Request $request)
{
    $accountId = auth()->user()->stripe_account_id;

    if (!$accountId) {
        abort(404, 'Stripe account not connected');
    }

    // Fetch all transactions
    $txns = $this->stripeService->getTransactions($accountId, $request);
    // Filter by date if needed
    if ($request->from_date && $request->to_date) {
        $from = strtotime($request->from_date);
        $to = strtotime($request->to_date);

        $txns = array_filter($txns, function ($txn) use ($from, $to) {
            return strtotime($txn['order_date']) >= $from &&
                   strtotime($txn['order_date']) <= $to;
        });
    }

    $txns = array_values($txns); // reset keys

    $totals = [
        'amount' => array_sum(array_column($txns, 'amount')),
        'fee'    => array_sum(array_column($txns, 'fee')),
        'net'    => array_sum(array_column($txns, 'net')),
    ];
    $sellername = auth()->user()->name;
    $selleremail= auth()->user()->email;
    $pdf = Pdf::loadView('admin.stripe_transaction_pdf', [
        'txns'      => $txns,
        'from_date' => $request->from_date,
        'to_date'   => $request->to_date,
        'totals'    => $totals,
'sellername' => $sellername,
    'selleremail'=> $selleremail,
        ])->setPaper('a4')->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
    ]);

    return $pdf->download('stripe-statement.pdf');
}
}