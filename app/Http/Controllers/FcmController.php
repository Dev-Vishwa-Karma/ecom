<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FcmToken;

class FcmController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->fcm_token) {
            return response()->json(['status' => false]);
        }

        FcmToken::updateOrCreate(
            ['token' => $request->fcm_token],
            [
                'user_id' => auth()->id(),
                'device_type' => $request->device_type ?? 'web',
                'is_active' => true
            ]
        );

        return response()->json(['status' => true]);
    }
}
