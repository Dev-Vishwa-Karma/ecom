<?php

namespace App\Http\Controllers;

use App\Services\SellerRevenueService;
use Illuminate\Http\Request;
use App\Services\OrderService;

class SuperAdminController extends Controller
{
    protected $service;
    protected $orderService;



    public function __construct(SellerRevenueService $service , OrderService $orderService)
    {
        $this->service = $service;
        $this->orderService = $orderService;
    }

    public function dashboard( Request $request)
    {
        $revenues = $this->service->getMonthlyAppRevenue()
            ->map(fn($item) => [
                'month' => (int) $item->month,
                'year' => (int) $item->year,
                'total' => (float) $item->total,
            ]);


            $month = $request->get('month', now()->month);
            $year = $request->get('year', now()->year);

            $stats = $this->orderService->getAllOrdersForSuperAdmin($month, $year);

            // Calculate percentages
            $total = $stats['total'] ?  : 1; // avoid division by zero
            $percentages = [
                'delivered' => round(($stats['delivered'] / $total) * 100, 2),
                'cancelled' => round(($stats['cancelled'] / $total) * 100, 2),
                'pending'   => round(($stats['pending'] / $total) * 100, 2),
                'processing'=> round(($stats['processing'] / $total) * 100, 2),
             ];

             $totalRevenue = $this->service->getTotalRevenue();
            
        return view('super.dashboard', compact('revenues', 'stats', 'percentages', 'month', 'year', 'totalRevenue'));
    }

   

    
}