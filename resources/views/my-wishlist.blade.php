@extends('layouts.app')

@section('title', 'My Wishlist')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

@section('content')
<style>
   

</style>

<h2>My Wishlist</h2>

@if(session('success'))
    <div style="color:#4dff88; margin-bottom:16px;">{{ session('success') }}</div>
@endif

<div style="display:flex; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <form method="GET" action="{{ route('my-wishlist') }}" style="display:flex; gap:8px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search wishlist..." style="padding:8px;">
        <button type="submit">Filter</button>
    </form>
</div>

<div class="cards">
    @forelse($wishlists as $wishlist)
        @php $product = $wishlist->product; @endphp
        <div class="card">
            <div class="image-wrapper" data-product-id="{{ $product->id }}">
                <button class="card-prev" type="button">‹</button>
                <img id="card-img-{{ $product->id }}"
                     src="{{ $product->images->first()?->image ?? 'https://via.placeholder.com/300x170?text=No+Image' }}"
                     alt="{{ $product->name }}">
                <button class="card-next" type="button">›</button>
            </div>

            <div class="menu" data-product-id="{{ $product->id }}">⋮</div>
            <div class="dropdown" id="menu-{{ $product->id }}">
                <a href="#" class="view-btn" data-id="{{ $product->id }}">View</a>
            </div>
            <a style="cursor: pointer;" onclick="window.location.href='{{ route('product.details', $product->id) }}'">
            <h4 style="color:#ff8c00; margin:12px 0 4px;">{{ $product->name }}</h4>
            <p style="margin:4px 0;">₹ {{ number_format($product->variants->min('price') ?? 0, 2) }}</p>
            <small style="color:#aaa;">By {{ $product->user?->name ?? '—' }}</small>
            </a>
           <div style="display:flex; gap:8px; margin-top:12px;">

            <!-- Remove Cart Button -->
            <button class="cart-btn added" 
                    data-product-id="{{ $product->id }}" style="padding: 0px !important;" >
                Remove Cart
            </button>

            <!-- Buy Now -->
            <!-- <button class="buy-now" onclick="window.location.href='{{ route('buy.now', $product->id) }}'">Buy Now</button> -->
           </div>
        </div>
    @empty
        <p style="grid-column: 1 / -1; text-align:center; color:#aaa; padding:40px;">
            Your wishlist is empty. Add some products!
        </p>
    @endforelse
</div>

<div class="pagination">{{ $wishlists->links() }}</div>

<!-- View Modal -->
<div class="modal" id="detailModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('detailModal')">×</span>
        <h3 id="detailTitle"></h3>
        <div id="detailImages">
            <button id="prevBtn" type="button">❮</button>
            <img id="detailImage" src="" alt="Product">
            <button id="nextBtn" type="button">❯</button>
        </div>
        <p id="detailPrice"></p>
        <p id="detailDesc"></p>
    </div>
</div>

<script>
// Same JS as all-products
const productsData = {!! $productsDataJson ?? '{}' !!};
const cardIndices = {};

function openModal(id)  { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

// Menu toggle
document.addEventListener('click', e => {
    const menuBtn = e.target.closest('.menu');
    if (menuBtn) {
        const id = menuBtn.dataset.productId;
        document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
        document.getElementById(`menu-${id}`).classList.toggle('show');
        return;
    }

    if (!e.target.closest('.dropdown') && !e.target.closest('.menu')) {
        document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
    }

    const viewBtn = e.target.closest('.view-btn');
    if (viewBtn) {
        e.preventDefault();
        openDetail(viewBtn.dataset.id);
    }
});

// Card arrows
document.querySelectorAll('.card-prev, .card-next').forEach(btn => {
    btn.addEventListener('click', () => {
        const wrapper = btn.closest('.image-wrapper');
        const id = wrapper.dataset.productId;
        const imgs = productsData[id]?.images || [];
        if (!imgs.length) return;

        cardIndices[id] = cardIndices[id] ?? 0;

        if (btn.classList.contains('card-prev')) {
            cardIndices[id] = (cardIndices[id] - 1 + imgs.length) % imgs.length;
        } else {
            cardIndices[id] = (cardIndices[id] + 1) % imgs.length;
        }

        document.getElementById(`card-img-${id}`).src = imgs[cardIndices[id]];
    });
});

// Card swipe
document.querySelectorAll('.image-wrapper').forEach(wrapper => {
    let startX = 0;
    wrapper.addEventListener('touchstart', e => {
        startX = e.touches[0].clientX;
    }, { passive: true });

    wrapper.addEventListener('touchend', e => {
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) < 50) return;

        const id = wrapper.dataset.productId;
        if (diff > 0) wrapper.querySelector('.card-next').click();
        else wrapper.querySelector('.card-prev').click();
    }, { passive: true });
});

// View modal
let currentImages = [];
let currentIndex = 0;

function showImage() {
    document.getElementById('detailImage').src = currentImages[currentIndex] || '';
}

document.getElementById('prevBtn')?.addEventListener('click', () => {
    if (currentImages.length > 1) {
        currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
        showImage();
    }
});

document.getElementById('nextBtn')?.addEventListener('click', () => {
    if (currentImages.length > 1) {
        currentIndex = (currentIndex + 1) % currentImages.length;
        showImage();
    }
});

function openDetail(id) {
    const prod = productsData[id];
    if (!prod) return;

    currentImages = prod.images;
    currentIndex = 0;

    document.getElementById('detailTitle').textContent = prod.name;
    document.getElementById('detailPrice').textContent = `₹ ${prod.price}`;
    document.getElementById('detailDesc').textContent = prod.description || '';

    showImage();
    openModal('detailModal');
}

// Cart Toggle (same as all-products)
document.querySelectorAll('.cart-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const productId = this.dataset.productId;

        fetch('{{ route("wishlist.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'added') {
                this.textContent = 'Add to Cart';
                this.classList.remove('added');
            } else {
                this.textContent = 'Remove Cart';
                this.classList.add('added');
            }
            alert(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong!');
        });
    });
});
</script>
@endsection