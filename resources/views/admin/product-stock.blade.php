{{-- resources/views/admin/product-stock.blade.php --}}
@extends('layouts.app')
@section('title', 'Manage Stock - ' . $product->name)
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

@section('content')
<style>
   
</style>
<div class="stock-container">
    <h2 style="color:#ff8c00; text-align:center; margin-bottom:16px;">Manage Stock for: {{ $product->name }}</h2>
    <div class="product-info">
        <p style="margin:8px 0; white-space: pre-wrap;">{{ $product->description }}</p>
        <div class="product-images">
            @foreach($product->images as $image)
                <img src="{{ $image->image }}" alt="{{ $product->name }}">
            @endforeach
        </div>
    </div>
    
    <h3 style="color:#ff8c00; margin-bottom:12px;">Current Variants Details</h3>
    <table class="variants-table">
        <thead>
            <tr>
                <th>Color</th>
                <th>Size</th>
                <th>Gender</th>
                <th>Price</th>
                <th>Current Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($variants as $variant)
                <tr>
                    <td>{{ $variant->color ?? '—' }}</td>
                    <td>{{ $variant->size ?? '—' }}</td>
                    <td>{{ $variant->gender ?? '—' }}</td>
                    <td>₹{{ number_format($variant->price, 2) }}</td>
                    <td>{{ $variant->quantity }}</td>
                    <td>
                        <a href="#" onclick="editVariant({{ $variant->id }})" style="color:#ff8c00;">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:#888;">No variants yet. Add one below.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="edit-section">
        <h3 style="color:#ff8c00; margin-bottom:16px;">Add / Update Stock</h3>
        <form method="POST" action="{{ route('admin.products.stock.store') }}" enctype="multipart/form-data" id="stockForm">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="edit-row">
                <select name="variant_id" id="variantSelect" style="flex:2;">
                    <option value="">Select Existing Variant</option>
                    @foreach($variants as $variant)
                        <option value="{{ $variant->id }}" data-color="{{ $variant->color }}" data-size="{{ $variant->size }}" data-gender="{{ $variant->gender }}" data-price="{{ $variant->price }}">
                            {{ $variant->color ?? '' }} {{ $variant->size ?? '' }} {{ $variant->gender ?? '' }} (₹{{ $variant->price }})
                        </option>
                    @endforeach
                </select>
                <button type="button" class="new-variant-option" onclick="toggleNewVariant()">+ New Variant</button>
            </div>
            <div id="newVariantFields" style="display:none;">
                <div class="edit-row">
                    <input type="text" name="color" placeholder="Color" id="newColor">
                    <input type="text" name="size" placeholder="Size" id="newSize">
                    <input type="text" name="gender" placeholder="Gender" id="newGender">
                </div>
                <div class="edit-row">
                    <input type="number" name="price" placeholder="Price" step="0.01" required id="newPrice">
                    <input type="number" name="quantity" placeholder="Add Quantity" min="1" required>
                </div>
            </div>
            <div class="edit-row" id="existingVariantFields" style="display:none;">
                <input type="number" name="quantity" placeholder="Update Quantity" min="0" required>
            </div>
            <div class="edit-row">
                <input type="file" name="images[]" multiple accept="image/*" placeholder="Upload Images (Optional)">
            </div>
            <div class="stock-actions">
                <button type="submit" class="update-btn">Update Stock</button>
                <a href="{{ route('admin.my-products') }}" class="back-btn" style="text-decoration:none; padding:10px 20px; border-radius:6px; display:inline-block;">Back to Products</a>
            </div>
        </form>
    </div>
</div>
<script>
function toggleNewVariant() {
    const fields = document.getElementById('newVariantFields');
    const existing = document.getElementById('existingVariantFields');
    const select = document.getElementById('variantSelect');
    if (fields.style.display === 'none') {
        fields.style.display = 'block';
        existing.style.display = 'none';
        select.value = '';
    } else {
        fields.style.display = 'none';
        // Clear new fields
        document.getElementById('newColor').value = '';
        document.getElementById('newSize').value = '';
        document.getElementById('newGender').value = '';
        document.getElementById('newPrice').value = '';
    }
}
document.getElementById('variantSelect').addEventListener('change', function() {
    const existing = document.getElementById('existingVariantFields');
    const newFields = document.getElementById('newVariantFields');
    if (this.value) {
        existing.style.display = 'block';
        newFields.style.display = 'none';
        // Auto-populate price if needed, but for update, quantity is main
    } else {
        existing.style.display = 'none';
    }
});
function editVariant(id) {
    // For simplicity, selecting it in dropdown triggers edit mode
    const option = document.querySelector(`#variantSelect option[value="${id}"]`);
    if (option) {
        document.getElementById('variantSelect').value = id;
        document.getElementById('variantSelect').dispatchEvent(new Event('change'));
    }
}
</script>
@endsection