<?php
namespace App\Services;

use App\Models\BusinessDetail;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BusinessService
{
    public function save($data)
    {
        $userId = Auth::id();

        $detail = BusinessDetail::updateOrCreate(
            ['user_id' => $userId],
            [
                'email' => $data['email'],
                'business_name' => $data['business_name'],
                'business_phone' => $data['business_phone'],
                'bank_account_number' => $data['bank_account_number'],
                'ifsc_code' => $data['ifsc_code'],
                'account_holder_name' => $data['account_holder_name'],
                'business_address' => $data['business_address'],
                'status' => 'pending'
            ]
        );

        // upload PAN
        if (!empty($data['pan_card'])) {
            $upload = Cloudinary::upload($data['pan_card']->getRealPath());
            $detail->update(['pan_card' => $upload->getSecurePath()]);
        }

        // upload GST
        if (!empty($data['gst_certificate'])) {
            $upload = Cloudinary::upload($data['gst_certificate']->getRealPath());
            $detail->update(['gst_certificate' => $upload->getSecurePath()]);
        }

        return $detail;
    }

    public function update($id, $request)
{
    $data = BusinessDetail::findOrFail($id);

    // BASIC UPDATE
    $data->email = $request->email;
    $data->business_name = $request->business_name;
    $data->business_phone = $request->business_phone;
    $data->bank_account_number = $request->bank_account_number;
    $data->ifsc_code = $request->ifsc_code;
    $data->account_holder_name = $request->account_holder_name;
    $data->business_address = $request->business_address;

    // PAN UPDATE (Cloudinary)
    if ($request->hasFile('pan_card')) {
        $upload = cloudinary()->upload(
            $request->file('pan_card')->getRealPath()
        );
        $data->pan_card = $upload->getSecurePath();
    }

    // GST UPDATE (Cloudinary)
    if ($request->hasFile('gst_certificate')) {
        $upload = cloudinary()->upload(
            $request->file('gst_certificate')->getRealPath()
        );
        $data->gst_certificate = $upload->getSecurePath();
    }

    $data->save();

    return $data;
}

    public function approve($id)
    {
        $detail = BusinessDetail::findOrFail($id);

        $detail->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);

        return $detail;
    }

    public function reject($id)
    {
        $detail = BusinessDetail::findOrFail($id);

        $detail->update([
            'status' => 'rejected'
        ]);

        return $detail;
    }
}