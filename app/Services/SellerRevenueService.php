<?php

namespace App\Services;

use App\Models\SellerPayout;
use Illuminate\Support\Facades\DB;

class SellerRevenueService
{
    public function getMonthlyRevenue($sellerId)
    {
        return SellerPayout::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->where('seller_id', $sellerId)
            ->where('status', 'paid') // only paid revenue
            ->groupBy('year', 'month')
            ->orderBy('year', 'ASC')
            ->orderBy('month', 'ASC')
            ->get();
    }
}