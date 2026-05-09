<?php

namespace App\Http\Controllers;
// app/Http/Controllers/BusinessController.php

use App\Services\BusinessService;
use App\Models\BusinessDetail;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    protected $service;

    public function __construct(BusinessService $service)
    {
        $this->service = $service;
    }

    // Seller view
    public function index()
    {
        $data = BusinessDetail::where('user_id', auth()->id())->first();
        return view('admin.BusinessDetails', compact('data'));
    }

    public function store(Request $request)
    {
            $request->validate([
        'email' => 'required|email',
        'business_name' => 'required|string|max:255',
        'business_phone' => 'required|digits:10',
        'bank_account_number' => 'required',
        'ifsc_code' => 'required',
        'account_holder_name' => 'required',
        'business_address' => 'required',
        'pan_card' => 'required_without:id|file|mimes:jpg,jpeg,png,pdf',
        'gst_certificate' => 'required_without:id|file|mimes:jpg,jpeg,png,pdf',
    ]);

        $this->service->save($request->all());

        return back()->with('success', 'Business details saved successfully');
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'email' => 'required|email',
        'business_name' => 'required|string|max:255',
        'business_phone' => 'required|digits:10',
        'bank_account_number' => 'required',
        'ifsc_code' => 'required',
        'account_holder_name' => 'required',
        'business_address' => 'required',
        'pan_card' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'gst_certificate' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
    ]);

    $this->service->update($id, $request);

    return back()->with('success', 'Business details updated successfully');
}

    // SUPER ADMIN VIEW
    public function adminList()
    {
        $list = BusinessDetail::latest()->get();
        return view('super.admin_business_details', compact('list'));
    }

    // APPROVE API
    public function approve($id)
    {
        $this->service->approve($id);
        return response()->json(['message' => 'Approved']);
    }

    public function reject($id)
    {
        $this->service->reject($id);
        return response()->json(['message' => 'Rejected']);
    }
}