<?php

namespace App\Http\Controllers;

use App\Services\SellerRevenueService;

class SellerRevenueController extends Controller
{
    protected $service;

    public function __construct(SellerRevenueService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $sellerId = auth()->id();

        $revenues = $this->service->getMonthlyRevenue($sellerId);
        return view('seller.revenue', compact('revenues'));
    }
}