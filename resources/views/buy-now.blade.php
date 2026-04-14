@extends('layouts.app')

@section('title', 'Buy Now - ' . $product->name)

@section('content')
<div style="max-width:700px; margin:40px auto; padding:20px; background:#1e1e1e; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.5);">
    <h2 style="color:#ff8c00; text-align:center;">Confirm Your Order</h2>

    @if ($errors->any())
        <div style="background:#ff4d4d; color:white; padding:12px; border-radius:8px; margin-bottom:20px;">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Product Details -->
    <div style="display:flex; gap:30px; margin-bottom:30px; flex-wrap:wrap;">
        <div style="flex:1; min-width:250px;">
            <img src="{{ $product->images->first()?->image ?? 'https://via.placeholder.com/300x300' }}"
                 alt="{{ $product->name }}"
                 style="width:100%; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.4);">
        </div>
        <div style="flex:2;">
            <h3 style="color:#ff8c00;">{{ $product->name }}</h3>
            <p style="font-size:1.2em; color:#fff; margin:10px 0;">{{ $product->description ?? 'No description available.' }}</p>
            <small style="color:#aaa;">Seller: {{ $product->user?->name ?? '—' }}</small>

            <!-- Variant & Quantity -->
            <div style="margin-top:20px;">
                <label style="color:#ddd;">Select Variant:</label>
                <select  name="variant_id" id="variantSelect" style="width:100%; padding:10px; border-radius:8px; margin-bottom:12px; background:#2a2a2a; color:white;">
                    @foreach($product->variants as $variant)
                        <option value="{{ $variant->id }}"
                                data-price="{{ $variant->price }}"                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  
                                data-stock="{{ $variant->quantity }}"
                                {{ (int)$variant->id === (int)$variantId ? 'selected' : '' }} >
                            Color: {{ $variant->color ?? '-' }}, 
                            Size: {{ $variant->size ?? '-' }}, 
                            Gender: {{ $variant->gender ?? '-' }}, ₹{{ number_format($variant->price,2) }}
                        </option>
                    @endforeach
                </select>

                <label style="color:#ddd;">Quantity:</label>
                <input type="number" name="quantity" id="quantityInput" value="{{ $quantity }}" min="1" max="{{ $product->variants->first()->quantity }}" style="width:100%; padding:12px; border-radius:8px; margin-bottom:12px; background:#2a2a2a; color:white; border:1px solid #444;">

                <p style="color:#ff8c00; font-size:1.2em;">Total Price: ₹<span id="totalPrice">0.00</span></p>
            </div>
        </div>
    </div>

    <!-- Order Form -->
    <form id="orderForm" method="POST" action="{{ route('payment.process', $product->id) }}">
        @csrf
        <input type="hidden" name="variant_id" id="hiddenVariantId" value="{{ $variantId }}">
        <input type="hidden" name="quantity" id="hiddenQuantity" value="{{ $quantity }}">
        <input type="hidden" name="total_price" id="hiddenTotalPrice" value="0.00">

        <!-- Customer Details -->
        <div style="margin-bottom:30px;">
            <h4 style="color:#ff8c00; margin-bottom:15px;">Your Details</h4>
            <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" placeholder="Full Name" required style="width:100%; padding:12px; margin-bottom:12px; border-radius:8px; border:1px solid #444; background:#2a2a2a; color:white;">
            <input type="text" name="mobile" value="{{ old('mobile', auth()->user()->mobile ?? '') }}" placeholder="Mobile Number" required style="width:100%; padding:12px; margin-bottom:12px; border-radius:8px; border:1px solid #444; background:#2a2a2a; color:white;">
            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" placeholder="Email" required style="width:100%; padding:12px; margin-bottom:12px; border-radius:8px; border:1px solid #444; background:#2a2a2a; color:white;">
            <textarea name="address" placeholder="Full Delivery Address" required rows="4" style="width:100%; padding:12px; border-radius:8px; border:1px solid #444; background:#2a2a2a; color:white;">{{ old('address') }}</textarea>
        </div>

        <!-- Payment Mode -->
        <div style="margin-bottom:30px;">
            <h4 style="color:#ff8c00; margin-bottom:15px;">Payment Mode</h4>
            <label style="display:block; margin-bottom:12px; color:#ddd;">
                <input type="radio" name="payment_mode" value="cod" checked> Cash on Delivery (COD)
            </label>
            <label style="display:block; color:#ddd;">
                <input type="radio" name="payment_mode" value="online"> Online Payment (Card)
            </label>
        </div>

        <div id="onlinePaymentFields" style="display:none; margin-top:15px;">
    
            <div id="card-element" 
                style="width:100%; padding:12px; border-radius:8px; border:1px solid #444; background:#2a2a2a; color:white;">
            </div>

            <div id="card-errors" style="color:red; margin-top:10px;"></div>

        </div>

        <label style="display:block; margin-bottom:20px; color:#ddd;">
            <input type="checkbox" name="declaration" required> I agree to the terms & conditions and confirm the order details.
        </label>

        <div class="d-flex justify-content-between">
            <button type="button" onclick="window.history.back()" style="padding:8px; background:#ff8c00; color:black; border:none; border-radius:10px; font-size:1.2em; font-weight:bold; cursor:pointer;">Back</button>
            <button type="submit" id="confirmBtn" style="padding:8px; background:#ff8c00; color:black; border:none; border-radius:10px; font-size:1.2em; font-weight:bold; cursor:pointer;">Confirm Order</button>
        </div>
    </form>
</div>

<script src="https://js.stripe.com/v3/"></script>

<script>
const stripe = Stripe("{{ env('STRIPE_KEY') }}");
const elements = stripe.elements();

const card = elements.create('card', {
    style: {
        base: {
            color: '#ffffff',
            fontSize: '16px',
            '::placeholder': {
                color: '#aaa'
            }
        }
    }
});

let cardMounted = false;

document.querySelectorAll('input[name="payment_mode"]').forEach(radio => {
    radio.addEventListener('change', function() {

        const isOnline = this.value === 'online';
        const container = document.getElementById('onlinePaymentFields');

        container.style.display = isOnline ? 'block' : 'none';

        //  Mount only once when visible
        if (isOnline && !cardMounted) {
            card.mount('#card-element');
            cardMounted = true;
        }
    });
});
</script>

<script>
const variants = @json($product->variants);

const variantSelect = document.getElementById('variantSelect');
const quantityInput = document.getElementById('quantityInput');
const totalPriceEl = document.getElementById('totalPrice');
const hiddenVariantId = document.getElementById('hiddenVariantId');
const hiddenQuantity = document.getElementById('hiddenQuantity');
const hiddenTotalPrice = document.getElementById('hiddenTotalPrice');

// Make sure variantId and quantity are numbers
const urlVariantId = parseInt("{{ $variantId }}");
const urlQuantity = parseInt("{{ $quantity }}");

// Set variant select and quantity input
variantSelect.value = urlVariantId;
quantityInput.value = urlQuantity;

// Update total price function
function updateTotal() {
    const selectedOption = variantSelect.querySelector(`option[value="${variantSelect.value}"]`);
    if (!selectedOption) return;

    const price = parseFloat(selectedOption.dataset.price);
    const stock = parseInt(selectedOption.dataset.stock);
    let qty = parseInt(quantityInput.value);

    if (qty > stock) qty = stock;
    if (qty < 1) qty = 1;
    quantityInput.value = qty;

    const total = (price * qty).toFixed(2);
    totalPriceEl.textContent = total;

    hiddenVariantId.value = selectedOption.value;
    hiddenQuantity.value = qty;
    hiddenTotalPrice.value = total;
}

// Event listeners
variantSelect.addEventListener('change', updateTotal);
quantityInput.addEventListener('input', updateTotal);

// Initialize total price on page load
updateTotal();

// Payment toggle
document.querySelectorAll('input[name="payment_mode"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('onlinePaymentFields').style.display = this.value === 'online' ? 'block' : 'none';
    });
});

const form = document.getElementById('orderForm');

let processing = false;

form.addEventListener('submit', async function(e) {

    if (processing) return;

    const paymentMode = document.querySelector('input[name="payment_mode"]:checked').value;

    if (paymentMode !== 'online') {
        return true;
    };

    e.preventDefault();
    processing = true;

    document.getElementById('confirmBtn').disabled = true;

    const {paymentMethod, error} = await stripe.createPaymentMethod({
        type: 'card',
        card: card,
    });

    if (error) {
        processing = false;
        document.getElementById('confirmBtn').disabled = false;
        document.getElementById('card-errors').textContent = error.message;
        return;
    }

    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'payment_method_id';
    input.value = paymentMethod.id;

    form.appendChild(input);

    form.submit();
});
</script>
@endsection