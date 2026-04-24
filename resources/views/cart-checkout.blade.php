@extends('layouts.app')

@section('title', 'Cart Checkout')

@section('content')

<style>
.checkout-container{
    max-width: 900px;
    margin: 40px auto;
    background: #111;
    color: #fff;
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
}

.checkout-title{
    text-align:center;
    color:#ff8c00;
    margin-bottom: 20px;
}

.summary-box{
    background:#1c1c1c;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}

.item-row{
    display:flex;
    justify-content:space-between;
    padding:10px 0;
    border-bottom:1px solid #333;
    font-size:14px;
}

.item-row:last-child{
    border-bottom:none;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
}

input, textarea{
    margin-right: 12px;
    padding:10px;
    border-radius:8px;
    border:none;
    outline:none;
    background:#222;
    color:#fff;
}

textarea{
    grid-column: span 2;
    min-height:90px;
}

.payment-box{
    margin-top:15px;
    padding:10px;
    background:#1c1c1c;
    border-radius:10px;
}
.buttons{
    
    display: flex;
    justify-content: space-between;
    margin-top: 20px;

}


</style>

<div class="checkout-container">

    <h2 class="checkout-title">🛒 Product Checkout</h2>

    @if(empty($items))
        <p style="text-align:center;color:#aaa;">No items found in cart</p>
    @else

    {{-- ORDER SUMMARY --}}
    <div class="summary-box">
        <h3 style="color:#ff8c00;">Order Summary</h3>

        @foreach($items as $item)

        <div class="item-row">

                <div>

                    Product : {{  $item['product']->name ?? '-'  }} <br>
                     <p> {{ $item['variant']->color ?? '-' }} |
                        {{ $item['variant']->size ?? '-' }} |
                        {{ $item['variant']->gender ?? '-' }}</p>


                </div>

                <div class="d-flex flex-column align-items-end">
                    <span>Qty: <b>{{ $item['quantity'] }}</b></span>
                    <span >Price: ₹ <b>{{ number_format(($item['variant']->price ?? 0) * $item['quantity'], 2) }}</b></span>
                </div>
            </div>
        @endforeach
    </div>
     <div class="summary-box">
        <div class="item-row" style="font-size:16px;">
            <strong>Total Amount:</strong>
            <strong>₹ {{ number_format(collect($items)->sum(fn($i) => ($i['variant']->price ?? 0) * $i['quantity']), 2) }}</strong>
        </div>
    </div>

    {{-- FORM --}}
    <form method="POST" action="{{ route('cart.placeOrder') }}">
        @csrf

       <h3 style="color:#ff8c00;">Customer Details</h3>

<div class="form-grid">

    <input type="text"
           name="customer_name"
           value="{{ old('customer_name', $user->name ?? '') }}"
           placeholder="Full Name"
           required>

    <input type="text"
           name="mobile"
           value="{{ old('mobile', $user->mobile ?? '') }}"
           placeholder="Mobile Number"
           required>

    <input type="email"
           name="email"
           value="{{ old('email', $user->email ?? '') }}"
           placeholder="Email"
           required>

    {{-- ADDRESS --}}
    <textarea name="address"
              id="addressBox"
              placeholder="Full Address"
              required>{{ old('address') }}</textarea>

</div>

        {{-- PAYMENT --}}
        <h4 style="color:#ff8c00; margin-top: 20px;">Payment Mode</h4>

        <div class="payment-box">
            <div class="d-flex direction-row justify-content-around">

            <label>
                <input type="radio" name="payment_mode" value="cod" checked>Cash on Delivery </label>


            <label >
                <input type="radio" name="payment_mode" value="online">
                Online Payment
            </label>
            </div>
        </div>

        {{-- BUTTONS --}}
        <div class="buttons">

            <button type="button"
                    class=" btn-cancel"
                    onclick="window.history.back()">
                Cancel
            </button>

            <button type="submit"
                    class=" btn-submit">
                Place Order
            </button>

        </div>

    </form>

    @endif

</div>

@endsection