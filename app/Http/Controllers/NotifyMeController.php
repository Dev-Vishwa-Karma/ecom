<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotifyMeRequest;
use App\Services\NotifyMeService;
use App\Services\StripePaymentService;

class NotifyMeController extends Controller
{
    protected $notifyMeService;
    protected $stripePaymentService;
    
    public function __construct(NotifyMeService $notifyMeService, StripePaymentService $stripePaymentService)
    {
        $this->notifyMeService = $notifyMeService;
        $this->stripePaymentService = $stripePaymentService;
    }

    public function store(StoreNotifyMeRequest $request)
    {
        $this->notifyMeService->store(
            $request->validated(),
            auth()->id()
        );

        return response()->json([
            'message' => 'You will be notified when the product is back in stock.'
        ]);
    }

public function adminDemandReport()
{
    $user = auth()->user();

    $topDemand = $this->notifyMeService
        ->getAdminDemandReport($user->id);

    $balance = [
        'pending' => 0,
        'available' => 0,
    ];

    // ✅ check stripe account exists
    if (!empty($user->stripe_account_id)) {

        try {
            $stripeBalance = $this->stripePaymentService
                ->getBalance($user->stripe_account_id);

            $balance['pending'] = $stripeBalance['pending'] ?? 0;
            $balance['available'] = $stripeBalance['available'] ?? 0;

        } catch (\Exception $e) {
            \Log::error('Stripe Balance Error: ' . $e->getMessage());
        }
    }

        $revenues = app(\App\Services\SellerRevenueService::class)
        ->getMonthlyRevenue(auth()->id());



    return view('admin.dashboard', [
        'pending' => $balance['pending'],
        'available' => $balance['available'],
        'revenues' => $revenues,
    ],compact('topDemand')
    
    );
}
}