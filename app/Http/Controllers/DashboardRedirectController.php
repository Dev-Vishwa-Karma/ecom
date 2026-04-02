<?php

namespace App\Http\Controllers;

class DashboardRedirectController extends Controller
{
    public function redirect()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return match (auth()->user()->role) {
            'super_admin' => redirect()->route('super.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('customer.dashboard'),
        };
    }
}