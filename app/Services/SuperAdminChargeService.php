<?php
namespace App\Services;

use App\Models\SellerPayout;
use App\Models\SuperAdminCharge;
use Illuminate\Support\Facades\DB;

class SuperAdminChargeService
{
    public function generate($month, $year)
    {
        $revenues = SellerPayout::select(
                'seller_id',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(amount) * 0.10 as commission')
            )
            ->where('status', 'paid')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->groupBy('seller_id')
            ->get();

        foreach ($revenues as $rev) {

            SuperAdminCharge::updateOrCreate(
                [
                    'seller_id' => $rev->seller_id,
                    'month'     => $month,
                    'year'      => $year,
                ],
                [
                    'total_amount' => $rev->total_amount,
                    'commission'   => $rev->commission,
                    'status'       => 'pending'
                ]
            );
        }
    }

    public function markPaid($id)
    {
        $charge = SuperAdminCharge::findOrFail($id);

        $charge->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);
    }

    public function list()
{
    $currentMonth = now()->month;
    $currentYear  = now()->year;
    $month = request('month');
    $status = request('status');
    $search = request('search');

    // 🔹 CURRENT MONTH (REAL TIME)
   $current = DB::table('users')
    ->where('role', 'admin')
    ->leftJoin('seller_payouts', function ($join) use ($currentMonth, $currentYear) {
        $join->on('users.id', '=', 'seller_payouts.seller_id')
            ->where('seller_payouts.status', 'paid')
            ->whereMonth('seller_payouts.created_at', $currentMonth)
            ->whereYear('seller_payouts.created_at', $currentYear);
    })
    ->select(
        DB::raw('NULL as charge_id'),
        'users.id as seller_id',

        'users.name',
        'users.email',
        'users.mobile',

        DB::raw("$currentMonth as month"),
        DB::raw("$currentYear as year"),

        DB::raw('COALESCE(SUM(seller_payouts.amount),0) as total_amount'),
        DB::raw('COALESCE(SUM(seller_payouts.amount) * 0.10,0) as commission'),

        DB::raw("'pending' as status"),
        DB::raw('NULL as paid_at')
    )
    ->groupBy('users.id','users.name','users.email','users.mobile');



    // 🔹 PREVIOUS MONTH (STORED DATA)
$history = DB::table('super_admin_charges')
    ->join('users','users.id','=','super_admin_charges.seller_id')
    ->select(
        'super_admin_charges.id as charge_id',
        'users.id as seller_id',

        'users.name',
        'users.email',
        'users.mobile',

        'super_admin_charges.month',
        'super_admin_charges.year',

        'super_admin_charges.total_amount',
        'super_admin_charges.commission',
        'super_admin_charges.status',
        'super_admin_charges.paid_at'
    );

    //  FINAL MERGE
$dataQuery = $history->unionAll($current);

$data = DB::query()
    ->fromSub($dataQuery, 't')
    
    // 🔹 Month Filter
    ->when(request('month'), function ($q, $month) {
        $q->where('month', $month);
    })

    // 🔹 Status Filter
    ->when(request('status'), function ($q, $status) {
        $q->where('status', $status);
    })

    // 🔹 Search Filter
    ->when(request('search'), function ($q, $search) {
        $q->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%");
        });
    })

    // 🔹 Sorting
    ->orderBy('year', 'desc')
    ->orderBy('month', 'desc')
    ->get();

return $data;
}
public function totalSales()
{
    return SellerPayout::select(
            'seller_id',
            DB::raw('SUM(amount) as total_sales')
        )
        ->where('status', 'paid')
        ->groupBy('seller_id')
        ->pluck('total_sales', 'seller_id');
}
}