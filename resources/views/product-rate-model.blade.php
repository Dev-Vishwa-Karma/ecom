@extends('layouts.app')

@section('title', 'My Orders')

<style>
    .star-rating {
        display: flex;
        gap: 5px;
        font-size: 28px;
        cursor: pointer;
        color: #ccc; /* empty stars */
    }

    .star-rating .star.filled {
        color: #ffcc00; /* filled stars */
    }

    .form-control:focus {
        border-color: #ff8c00 !important;
        outline: none !important;
        box-shadow: 0 0 5px #ff8c00 !important;
    }

    /* Optional: Add a border/padding for the captured area */
    .capture-area {
        padding: 15px;
        background: #212529;
    }
</style>

<link rel="stylesheet" href="{{ asset('css/order-table.css') }}">

{{-- resources/views/product-rate-model.blade.php --}}
<div class="modal fade" id="rateProductModal-{{ $order->product->id }}" tabindex="-1" aria-labelledby="rateProductModalLabel-{{ $order->product->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('product.rate.store') }}">
            @csrf
            <input type="hidden" name="product_id" value="{{ $productId }}">
            <input type="hidden" name="variant_id" value="{{ $variantId }}">

            <!-- Modal Header (optional, can remove from capture) -->
            <div class="modal-header">
                <h5 class="modal-title" id="rateProductModalLabel-{{ $productId }}">Rate Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Capture only this section -->
                <div id="capture-area-{{ $order->product->id }}" class="capture-area">
                    <div>
                        <span style="font-weight:bold;">Product Name: {{ $order->product->name }}</span>
                        <br>
                        <span style="font-weight:bold;">Variant: {{ $order->variant->color }} {{ $order->variant->size }}</span>
                        <br>
                        <span style="font-weight:bold;">Price: {{ $order->variant->price }}</span>
                        <br>
                    </div>

                    <div class="mb-3">
                        <span style="font-weight:bold;">Rating</span>
                        <div class="star-rating" data-product-id="{{ $productId }}">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="star" data-value="{{ $i }}">&#9733;</span>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="rating-input-{{ $productId }}" value="0" required>
                    </div>

                    <div class="mb-3">
                        <span for="comment-{{ $productId }}" style="font-weight:bold;">Comment</span>
                        <textarea style="max-width: 400px;" class="form-control" id="comment-{{ $productId }}" name="comment"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"  data-bs-dismiss="modal">Close</button>
                <button type="submit">Submit Rating</button>
                
                <!-- Facebook Share Button -->
                <button type="button" id="fb-share-btn-{{ $order->product->id }}">
                    Share on Facebook
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Facebook SDK -->
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v12.0" nonce="U96dSeG1"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Star rating functionality
    document.querySelectorAll('.star-rating').forEach(function(starContainer) {
        const productId = starContainer.dataset.productId;
        const ratingInput = document.getElementById('rating-input-' + productId);
        if (!ratingInput) return;

        const stars = starContainer.querySelectorAll('.star');

        stars.forEach(star => {
            star.addEventListener('mouseover', () => {
                const value = parseInt(star.dataset.value);
                stars.forEach(s => s.classList.toggle('filled', parseInt(s.dataset.value) <= value));
            });

            star.addEventListener('click', () => {
                const value = parseInt(star.dataset.value);
                ratingInput.value = value;
                stars.forEach(s => s.classList.toggle('filled', parseInt(s.dataset.value) <= value));
            });

            star.addEventListener('mouseout', () => {
                const value = parseInt(ratingInput.value) || 0;
                stars.forEach(s => s.classList.toggle('filled', parseInt(s.dataset.value) <= value));
            });
        });
    });

   

    document.getElementById('fb-share-btn-{{ $order->product->id }}').addEventListener('click', function() {
        const element = document.getElementById('capture-area-{{ $order->product->id }}'); // capture only this

        html2canvas(element).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            shareImageOnFacebook(imgData);
        });
    });

    function shareImageOnFacebook(imageData) {
        fetch("{{ route('image.upload') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ image_data: imageData })
        })
        .then(res => res.json())
        .then(data => {
            const fbUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(data.url)}`;
            window.open(fbUrl, '_blank', 'width=600,height=400');
        })
        .catch(err => console.error("Image upload failed:", err));
    }
});

// Initialize Facebook SDK
window.fbAsyncInit = function() {
    FB.init({
        appId      : "{{ env('FACEBOOK_APP_ID') }}",
        cookie     : true,
        xfbml      : true,
        version    : 'v12.0'
    });
};
</script>