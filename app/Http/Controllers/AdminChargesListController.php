<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SuperAdminCharge;
use Symfony\Component\HttpFoundation\Request;

class AdminChargesListController extends Controller
{
    public function index(Request $request){
        $data = SuperAdminCharge::where('seller_id', auth()->user()->id)->get();

        return view('admin.BusinessCharges', compact('data'));
    }
}