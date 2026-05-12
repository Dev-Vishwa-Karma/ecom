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
#cancelComment{
    background:#fff !important;
    color:#000 !important;
    pointer-events:auto !important;
    position:relative;
    z-index:9999;
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
<div class="modal fade" id="viewOrderModal{{ $order->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-dark text-white">

            <div class="modal-header">
                <h5 class="modal-title">
                    Order #{{ $order->order_number }}
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                {{-- ADDRESS --}}
                <div style="margin-bottom:20px;">
                    <strong>Address :</strong>{{ $order->address }}
                    
                </div>

                {{-- ITEMS --}}
                @foreach($order->items as $item)

                <div style="
                    border:1px solid #444;
                    border-radius:10px;
                    padding:15px;
                    margin-bottom:15px;
                ">

                    <div style="display:flex;justify-content:space-between;gap:20px;">

                        <div>

                            <h6 style="margin-bottom:10px;">
                                {{ $item->product->name ?? 'Product' }}
                            </h6>

                            <div style="font-size:14px;color:#ccc;">
                                Variant :
                                {{ $item->variant->color ?? '' }}
                                {{ $item->variant->size ?? '' }}
                            </div>

                            <div style="margin-top:8px;">
                                Qty : {{ $item->quantity }}
                            </div>

                            <div>
                                Price : ₹{{ $item->price }}
                            </div>
                            <div>
                                Total : ₹{{ $item->total_price }}
                            </div>

                        </div>

                        <div style="text-align:right;">

                            {{-- ITEM STATUS --}}
                            <span style="
                                background:#333;
                                color:#ffcc00;
                                padding:6px 12px;
                                border-radius:6px;
                                display:inline-block;
                                margin-bottom:10px;
                            ">
                                {{ ucfirst($item->status ?? $order->status) }}
                            </span>

                                                            {{-- CANCELLED INFO --}}
                                @if($item->cancellation)

                                <div style="font-size:12px;color:#aaa;">

                                    Cancelled by

                                    <strong style="color:#ff4d4d;">

                                        @if($item->cancellation->cancelled_by_type === 'customer')
                                            You
                                        @elseif($item->cancellation->cancelled_by_type === 'seller')
                                            Seller 
                                </br>{{$item->cancellation->reason }}
                                        @elseif($item->cancellation->cancelled_by_type === 'admin')
                                            Admin
                                        @endif

                                    </strong>

                                </div>

                                @endif

                            {{-- CANCEL ITEM BUTTON --}}
                            @if(
                                $order->items->count() > 1
                                &&
                                ($item->status ?? '') !== 'cancelled'
                                &&
                                $order->status !== 'dispatched'
                                &&
                                $order->status !== 'delivered'
                            )

                                <button
                                    class=" btn-sm mt-2 openCancelModalBtn"
                                    data-type="item"
                                    data-id="{{ $item->id }}">
                                    Cancel Item
                                </button>

                            @endif

                        </div>

                    </div>

                </div>

                @endforeach


                {{-- FULL ORDER CANCEL --}}
                @if(
                    $order->status !== 'dispatched'
                    &&
                    $order->status !== 'delivered'
                    &&
                    $order->status !== 'cancelled'
                )

                <div class="text-end mt-3">

                    <button
                        class="openCancelModalBtn"
                        data-type="order"
                        data-id="{{ $order->id }}">
                        Cancel Order
                    </button>

                </div>

                @endif

            </div>

        </div>
    </div>
</div>


<div class="order-card" style="padding:15px;margin-bottom:15px;background:#1e1e1e;border-radius:10px;">

    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h4 style="color:#fff;">Order #{{ $order->order_number }}</h4>

        <span style="padding:5px 10px;border-radius:6px;background:#333;color:#ffcc00;">
            {{ ucfirst($order->status) }}

        </span>
    </div>

    <hr style="border-color:#333;">

    <p><strong>Payment Mode:</strong> {{ ucfirst($order->payment_mode) }}</p>
    <p><strong>Total Amount:</strong> ₹{{ number_format($order->total_amount, 2) }}</p>
    <p><strong>Order Date:</strong> {{ $order->order_date?->format('d M Y h:i A') }}</p>

    <div style="margin-top:15px;display:flex;gap:10px;flex-wrap:wrap;">

        {{-- INVOICE --}}
        <a href="{{ route('invoice', $order->id) }}"
           style="padding:8px 14px;background:#ff8c00;color:white;border-radius:6px;text-decoration:none;">
            View Invoice
        </a>

        {{-- VIEW ORDER --}}
        <button
            type="button"
            class="viewOrderBtn"
            data-bs-toggle="modal"
            data-bs-target="#viewOrderModal{{ $order->id }}"
            style="padding:8px 14px;">
            View Order
        </button>

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

<!-- Cancel Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">

            <div class="modal-header">
                <h5 class="modal-title">Cancel Request</h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="cancelType">
                <input type="hidden" id="cancelTargetId">

                <label class="mb-2">
                    <input type="radio" name="reason" value="better_price">
                    Better Price
                </label>
                <br>

                <label class="mb-2">
                    <input type="radio" name="reason" value="not_needed">
                    Not Needed
                </label>
                <br>

                <label class="mb-2">
                    <input type="radio" name="reason" value="mistake">
                    Mistake
                </label>
                <br>

                <label class="mb-2">
                    <input type="radio" name="reason" value="other">
                    Other
                </label>

     </div>

            <div class="modal-footer">

                <button type="button"
                        class=""
                        data-bs-dismiss="modal">
                    Close
                </button>

                <button type="button"
                        class=""
                        id="confirmCancelBtn">
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

   

});


document.addEventListener("DOMContentLoaded", function () {




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

<script>

document.addEventListener("DOMContentLoaded", function () {

    const cancelModalEl = document.getElementById('cancelOrderModal');
    const cancelModal = new bootstrap.Modal(cancelModalEl);

    // OPEN MODAL
    document.querySelectorAll('.openCancelModalBtn').forEach(btn => {

        btn.addEventListener('click', function () {

            document.getElementById('cancelType').value = this.dataset.type;
            document.getElementById('cancelTargetId').value = this.dataset.id;

            // reset
            document.querySelectorAll('input[name="reason"]').forEach(r => {
                r.checked = false;
            });


            cancelModal.show();
        });

    });

    // CONFIRM CANCEL
    document.getElementById('confirmCancelBtn').addEventListener('click', function () {

        let reasonEl = document.querySelector('input[name="reason"]:checked');

        if (!reasonEl) {
            alert('Please select reason');
            return;
        }

        const reason = reasonEl.value;

        const type = document.getElementById('cancelType').value;
        const targetId = document.getElementById('cancelTargetId').value;

        let url = '';
        let payload = {
            reason: reason,
        };

        // ITEM CANCEL
        if(type === 'item') {

            url = "{{ route('order.item.cancel') }}";

            payload.item_id = targetId;

        } else {

            url = "{{ route('order.cancel') }}";

            payload.order_id = targetId;
        }

        fetch(url, {

            method: "POST",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },

            body: JSON.stringify(payload)

        })
        .then(res => res.json())
        .then(data => {

            alert(data.message);

            location.reload();

        });

    });

});

</script>



@endsection
