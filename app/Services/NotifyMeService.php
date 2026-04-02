<?php

namespace App\Services;

use App\Models\NotifyMe;
use Illuminate\Support\Facades\DB;


class NotifyMeService
{
    public function store(array $data, int $userId): void
{
    $notify = NotifyMe::where('user_id', $userId)
        ->where('seller_id', $data['seller_id'])
        ->where('variant_id', $data['variant_id'])
        ->first();

    if ($notify) {
        $notify->update([
            'notified_at' => null
        ]);
    } else {
        NotifyMe::create([
            'user_id' => $userId,
            'seller_id' => $data['seller_id'],
            'variant_id' => $data['variant_id'],
            'notified_at' => null
        ]);
    }
}

    public function getAdminDemandReport(int $sellerId)
    {
        return NotifyMe::with([
                'variant:id,product_id,price,color,size,gender',
                'variant.product:id,name',
                'variant.images:id,product_id,image'
            ])
        ->whereHas('variant', function ($q) {
            $q->where('quantity', '<=', 0);
        })
        ->whereNull('notified_at') 
            ->select('variant_id', DB::raw('COUNT(*) as notify_count'))
            ->where('seller_id', $sellerId)
            ->groupBy('variant_id')
            ->orderByDesc('notify_count')
            ->get();
    }
}