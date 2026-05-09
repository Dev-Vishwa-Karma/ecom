<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderItemRefund;
use Illuminate\Http\Request;

class OrderItemRefundController extends Controller
{
    // List all refunds
    public function index(Request $request)
    {
        $refunds = OrderItemRefund::with(['order','item','seller','customer','refundedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.refunds.index', compact('refunds'));
    }

    // Filter by order_id or seller_id
    public function filter(Request $request)
    {
        $query = OrderItemRefund::with(['order','item','seller','customer','refundedBy']);

        if ($request->order_id) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->seller_id) {
            $query->where('seller_id', $request->seller_id);
        }

        $refunds = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.refunds.index', compact('refunds'));
    }
}