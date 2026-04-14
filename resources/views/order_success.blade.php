@extends('layouts.app')

@section('content')
<div style="text-align:center; margin-top:100px;">
    <h2 style="color:#28a745;">✅ Order Placed Successfully!</h2>
    <p style="color:white;">Your payment/order has been processed.</p>

    <a href="{{ url('/my-orders') }}" style="display:inline-block; margin-top:20px; padding:10px 20px; background-color:#ff8c00; color:white; text-decoration:none; border-radius:5px;">Back</a>
</div>
@endsection