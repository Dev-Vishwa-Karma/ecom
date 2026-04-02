{{-- resources/views/admin/my-products.blade.php --}}
@extends('layouts.app')
@section('title', 'My Products')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<style>
    div#editDropzone {
    background: none;
}
</style>
@section('content')

@if(session('success'))
    <div style="color:#ff8c00; margin-bottom:16px; font-weight:bold;">
        {{ session('success') }}
    </div>
@endif

<div style="display:flex; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <form method="GET" action="{{ route('admin.my-products') }}" style="display:flex; gap:8px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search my products...">
        <button type="submit">Filter</button>
    </form>
    <button onclick="openModal('createModal')">+ Add Product</button>
</div>

<div class="cards">
    @forelse($products as $product)
        <div class="card">
            <div class="image-wrapper" data-product-id="{{ $product->id }}">
                <button class="card-prev" type="button">‹</button>
                <img id="card-img-{{ $product->id }}" src="{{ $product->images->first()?->image ?? asset('images/placeholder.jpg') }}" alt="{{ $product->name }}">
                <button class="card-next" type="button">›</button>
            </div>
            <div class="menu" data-product-id="{{ $product->id }}">⋮</div>
            <div class="dropdown" id="menu-{{ $product->id }}">
                <a href="#" class="view-btn" data-id="{{ $product->id }}">View</a>
                <a href="#" class="edit-btn" data-id="{{ $product->id }}">Edit</a>
                <a href="#" class="stock-btn" data-id="{{ $product->id }}">Manage Stock</a>
                <a href="#" class="delete-btn" data-id="{{ $product->id }}">Delete</a>
            </div>
            <h4 style="color:#ff8c00; margin:12px 0 4px;">{{ $product->name }}</h4>
            <p style="margin:4px 0;">₹ {{ number_format($product->variants->min('price') ?? 0, 2) }}</p>
            <small style="color:#aaa;">By {{ $product->user?->name ?? '—' }}</small>
        </div>
    @empty
        <p style="text-align:center; color:#888; grid-column: 1 / -1; padding:40px 0;">
            No products found.
        </p>
    @endforelse
</div>

{{ $products->links() }}

<!-- Create Modal -->
<div class="modal" id="createModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('createModal')">×</span>
        <h3 style="color:#ff8c00;">Create Product</h3>
        <form action="{{ route('admin.products.store') }}" method="POST" id="dropzoneForm" enctype="multipart/form-data" class="dropzone">
            @csrf
            <input type="text" name="name" placeholder="Product Name" required style="width:100%; padding:10px; margin-bottom:12px; border:1px solid #444; border-radius:6px; background:#2a2a2a; color:#ddd;">
            <textarea name="description" placeholder="Description" rows="4" style="width:100%; padding:10px; margin-bottom:16px; border:1px solid #444; border-radius:6px; background:#2a2a2a; color:#ddd;"></textarea>

            <h4 style="margin-top:15px; color:#ff8c00;">Variants (at least one required)</h4>
            <div id="variantContainer">
                <div class="variant-row" style="display:flex;gap:10px;margin-bottom:8px;flex-wrap:wrap;">
                    <input type="text" name="variants[0][color]" placeholder="Color" style="flex:1; min-width:90px; padding:8px; border:1px solid #444; border-radius:4px; background:#2a2a2a; color:#ddd;">
                    <input type="text" name="variants[0][size]" placeholder="Size" style="flex:1; min-width:90px; padding:8px; border:1px solid #444; border-radius:4px; background:#2a2a2a; color:#ddd;">
                    <input type="text" name="variants[0][gender]" placeholder="Gender" style="flex:1; min-width:90px; padding:8px; border:1px solid #444; border-radius:4px; background:#2a2a2a; color:#ddd;">
                    <input type="number" name="variants[0][price]" placeholder="Price *" required step="0.01" style="flex:1; min-width:90px; padding:8px; border:1px solid #444; border-radius:4px; background:#2a2a2a; color:#ddd;">
                    <input type="number" name="variants[0][quantity]" placeholder="Qty *" required min="0" style="flex:1; min-width:90px; padding:8px; border:1px solid #444; border-radius:4px; background:#2a2a2a; color:#ddd;">
                    <button type="button" onclick="removeVariant(this)" style="background:red;color:white;border:none;padding:8px 12px;border-radius:4px;">X</button>
                </div>
            </div>
            <button type="button" onclick="addVariant()" style="background:#444; color:white; padding:8px 16px; border:none; border-radius:6px; margin:10px 0;">+ Add Variant</button>

            <div class="dz-previews-wrapper"></div>
            <div class="dz-message">Drag & drop images or click to upload</div>

            <button type="submit" id="saveProductBtn" style="margin-top:16px; background:#ff8c00; color:black; width:100%; padding:12px; border:none; border-radius:6px;">
                Save Product
            </button>
        </form>
    </div>
</div>

<!-- Detail / Edit Modal -->
<div class="modal" id="detailModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('detailModal')">×</span>
        <h3 id="detailTitle" style="color:#ff8c00;"></h3>

        <div id="viewSection">
            <div id="detailImages">
                <button id="prevBtn">❮</button>
                <img id="detailImage" src="" alt="">
                <button id="nextBtn">❯</button>
            </div>
            <p id="detailDesc"></p>

            <h4 style="color:#ff8c00; margin:16px 0 8px;">Variants</h4>
            <table class="variants-table">
                <thead>
                    <tr>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Gender</th>
                        <th>Price</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody id="variantsTableBody"></tbody>
            </table>

            <button onclick="switchToEdit()" style="background:#ff8c00;color:black;padding:8px 16px;margin-top:16px;">Edit Product</button>
        </div>

        <div id="editSection" style="display:none;">
            <form method="POST" id="editForm" enctype="multipart/form-data" action="">
                @csrf
                <input type="hidden" name="id" id="eId">
                <input type="text" name="name" id="eName" required style="width:100%;padding:10px;margin-bottom:12px;border:1px solid #444;border-radius:6px;background:#2a2a2a;color:#ddd;">
                <textarea name="description" id="eDesc" rows="4" style="width:100%;padding:10px;margin-bottom:16px;border:1px solid #444;border-radius:6px;background:#2a2a2a;color:#ddd;"></textarea>

                <h4 style="color:#ff8c00; margin:16px 0 8px;">Existing Images</h4>
                <div id="existingImagesPreview" class="edit-preview-container"></div>

                <h4 style="color:#ff8c00; margin:16px 0 8px;">Add New Images</h4>
                <div id="editDropzone" class="dropzone" style="border:2px dashed #444; padding:20px; text-align:center; min-height:120px; cursor:pointer;">
                    <div class="dz-message">Drag & drop or click to upload new images</div>
                    <div class="dz-previews-wrapper"></div>
                </div>

                <button type="submit" id="updateProductBtn" style="margin-top:16px; background:#ff8c00; color:black; width:100%; padding:12px; border:none; border-radius:6px;">
                    Update Product
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Stock Modal -->
<div class="modal" id="stockModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('stockModal')">×</span>
        <h3 style="color:#ff8c00;">Manage Stock — <span id="stockProductName"></span></h3>

        <div id="stockEditForm" style="display:none; margin:20px 0;">
            <h4 id="stockFormTitle" style="color:#ff8c00;">Add / Edit Variant</h4>
            <form id="stockForm">
                @csrf
                <input type="hidden" name="product_id" id="stockProductId">
                <input type="hidden" name="variant_id" id="editVariantId">

                <div class="stock-edit-row">
                    <input type="text" name="color" id="stockColor" placeholder="Color">
                    <input type="text" name="size" id="stockSize" placeholder="Size">
                    <input type="text" name="gender" id="stockGender" placeholder="Gender">
                </div>

                <input type="number" name="price" id="stockPrice" placeholder="Price" step="0.01" required>
                <input type="number" name="quantity" id="stockQuantity" placeholder="Quantity" min="0" required>

                <div id="averagePriceInfo" style="margin:12px 0; color:#aaa;"></div>

                <button type="submit" style="background:#ff8c00; color:black; width:100%; padding:10px; border:none; border-radius:6px;">
                    Save Variant
                </button>
            </form>
        </div>

        <button id="addVariantBtn" style="background:#444; color:white; padding:10px 20px; border:none; border-radius:6px; margin:16px 0; cursor:pointer;">
            + Add Variant
        </button>

        <hr style="margin:20px 0; border-color:#444;">

        <h4 style="color:#ff8c00;">Current Variants</h4>
        <div id="variantList"></div>
    </div>
</div>

<!-- Delete Confirmation -->
<div class="modal" id="deleteModal">
    <div class="modal-content" style="text-align:center; max-width:400px;">
        <span class="close" onclick="closeModal('deleteModal')">×</span>
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this product?</p>
        <form method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <input type="hidden" name="id" id="deleteId">
            <button type="submit" style="background:#ff8c00; color:white; padding:10px 20px; border:none; border-radius:6px; cursor:pointer; margin:8px;">Yes, Delete</button>
            <button type="button" onclick="closeModal('deleteModal')" style="background:#ff8c00; color:white; padding:10px 20px; border:none; border-radius:6px; cursor:pointer;">Cancel</button>
        </form>
    </div>
</div>
<div class="modal" id="variantDeleteModal">
    <div class="modal-content" style="text-align:center; max-width:400px;">
        <span class="close" onclick="closeModal('variantDeleteModal')">×</span>
        <h3>Delete Variant</h3>
        <p>Are you sure you want to delete this variant?</p>
        <div style="display: flex; justify-content: space-between ">
    
        <button onclick="deleteVariant()" 
            style="background:#ff8c00; color:white; padding:10px 20px; border:none; border-radius:6px; cursor:pointer; margin:8px;">
            Yes, Delete
        </button>

        <button onclick="closeModal('variantDeleteModal')" 
            style="background:#ff8c00; color:white; padding:10px 20px; border:none; border-radius:6px; cursor:pointer; height:44px; margin-top:7px">
            Cancel
        </button>

        </div>
    </div>
</div>

<script>
const productsData = {!! $productsDataJson ?? '{}' !!};

// ── Helpers ────────────────────────────────────────────────
function openModal(id) { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

// Menu dropdown
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
});

// Card image navigation
const cardIndices = {};
document.querySelectorAll('.card-prev, .card-next').forEach(btn => {
    btn.addEventListener('click', () => {
        const wrapper = btn.closest('.image-wrapper');
        const id = wrapper.dataset.productId;
        const imgs = productsData[id]?.images || [];
        if (!imgs.length) return;
        cardIndices[id] = cardIndices[id] ?? 0;
        cardIndices[id] = btn.classList.contains('card-prev')
            ? (cardIndices[id] - 1 + imgs.length) % imgs.length
            : (cardIndices[id] + 1) % imgs.length;
        document.getElementById(`card-img-${id}`).src = imgs[cardIndices[id]];
    });
});

// ── Detail Modal ───────────────────────────────────────────
let currentImages = [];
let currentIndex = 0;

function showImage() {
    document.getElementById('detailImage').src = currentImages[currentIndex] || '';
}

['prevBtn', 'nextBtn'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', () => {
        if (currentImages.length > 1) {
            currentIndex = (id === 'prevBtn')
                ? (currentIndex - 1 + currentImages.length) % currentImages.length
                : (currentIndex + 1) % currentImages.length;
            showImage();
        }
    });
});

let editDropzone = null;

function initEditDropzone() {
    if (editDropzone) {
        editDropzone.destroy();
        editDropzone = null;
    }

    editDropzone = new Dropzone("#editDropzone", {
        url: document.getElementById('editForm')?.action || '/fallback',
        paramName: "images[]",
        uploadMultiple: true,
        parallelUploads: 5,
        maxFilesize: 5,
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        autoProcessQueue: false,
        clickable: true,
        dictDefaultMessage: "Drag & drop or click to upload new images",
        previewsContainer: "#editDropzone .dz-previews-wrapper",
    });
}

function openDetail(id, mode = 'view') {
    const prod = productsData[id];
    if (!prod) return;

    currentImages = prod.images || [];
    currentIndex = 0;

    document.getElementById('detailTitle').textContent = prod.name;
    document.getElementById('detailDesc').textContent = prod.description || 'No description';

    ['eId', 'eName', 'eDesc'].forEach((elId, i) => {
        const el = document.getElementById(elId);
        if (el) el.value = [id, prod.name, prod.description || ''][i];
    });

    document.getElementById('editForm').action = '{{ route("admin.products.update", ":id") }}'.replace(':id', id);
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteForm').action = '{{ route("admin.products.destroy", ":id") }}'.replace(':id', id);

    showImage();

    const tbody = document.getElementById('variantsTableBody');
    if (tbody) {
        tbody.innerHTML = prod.variants?.length > 0
            ? prod.variants.map(v => `
                <tr>
                    <td>${v.color || '—'}</td>
                    <td>${v.size || '—'}</td>
                    <td>${v.gender || '—'}</td>
                    <td>₹${Number(v.price || 0).toFixed(2)}</td>
                    <td>${v.quantity || 0}</td>
                </tr>
            `).join('')
            : '<tr><td colspan="5" style="text-align:center;padding:20px;color:#888;">No variants found</td></tr>';
    }

    // ── Existing Images with Remove Button ────────────────────────────────
    const preview = document.getElementById('existingImagesPreview');
    if (preview) {
        preview.innerHTML = ''; // clear previous content

        if (!prod.images || prod.images.length === 0) {
            preview.innerHTML = '<p style="color:#888; text-align:center;">No existing images</p>';
        } else {
            prod.images.forEach((imageUrl, index) => {
                // Create wrapper for positioning the cross button
                const imgWrapper = document.createElement('div');
                imgWrapper.style.position = 'relative';
                imgWrapper.style.display = 'inline-block';
                imgWrapper.style.margin = '4px';

                // Image element
                const img = document.createElement('img');
                img.src = imageUrl;
                img.alt = 'Existing product image';
                img.style.maxWidth = '100px';
                img.style.borderRadius = '6px';
                img.style.display = 'block';
                img.style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';

                // Remove button (cross)
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.innerHTML = '×';
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '-10px';
                removeBtn.style.right = '4px';
                removeBtn.style.background = 'none';
                removeBtn.style.color = 'red';
                removeBtn.style.border = 'none';
                removeBtn.style.borderRadius = '50%';
                removeBtn.style.fontSize = '89px';
                removeBtn.style.fontWeight = 'bold';
                removeBtn.style.cursor = 'pointer';
                removeBtn.style.lineHeight = '1';
                removeBtn.style.boxShadow = '0 1px 4px rgba(0,0,0,0.4)';
                removeBtn.title = 'Remove this image';

                // Click handler to delete image
                removeBtn.onclick = async () => {
                    if (!confirm('Are you sure you want to delete this image? This cannot be undone.')) {
                        return;
                    }

                
                    const parts = imageUrl.split('/');
                    const fileNameWithExt = parts[parts.length - 1];
                    const imageId = fileNameWithExt.split('.')[0]; // remove .jpg / .png etc.

                    try {
                        const response = await fetch(`/admin/products/images/${imageId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            imgWrapper.remove(); 
                            // Optional: remove from local data
                            prod.images.splice(index, 1);
                            alert('Image removed successfully');
                        } else {
                            alert(data.message || 'Failed to remove image');
                        }
                    } catch (error) {
                        console.error('Delete error:', error);
                        alert('Error removing image. Please try again.');
                    }
                };

                imgWrapper.appendChild(img);
                imgWrapper.appendChild(removeBtn);
                preview.appendChild(imgWrapper);
            });
        }
    }

    document.getElementById('viewSection').style.display = mode === 'view' ? 'block' : 'none';
    document.getElementById('editSection').style.display = mode === 'edit' ? 'block' : 'none';

    if (mode === 'edit') {
        setTimeout(() => {
            initEditDropzone();
        }, 100);
    }

    openModal('detailModal');
}

function switchToEdit() {
    document.getElementById('viewSection').style.display = 'none';
    document.getElementById('editSection').style.display = 'block';
    setTimeout(initEditDropzone, 100);
}

// ── Click Handlers ─────────────────────────────────────────
document.addEventListener('click', e => {
    const btn = e.target.closest('.view-btn, .edit-btn, .stock-btn, .delete-btn');
    if (!btn) return;
    e.preventDefault();

    const id = btn.dataset.id;

    if (btn.classList.contains('view-btn')) openDetail(id, 'view');
    if (btn.classList.contains('edit-btn')) openDetail(id, 'edit');
    if (btn.classList.contains('stock-btn')) {
        document.getElementById('stockProductId').value = id;
        document.getElementById('stockProductName').textContent = productsData[id]?.name || 'Product';
        loadVariants(id);
        openModal('stockModal');
    }
    if (btn.classList.contains('delete-btn')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').action = '{{ route("admin.products.destroy", ":id") }}'.replace(':id', id);
        openModal('deleteModal');
    }
});

// ── Create Form + Dropzone ─────────────────────────────────
Dropzone.autoDiscover = false;

const myDropzone = new Dropzone("#dropzoneForm", {
    paramName: "images[]",
    uploadMultiple: true,
    parallelUploads: 5,
    maxFilesize: 5,
    acceptedFiles: "image/*",
    addRemoveLinks: true,
    autoProcessQueue: false,
    previewsContainer: ".dz-previews-wrapper",
});

document.getElementById("dropzoneForm")?.addEventListener("submit", async e => {
    e.preventDefault();
    e.stopPropagation();

    const btn = document.getElementById("saveProductBtn");
    btn.disabled = true;
    btn.textContent = "Saving...";

    const formData = new FormData(e.target);
    myDropzone.files.forEach(file => formData.append('images[]', file));

    try {
        const res = await fetch(e.target.action, {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        });
        const data = await res.json();

        if (res.ok && data.success) {
            alert('Product saved successfully!');
            location.reload();
        } else {
            alert(data.message || 'Something went wrong');
        }
    } catch (err) {
        alert('Failed: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.textContent = "Save Product";
    }
});

// ── Edit Form Submit (with method spoofing) ────────────────
document.getElementById("editForm")?.addEventListener("submit", async e => {
    e.preventDefault();
    e.stopPropagation();

    const btn = document.getElementById("updateProductBtn");
    btn.disabled = true;
    btn.textContent = "Updating...";

    const formData = new FormData(e.target);
    formData.append('_method', 'PUT');

    if (editDropzone?.files?.length > 0) {
        editDropzone.files.forEach(file => formData.append('images[]', file));
    }

    try {
        const res = await fetch(e.target.action, {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        });
        const data = await res.json();

        if (res.ok && data.success) {
            alert('Product updated successfully!');
            location.reload();
        } else {
            alert(data.message || 'Update failed');
        }
    } catch (err) {
        alert('Failed: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.textContent = "Update Product";
    }
});

// ── Variant add/remove (create) ────────────────────────────
let variantIndex = 1;

function addVariant() {
    variantIndex++;
    document.getElementById('variantContainer').insertAdjacentHTML('beforeend', `
        <div class="variant-row" style="display:flex;gap:10px;margin-bottom:8px;flex-wrap:wrap;">
            <input type="text" name="variants[${variantIndex}][color]" placeholder="Color">
            <input type="text" name="variants[${variantIndex}][size]" placeholder="Size">
            <input type="text" name="variants[${variantIndex}][gender]" placeholder="Gender">
            <input type="number" name="variants[${variantIndex}][price]" placeholder="Price" step="0.01">
            <input type="number" name="variants[${variantIndex}][quantity]" placeholder="Stock" min="0">
            <button type="button" onclick="removeVariant(this)" style="background:#c00;color:white;">X</button>
        </div>
    `);
}

function removeVariant(btn) {
    btn.closest('.variant-row').remove();
}

// ── Stock Management ───────────────────────────────────────
let currentVariants = [];

async function loadVariants(productId) {
        currentProductId = productId; // Set the current product ID

    try {
        const res = await fetch(`/admin/products/${productId}/variants`);
        if (!res.ok) throw new Error('Failed');
        currentVariants = await res.json();

        document.getElementById('variantList').innerHTML = currentVariants.length === 0
            ? '<p style="color:#888; text-align:center;">No variants yet.</p>'
            : currentVariants.map(v => `
                <div class="stock-item">
                    <div>
                        <strong>${v.color || ''} ${v.size || ''} ${v.gender || ''}</strong><br>
                        <small>₹${Number(v.price || 0).toFixed(2)} | Qty: ${v.quantity}</small>
                    </div>
                    <button onclick="editStock(${v.id}, ${v.price}, ${v.quantity}, '${v.color||''}', '${v.size||''}', '${v.gender||''}')">Edit</button>
                     <button style="background:red; color:#fff;"
                            onclick="confirmDeleteVariant(${v.id})">
                            Delete
                        </button>
                    </div>
            `).join('');

        const avg = currentVariants.reduce((sum, v) => sum + (parseFloat(v.price||0) * (parseInt(v.quantity||0))), 0) /
                    currentVariants.reduce((sum, v) => sum + (parseInt(v.quantity||0)), 0) || 0;

       // document.getElementById('averagePriceInfo').innerHTML = `Current average price: ₹${avg.toFixed(2)}`;

        document.getElementById('stockEditForm').style.display = 'none';
    } catch (err) {
        document.getElementById('variantList').innerHTML = '<p style="color:#f00;">Failed to load variants</p>';
    }
}
let deleteVariantId = null;
let currentProductId = null;

function confirmDeleteVariant(id) {
    deleteVariantId = id;
    openModal('variantDeleteModal'); 
}

async function deleteVariant() {
    if (!deleteVariantId) return;

    try {
        const res = await fetch(`/admin/products/variant/${deleteVariantId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (data.success) {
            closeModal('variantDeleteModal');

            // reload variants
            loadVariants(currentProductId);

        } else {
            alert('Delete failed');
        }

    } catch (err) {
        alert('Error deleting variant');
    }
}

function showStockForm(isEdit = false) {
    document.getElementById('stockEditForm').style.display = 'block';
    document.getElementById('stockFormTitle').textContent = isEdit ? 'Edit Variant' : 'Add New Variant';
}

function editStock(id, price, qty, color = '', size = '', gender = '') {
    showStockForm(true);
    document.getElementById('editVariantId').value = id;
    document.getElementById('stockColor').value = color;
    document.getElementById('stockSize').value = size;
    document.getElementById('stockGender').value = gender;
    document.getElementById('stockPrice').value = price;
    document.getElementById('stockQuantity').value = qty;
}

document.getElementById('addVariantBtn')?.addEventListener('click', () => {
    showStockForm(false);
    document.getElementById('stockForm').reset();
    document.getElementById('editVariantId').value = '';
});

document.getElementById('stockForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    const productId   = formData.get('product_id');
    const variantId   = formData.get('variant_id') || ''; // empty string = new variant
    const newPrice    = parseFloat(formData.get('price')) || 0;
    const newQuantity = parseInt(formData.get('quantity')) || 0;

    if (isNaN(newPrice) || newPrice < 0) {
        alert('Please enter a valid price');
        return;
    }
    if (isNaN(newQuantity) || newQuantity < 0) {
        alert('Please enter a valid quantity');
        return;
    }

    const csrfTokenInput = form.querySelector('input[name="_token"]');
    if (!csrfTokenInput) {
        alert('CSRF token not found in form. Page may need refresh.');
        return;
    }

    try {
        // 1. Fetch current variants for average price preview
        const variantsRes = await fetch(`/admin/products/${productId}/variants`);
        if (!variantsRes.ok) throw new Error(`Failed to load variants: ${variantsRes.status}`);

        const variants = await variantsRes.json();

        let totalValue = 0;
        let totalQty   = 0;

        variants.forEach(v => {
            totalValue += (parseFloat(v.price) || 0) * (parseInt(v.quantity) || 0);
            totalQty   += parseInt(v.quantity) || 0;
        });

        totalValue += newPrice * newQuantity;
        totalQty   += newQuantity;

        const avgPrice = totalQty > 0 ? (totalValue / totalQty).toFixed(2) : '0.00';

        document.getElementById('averagePriceInfo').innerHTML = 
            `<strong style="color:#ff8c00;">After save → average price: ₹${avgPrice}</strong>`;

        const url = variantId
            ? `/admin/products/variant/${variantId}`
            : `/admin/products/stock/store`;

        const method = 'POST'; // both cases use POST in your routes probably

        const submitResponse = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrfTokenInput.value,
                'Accept': 'application/json',
            },
            body: formData
        });

        let result;
        try {
            result = await submitResponse.json();
        } catch (jsonErr) {
            console.error('Response was not valid JSON', jsonErr, await submitResponse.text());
            throw new Error('Server returned invalid response');
        }

        if (submitResponse.ok && result?.success) {
            alert(result.message || `Variant saved! New average price: ₹${avgPrice}`);
            await loadVariants(productId); // refresh list
            form.reset();
            document.getElementById('editVariantId').value = '';
            document.getElementById('stockEditForm').style.display = 'none';
            document.getElementById('averagePriceInfo').innerHTML = ''; // clear preview
        } else {
            const errorMsg = result?.message 
                || result?.errors 
                    ? Object.values(result.errors || {}).flat().join('\n')
                    : `Server error (${submitResponse.status})`;
            alert(errorMsg || 'Failed to save variant');
        }
    } catch (err) {
        console.error('Stock save failed:', err);
        alert('Failed to process request: ' + (err.message || 'Unknown error'));
    }
});

</script>

@endsection