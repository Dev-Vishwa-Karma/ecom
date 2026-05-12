@extends('layouts.app')
@section('title', 'My Cart')

<style>
.cart-item { display: flex; gap: 20px; border: 1px solid #cb7000; padding: 9px; margin-bottom: 20px; border-radius: 10px; }
.cart-image img { width: 110px; height: 160px; object-fit: cover; border-radius: 8px; }
.cart-details { flex: 1; }
.variant-row { margin-bottom: 10px; }
.price { font-weight: bold; color: #ff8c00; margin-bottom: 10px; }
.quantity-box { display: flex; justify-content: space-between; align-items: center; }
.qty-btn { padding: 2px 12px !important; }
.qty-input { width: 50px; text-align: center; }
.mycartcontainer { max-height: 550px; overflow-y: scroll; scrollbar-width: none; -ms-overflow-style: none; }
.mycartcontainer::-webkit-scrollbar { display: none; }
</style>

@section('content')

<h2>My Cart</h2>

@if($products->isEmpty())
    <p style="text-align:center; color:#aaa;">Your cart is empty</p>
@endif

<div class="mycartcontainer">

@foreach($products as $product)
    @foreach($product->wishlists as $wishlist)
        @php
            $variant = $product->variants->firstWhere('id', $wishlist->variant_id);
        @endphp

        <div class="cart-item">
            <div class="cart-image">
                <img src="{{ $product->images->first()?->image ?? 'https://via.placeholder.com/150' }}">
            </div>

            <div class="cart-details">
                <h3>{{ $product->name }}</h3>

                <!-- VARIANT DETAILS -->
                <div class="variant-row">
                    <p>{{ $variant->color ?? '-' }} | {{ $variant->size ?? '-' }} | {{ $variant->gender ?? '-' }}</p>
                </div>

                <!-- PRICE -->
                <p class="price">
                    ₹ <span class="item-price" data-base-price="{{ $variant->price ?? 0 }}">
                        {{ $variant->price ?? 0 }}
                    </span>
                </p>

                <!-- QUANTITY -->
                <div class="quantity-box">
                    <div>
                        <button class="qty-btn minus">-</button>
                        <input type="number" value="1" min="1" class="qty-input">
                        <button class="qty-btn plus">+</button>
                    </div>

                    <div>
                        <button class="remove-btn" data-product-id="{{ $wishlist->product_id }}"
                        data-variant-id="{{ $wishlist->variant_id }}">Remove</button>
                    <button class="buy-now"
                        data-product-id="{{ $wishlist->product_id }}"
                        data-variant-id="{{ $wishlist->variant_id }}">
                        Buy Now
                    </button>    
                                </div>
                </div>
            </div>
        </div>
    @endforeach
@endforeach

</div>

<!-- TOTAL -->
 @if ($products->isNotEmpty())
     <div class="d-flex justify-content-between mt-3">
    <h4>Total: ₹ <span id="cart-total">0.00</span></h4>
    <div>
        <button id="remove-all">Remove All</button>
        <button id="buy-all">Buy All</button>
    </div>
</div>

 @endif

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // UPDATE TOTAL
    // =========================
    function updateTotal() {
        let total = 0;

        document.querySelectorAll('.cart-item').forEach(item => {
            const basePrice = parseFloat(item.querySelector('.item-price').dataset.basePrice) || 0;
            const qty = parseInt(item.querySelector('.qty-input').value) || 1;

            total += basePrice * qty;

            item.querySelector('.item-price').innerText = (basePrice * qty).toFixed(2);
        });

        const totalEl = document.getElementById('cart-total');
        if (totalEl) {
            totalEl.innerText = total.toFixed(2);
        }
    }

    // =========================
    // QUANTITY BUTTONS
    // =========================
    document.querySelectorAll('.cart-item').forEach(item => {

        const minus = item.querySelector('.minus');
        const plus = item.querySelector('.plus');
        const input = item.querySelector('.qty-input');

        if (minus && plus && input) {

            minus.addEventListener('click', () => {
                let val = parseInt(input.value) || 1;
                if (val > 1) input.value = val - 1;
                updateTotal();
            });

            plus.addEventListener('click', () => {
                let val = parseInt(input.value) || 1;
                input.value = val + 1;
                updateTotal();
            });
        }
    });

    // =========================
    // REMOVE ITEM
    // =========================
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function () {

            fetch('{{ route("wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: this.dataset.productId,
                    variant_id: this.dataset.variantId
                })
            })
            .then(res => res.json())
            .then(() => {
                this.closest('.cart-item').remove();
                updateTotal();
            });
        });
    });

    // =========================
    // REMOVE ALL
    // =========================
    const removeAllBtn = document.getElementById('remove-all');
    if (removeAllBtn) {
        removeAllBtn.addEventListener('click', function () {

            fetch('{{ route("cart.clear") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(() => {
                document.querySelector('.mycartcontainer').innerHTML = '';
                updateTotal();
            });
        });
    }

    // =========================
    // BUY NOW
    // =========================
// =========================
// BUY NOW
// =========================
document.querySelectorAll('.buy-now').forEach(btn => {

    btn.addEventListener('click', function () {

        const productId = this.dataset.productId;
        const variantId = this.dataset.variantId;

        const item = {
            product_id: productId,
            variant_id: variantId,
            quantity: this.closest('.cart-item')
                .querySelector('.qty-input').value || 1
        };

        fetch('/cart/session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                items: [item]
            })
        })
        .then(res => res.json())
        .then(() => {
            window.location.href = '/cart/checkout';
        });

    });

});
    // =========================
    // BUY ALL
    // =========================
    const buyAllBtn = document.getElementById('buy-all');

    if (buyAllBtn) {
        buyAllBtn.addEventListener('click', function () {

            let items = [];

            document.querySelectorAll('.cart-item').forEach(item => {

                const productId = item.querySelector('.remove-btn')?.dataset.productId;
                const variantId = item.querySelector('.remove-btn')?.dataset.variantId;
                const qty = item.querySelector('.qty-input')?.value || 1;

                if (productId && variantId) {
                    items.push({
                        product_id: productId,
                        variant_id: variantId,
                        quantity: qty
                    });
                }
            });

            fetch('/cart/session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ items })
            })
            .then(res => res.json())
            .then(() => {
                window.location.href = '/cart/checkout';
            });
        });
    }

    // INIT
    updateTotal();
});
</script>