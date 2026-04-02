<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
   public function customerList()
{
    $customers = User::where('role','customer')->paginate(5);
    return view('customer_list', compact('customers'));
}
}
