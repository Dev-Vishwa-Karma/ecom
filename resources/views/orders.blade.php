@extends('layouts.app')

@section('title', 'My Orders')

<link rel="stylesheet" href="{{ asset('css/order-table.css') }}">

<style>
.star-rating {
    display: flex;
    gap: 5px;
    font-size: 28px;
    cursor: pointer;
    color: #ccc;
}
.star-rating .star.filled {
    color: #ffcc00;
}
.capture-area {
    padding: 15px;
    background: #212529;
    color: #fff;
}
.form-control:focus {
    border-color: #ff8c00 !important;
    outline: none !important;
    box-shadow: 0 0 5px #ff8c00 !important;
}
</style>

@section('content')

<h2 style="color:#ff8c00;text-align:center;margin-bottom:30px;">My Orders</h2>

@if(session('success'))
<div style="background:#1a3a1a;color:#4dff88;padding:12px;border-radius:8px;margin-bottom:20px;text-align:center;">
    {{ session('success') }}
</div>
@endif

@forelse($orders as $order)

<div class="order-card" style="padding:15px;margin-bottom:15px;background:#1e1e1e;border-radius:10px;">

    {{-- HEADER --}}
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h4 style="color:#fff;">Order #{{ $order->order_number }}</h4>

        <span style="padding:5px 10px;border-radius:6px;background:#333;color:#ffcc00;">
            {{ ucfirst($order->status) }}

        </span>
            @if($order->status === 'cancelled')
        <div style="font-size:12px; color:#aaa; margin-top:4px;">
            Cancelled by 
            <strong style="color:#ff4d4d;">
                {{ $order->cancelled_by_type === 'customer' ? 'You' : ucfirst($order->cancelled_by_type) }}
            </strong>

            @if($order->cancelled_at)
                <div style="font-size:11px;">
                    on {{ \Carbon\Carbon::parse($order->cancelled_at)->format('d M Y h:i A') }}
                </div>
            @endif
        </div>
    @endif

    </div>

    <hr style="border-color:#333;">

    {{-- DETAILS --}}
    <p><strong>Payment Mode:</strong> {{ ucfirst($order->payment_mode) }}</p>
    <p><strong>Total Amount:</strong> ₹{{ number_format($order->total_amount, 2) }}</p>
    <p><strong>Order Date:</strong> {{ $order->order_date?->format('d M Y h:i A') }}</p>

    <p>
        <strong>Address:</strong><br>
        {{ $order->address }}
    </p>

    {{-- ACTIONS --}}
    <div style="margin-top:15px;display:flex;gap:10px;">

        {{-- INVOICE --}}
        <a href="{{ route('invoice', $order->id) }}"
           style="padding:8px 14px;background:#ff8c00;color:#000;border-radius:6px;text-decoration:none;">
            View Invoice
        </a>

        {{-- RATE BUTTON (ONLY DELIVERED) --}}
        @if($order->status === 'delivered')
        <a href="#"
        style="text-decoration: none; color: black; background: #ff8c00; padding: 8px 14px; border-radius: 6px;"
        class="rate-btn"
        data-product-id="{{ $order->items->first()->product_id }}"
        data-variant-id="{{ $order->items->first()->variant_id }}"
        data-product-name="{{ $order->items->first()->product->name ?? '' }}"
        data-variant="{{ $order->items->first()->variant->color ?? '' }} {{ $order->items->first()->variant->size ?? '' }}"
        data-price="{{ $order->items->first()->variant->price ?? 0 }}">
            Rate Order
        </a>
        @endif
        @if ($order->status === 'pending'|| $order->status==='processing')
            <a href="#" 
                style="text-decoration: none; color: black; background: #ff8c00; padding: 8px 14px; border-radius: 6px;"
                data-order-id="{{ $order->id }}"
                data-bs-toggle="modal" 
                data-bs-target="#cancelOrderModal"
                class="cancelBtn"
                >
                Cancel Order
            </a>
        
        @endif 

    </div>

</div>

@empty
<div style="text-align:center;color:#aaa;padding:50px;">
    No orders found
</div>
@endforelse

<div class="pagination">{{ $orders->links() }}</div>


{{-- Include rate modals only for delivered orders --}}


{{-- Single modal for rating --}}
<div class="modal fade" id="rateProductModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('product.rate.store') }}" class="modal-content">
            @csrf
            <input type="hidden" id="modalProductId" name="product_id">
            <input type="hidden" id="modalVariantId" name="variant_id">
            <div class="modal-header justify-content-between">
                <h5>Rate Product</h5>
                <button type="button" data-bs-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div id="capture-area" class="capture-area">
                    <strong id="modalProductName"></strong><br>
                    <strong id="modalVariant"></strong><br>
                    <strong id="modalPrice"></strong>
                    <div class="mt-3">
                        <div class="star-rating" id="starContainer">
                            <span class="star" data-value="1">★</span>
                            <span class="star" data-value="2">★</span>
                            <span class="star" data-value="3">★</span>
                            <span class="star" data-value="4">★</span>
                            <span class="star" data-value="5">★</span>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" required>
                    </div>
                    <textarea name="comment" class="form-control mt-3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit">Submit</button>
                <button type="button" id="fb-share-btn">Share</button>
            </div>
        </form>
    </div>
</div>

{{-- FB Confirmation Modal --}}
<div class="modal fade" id="fbConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Share on Facebook?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Do you want to share this review on Facebook?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="fbConfirmNo" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="fbConfirmYes">Yes</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <input type="hidden" id="cancelOrderId">

                <label><input type="radio" name="reason" value="better_price"> Better Price</label><br>
                <label><input type="radio" name="reason" value="not_needed"> Not Needed</label><br>
                <label><input type="radio" name="reason" value="mistake"> Mistake</label><br>
                <label><input type="radio" name="reason" value="other" id="otherRadio"> Other</label>

                <textarea id="otherText" class="form-control mt-2"
                    placeholder="Write reason..." style="display:none;"></textarea>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Go Back
                </button>

                <button type="button" class="btn btn-danger" id="confirmCancelBtn">
                    Submit Cancellation
                </button>
            </div>

        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let selectedOrderId = null;

    document.querySelectorAll('.cancelBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            selectedOrderId = this.dataset.orderId;
        });
    });

    // show textarea
    document.getElementById('otherRadio').addEventListener('change', function () {
        document.getElementById('otherText').style.display = 'block';
    });

    document.querySelectorAll('input[name="reason"]').forEach(r => {
        if (r.value !== 'other') {
            r.addEventListener('change', function () {
                document.getElementById('otherText').style.display = 'none';
            });
        }
    });

    // confirm cancel
    document.getElementById('confirmCancelBtn').addEventListener('click', function () {

        let reason = document.querySelector('input[name="reason"]:checked');

        if (!reason) {
            alert("Select reason");
            return;
        }

        let finalReason = reason.value;

        if (reason.value === 'other') {
            finalReason = document.getElementById('otherText').value;
        }

        fetch("{{ route('order.cancel') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                order_id: selectedOrderId,
                reason: finalReason
            })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            location.reload();
        });
    });

});   

document.addEventListener('DOMContentLoaded', function () {



    const modalEl = document.getElementById('rateProductModal');
    const modal = new bootstrap.Modal(modalEl);

    

    document.querySelectorAll('.rate-btn').forEach(btn => {

        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const productId = this.dataset.productId;
            const variantId = this.dataset.variantId;

            if (!productId || !variantId) {
                console.log("Missing dataset:", this.dataset);
                alert("Data missing on button");
                return;
            }

            document.getElementById('modalProductId').value = productId;
            document.getElementById('modalVariantId').value = variantId;

            document.getElementById('modalProductName').innerText =
                "Product: " + (this.dataset.productName || '');

            document.getElementById('modalVariant').innerText =
                "Variant: " + (this.dataset.variant || '');

            document.getElementById('modalPrice').innerText =
                "Price: ₹" + (this.dataset.price || 0);

                modal.show();
            });
        });

        // Star rating
        const stars = document.querySelectorAll('#starContainer .star');
        const ratingInput = document.getElementById('ratingInput');

        stars.forEach(star => {
            star.addEventListener('click', () => {
                let val = star.dataset.value;
                ratingInput.value = val;

                stars.forEach(s => {
                    s.classList.toggle('filled', s.dataset.value <= val);
                });
            });
        });

        // Share review image
        // Share review image
        const fbModalEl = document.getElementById('fbConfirmModal');
        const fbModal = new bootstrap.Modal(fbModalEl);

        document.getElementById('fb-share-btn').addEventListener('click', function() {
            const comment = document.querySelector('textarea[name="comment"]').value;

            fetch("{{ route('generate.review.image') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        product_name: document.getElementById('modalProductName').innerText.replace('Product: ', ''),
                        variant: document.getElementById('modalVariant').innerText.replace('Variant: ', ''),
                        price: document.getElementById('modalPrice').innerText.replace('Price: ₹', ''),
                        rating: document.getElementById('ratingInput').value,
                        comment: comment
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Show confirmation modal
                        fbModal.show();

                        document.getElementById('fbConfirmYes').onclick = function() {
                            fbModal.hide();
                            // Open FB share with generated image URL
                            const fbUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(data.url)}`;
                            window.open(fbUrl, "_blank");
                        }
                    } else {
                        alert("Image generation failed: " + (data.message || "Unknown error"));
                    }
                })
                .catch(err => console.error(err));
        });
        // Yes clicked
        document.getElementById('fbConfirmYes').addEventListener('click', function() {
            const data = {
                product_name: document.getElementById('modalProductName').innerText.replace('Product: ', ''),
                variant: document.getElementById('modalVariant').innerText.replace('Variant: ', ''),
                price: document.getElementById('modalPrice').innerText.replace('Price: ₹', ''),
                rating: document.getElementById('ratingInput').value,
                comment: document.querySelector('textarea[name="comment"]').value
            };

            fetch("{{ route('generate.review.image') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const fbUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(data.url)}`;
                        window.open(fbUrl, "_blank"); // Open FB share in new tab
                    } else {
                        alert("Image generation failed: " + (data.message || "Unknown error"));
                    }
                });
            fbModal.hide();
        });

        // No clicked
        document.getElementById('fbConfirmNo').addEventListener('click', function() {
            fbModal.hide();
        });

});
</script>



@endsection
