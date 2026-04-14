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
    width:100%;
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
    display:flex;
    justify-content:space-between;
    margin-top:20px;
}

.btn{
    padding:10px 18px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:bold;
}

.btn-cancel{
    background:#444;
    color:#fff;
}

.btn-submit{
    background:#ff8c00;
    color:#000;
}
</style>

<div class="checkout-container">

    <h2 class="checkout-title">🛒 Cart Checkout</h2>

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

                <div>
                    Qty: <b>{{ $item['quantity'] }}</b>
                </div>
            </div>
        @endforeach
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
        <div class="payment-box">
            <h4 style="color:#ff8c00;">Payment Mode</h4>

            <label>
                <input type="radio" name="payment_mode" value="cod" checked>
                Cash on Delivery
            </label>

            <br>

            <label>
                <input type="radio" name="payment_mode" value="online">
                Online Payment
            </label>
        </div>

        {{-- BUTTONS --}}
        <div class="buttons">

            <button type="button"
                    class="btn btn-cancel"
                    onclick="window.location.href='/my-cart'">
                Cancel
            </button>

            <button type="submit"
                    class="btn btn-submit">
                Place Order
            </button>

        </div>

    </form>

    @endif

</div>

@endsection