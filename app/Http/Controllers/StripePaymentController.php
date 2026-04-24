<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Services\StripePaymentService;
use App\Services\StripeService;
use Symfony\Component\HttpFoundation\Request;

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
}