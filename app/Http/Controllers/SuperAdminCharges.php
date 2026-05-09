<?php
namespace App\Http\Controllers;

use App\Services\SuperAdminChargeService;
use App\Http\Requests\GenerateChargesRequest;

class SuperAdminCharges extends Controller
{
    protected $service;

    public function __construct(SuperAdminChargeService $service)
    {
        $this->service = $service;
    }

    // SUPER ADMIN PAGE
   public function index()
{
    $data = $this->service->list();

    return view('super.charge_calculate', compact('data'));
}

    // GENERATE (manual button)
    public function generate(GenerateChargesRequest $request)
    {
        $this->service->generate($request->month, $request->year);

        return back()->with('success', 'Charges generated successfully');
    }

    // MARK PAID
    public function markPaid($id)
    {
        $this->service->markPaid($id);

        return back()->with('success', 'Marked as paid');
    }
}