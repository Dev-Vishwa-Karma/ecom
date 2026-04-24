@extends('layouts.app')
@section('title', 'Checkout Payment')

@section('content')
<div style="max-width:800px;margin:40px auto;padding:20px;background:#1e1e1e;border-radius:12px;">
    <h2 style="color:#ff8c00;text-align:center;">Complete Your Payment</h2>
    <h3 style="color:#fff;">Order Number: {{ $order->order_number }}</h3>

    <table style="width:100%;color:white;margin-bottom:20px;border-collapse:collapse;">
        <thead>
            <tr>
                <th>Product</th><th>Variant</th><th>Qty</th><th>Price</th><th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>Color: {{ $item->variant->color ?? '-' }}, Size: {{ $item->variant->size ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->price,2) }}</td>
                <td>₹{{ number_format($item->total_price,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="color:#ff8c00;">Total: ₹{{ number_format($order->items->sum('total_price'),2) }}</h3>

    <form action="{{ route('payment.process', $order->id) }}" method="POST" id="paymentForm">
        @csrf
        <input type="hidden" name="payment_intent_id" id="payment_intent_id">
        <div id="card-element" style="padding:12px;border-radius:8px;border:1px solid #444;background:#2a2a2a;color:white;"></div>
        <div id="card-errors" style="color:red;margin-top:10px;"></div>
        <div class="d-flex justify-content-between">
        <button type="button" style="margin-top:15px;padding:10px 20px;background:#ff8c00;color:black;border:none;border-radius:8px;" onclick="window.history.back()">
            Back
        </button>
        <button type="submit" style="margin-top:15px;padding:10px 20px;background:#ff8c00;color:black;border:none;border-radius:8px;">Pay Now</button>
        </div>
    </form>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe("{{ env('STRIPE_KEY') }}");
const elements = stripe.elements();
const card = elements.create('card', { style: { base: { color:'#fff', fontSize:'16px', '::placeholder': { color:'#aaa' } } } });
card.mount('#card-element');

const form = document.getElementById('paymentForm');
form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const {paymentMethod, error} = await stripe.createPaymentMethod({ type:'card', card });
    if(error){ document.getElementById('card-errors').textContent = error.message; return; }

    const {paymentIntent, error: confirmError} = await stripe.confirmCardPayment("{{ $intent->client_secret }}", { payment_method: paymentMethod.id });
    if(confirmError){ document.getElementById('card-errors').textContent = confirmError.message; return; }

    document.getElementById('payment_intent_id').value = paymentIntent.id;
    form.submit();
});
</script>
@endsection