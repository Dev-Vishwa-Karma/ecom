@extends('layouts.app')

@section('title', 'All Products')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

@section('content')
<style>

</style>

<h2>All Products</h2>

@if(session('success'))
    <div style="color:#ff8c00; margin-bottom:16px;">{{ session('success') }}</div>
@endif

<div style="display:flex; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <form method="GET" action="{{ route('admin.all-products') }}" style="display:flex; gap:8px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search all products..." style="padding:8px;">
        <button type="submit">Filter</button>
    </form>
</div>

<div class="cards">
    @forelse($products as $product)
    
        <div class="card" >
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

            <h4 style="color:#ff8c00;margin:12px 0 4px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{{ $product->name }}</h4>
            <p style="margin:4px 0;">₹ {{ number_format($product->variants->min('price') ?? 0, 2) }}</p>
            <small style="color:#aaa;">By {{ $product->user?->name ?? '—' }}</small>
            </a>
           <div style="display:flex; gap:8px; margin-top:12px;">
             <!-- Cart Toggle Button -->
           @php
    $isAdded = $product->wishlists->isNotEmpty();
@endphp

<button class="cart-btn {{ $isAdded ? 'added' : '' }}"
        data-product-id="{{ $product->id }}">
    {{ $isAdded ? 'Added!' : 'Add to Cart' }}
</button>

            <!-- Buy Now -->
           </div>
        </div>
    @empty
        <p style="grid-column: 1 / -1; text-align:center; color:#aaa; padding:40px;">
            No products found.
        </p>
    @endforelse
</div>

<div class="pagination">{{ $products->links() }}</div>

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
<div class="modal" id="variantModal">
    <div class="modal-content" style="width:700px;">

        <span class="close" onclick="closeModal('variantModal')">×</span>

        <h3 id="modalProductName"></h3>
        <img id="modalProductImage" style="width:120px; margin-bottom:10px;">

        <table border="1" width="100%" cellpadding="6">
            <thead>
                <tr>
                    <th>Color</th>
                    <th>Size</th>
                    <th>Gender</th>
                    <th>Price</th>
                    <th>Select</th>
                </tr>
            </thead>
            <tbody id="variantTable"></tbody>
        </table>

        <div style="margin-top:15px; display:flex; gap:10px; justify-content:flex-end;">
            <button onclick="closeModal('variantModal')">Close</button>
            <button id="saveVariantsBtn">Done</button>
        </div>

    </div>
</div>

<script>
let currentProductId = null;

const productsData = {!! $productsDataJson !!} ?? {};

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

/// ─────────────────────────────────────────────
// Cart Toggle (Open Variant Modal)
// ─────────────────────────────────────────────


document.querySelectorAll('.cart-btn').forEach(btn => {
    btn.addEventListener('click', function () {

        currentProductId = this.dataset.productId;
        const product = productsData[currentProductId];

        if (!product) {
            alert("Product data not found");
            return;
        }

        // safe defaults
        const variants = product.variants || [];
        const selected = product.selected_variants || [];

        // modal header
        document.getElementById('modalProductName').innerText = product.name || '';
        document.getElementById('modalProductImage').src =
            (product.images && product.images.length > 0)
                ? product.images[0]
                : 'https://via.placeholder.com/100';

        // build table
        let html = '';

        if (variants.length === 0) {
            html = `<tr>
                        <td colspan="5" style="text-align:center;color:#888;">
                            No variants found
                        </td>
                    </tr>`;
        } else {

            variants.forEach(v => {

                const isChecked = selected.includes(v.id) ? 'checked' : '';

                html += `
                    <tr>
                        <td>${v.color ?? '-'}</td>
                        <td>${v.size ?? '-'}</td>
                        <td>${v.gender ?? '-'}</td>
                        <td>₹ ${v.price ?? 0}</td>
                        <td>
                            <input type="checkbox"
                                   class="variant-check"
                                   value="${v.id}"
                                   ${isChecked}>
                        </td>
                    </tr>
                `;
            });
        }

        document.getElementById('variantTable').innerHTML = html;

        openModal('variantModal');
    });
});


// ─────────────────────────────────────────────
// Save Selected Variants (Bulk Update Wishlist)
// ─────────────────────────────────────────────

document.getElementById('saveVariantsBtn').addEventListener('click', async function () {

    if (!currentProductId) {
        alert("No product selected");
        return;
    }

    const btn = this;
    btn.disabled = true;
    btn.innerText = "Saving...";

    try {

        let selected = [];

        document.querySelectorAll('.variant-check:checked').forEach(cb => {
            selected.push(cb.value);
        });

        const response = await fetch('{{ route("wishlist.variant.bulk") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: currentProductId,
                variants: selected
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Failed to update wishlist');
        }

        // close modal
        closeModal('variantModal');

        // update UI button state
        const btnEl = document.querySelector(`.cart-btn[data-product-id="${currentProductId}"]`);

        if (btnEl) {
            if (selected.length > 0) {
                btnEl.classList.add('added');
                btnEl.innerText = "Added!";
            } else {
                btnEl.classList.remove('added');
                btnEl.innerText = "Add to Cart";
            }
        }
        window.location.reload();
            
        // success popup
        Swal.fire({
            icon: 'success',
            title: 'Updated',
            text: selected.length > 0
                ? 'Variants added to wishlist'
                : 'Wishlist cleared for this product',
            timer: 1500,
            showConfirmButton: false
        });

    } catch (err) {

        console.error(err);

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: err.message || 'Something went wrong'
        });

    } finally {
        btn.disabled = false;
        btn.innerText = "Done";
    }
});
</script>
@endsection