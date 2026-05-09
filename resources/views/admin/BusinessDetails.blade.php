@extends('layouts.app')

@section('title', 'Business Details')

<style>
.container-box {
    max-width: 800px;
    margin: auto;
    background: #1e1e1e;
    padding: 25px;
    border-radius: 12px;
    color: #fff;
}

.form-group { margin-bottom: 15px; }

.form-control {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #444;
    background: #2a2a2a;
    color: #fff;
}

.form-control:focus {
    border-color: #ff8c00 !important;
    box-shadow: 0 0 5px #ff8c00 !important;
    outline: none !important;
}

label { font-weight: 600; }

.preview-img {
    margin-top: 10px;
    max-height: 120px;
    border-radius: 8px;
    border: 1px solid #444;
    display: none;
}

.btn-main {
    background: #ff8c00;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    color: black;
    font-weight: bold;
    cursor: pointer;
}

.view-btn {
    margin-top: 8px;
    padding: 5px 10px;
    font-size: 12px;
    background: #444;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
</style>

@section('content')

<div class="container-box">

<h2 style="text-align:center;">Business Details</h2>
<span style="color:#ff8c00; font-size:18px; font-weight:bold;">Status: {{ ucfirst($data->status ?? 'Not Submitted') }}</span>

@if(session('success'))
<div style="background:#1a3a1a;color:#4dff88;padding:10px;border-radius:8px;text-align:center;">
    {{ session('success') }}
</div>
@endif

<form method="POST"
      action="{{ !empty($data) ? route('admin.business.update',$data->id) : route('admin.business.store') }}"
      enctype="multipart/form-data">

@csrf
@if(!empty($data)) @method('PUT') @endif

@php
    $isEdit = empty($data); // agar new hai to editable, warna readonly
@endphp

<!-- EMAIL -->
<div class="form-group">
<label>Email</label>
<input type="email" name="email" class="form-control editable"
       value="{{ $data->email ?? '' }}"
       {{ !$isEdit ? 'readonly' : '' }}>
</div>

<!-- BUSINESS NAME -->
<div class="form-group">
<label>Business Name</label>
<input type="text" name="business_name" class="form-control editable"
       value="{{ $data->business_name ?? '' }}"
       {{ !$isEdit ? 'readonly' : '' }}>
</div>

<!-- PHONE -->
<div class="form-group">
<label>Phone</label>
<input type="text" name="business_phone" class="form-control editable"
       value="{{ $data->business_phone ?? '' }}"
       {{ !$isEdit ? 'readonly' : '' }}>
</div>

<!-- BANK -->
<div class="form-group">
<label>Account No</label>
<input type="text" name="bank_account_number" class="form-control editable"
       value="{{ $data->bank_account_number ?? '' }}"
       {{ !$isEdit ? 'readonly' : '' }}>
</div>

<div class="form-group">
<label>IFSC</label>
<input type="text" name="ifsc_code" class="form-control editable"
       value="{{ $data->ifsc_code ?? '' }}"
       {{ !$isEdit ? 'readonly' : '' }}>
</div>

<div class="form-group">
<label>Holder Name</label>
<input type="text" name="account_holder_name" class="form-control editable"
       value="{{ $data->account_holder_name ?? '' }}"
       {{ !$isEdit ? 'readonly' : '' }}>
</div>

<!-- ADDRESS -->
<div class="form-group">
<label>Address</label>
<textarea name="business_address" class="form-control editable"
    {{ !$isEdit ? 'readonly' : '' }}>{{ $data->business_address ?? '' }}</textarea>
</div>

<!-- PAN -->
<div class="form-group">
<label>PAN</label>
<input type="file" name="pan_card" class="form-control editable"
       {{ !$isEdit ? 'disabled' : '' }}
       onchange="previewImage(event,'panPreview')">

@if(!empty($data->pan_card))
<button type="button" class="view-btn" onclick="togglePreview('panPreview')">
    View
</button>
<img src="{{ $data->pan_card }}" id="panPreview" class="preview-img">
@endif
</div>

<!-- GST -->
<div class="form-group">
<label>GST</label>
<input type="file" name="gst_certificate" class="form-control editable"
       {{ !$isEdit ? 'disabled' : '' }}
       onchange="previewImage(event,'gstPreview')">

@if(!empty($data->gst_certificate))
<button type="button" class="view-btn" onclick="togglePreview('gstPreview')">
    View
</button>
<img src="{{ $data->gst_certificate }}" id="gstPreview" class="preview-img">
@endif
</div>

<!-- BUTTON -->
<div style="text-align:center; margin-top:20px;">
<button type="button" id="editBtn" class="btn-main">
    {{ empty($data) ? 'Save' : 'Update' }}
</button>
</div>

</form>

</div>

<!-- CONFIRM MODAL -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:#1e1e1e; color:#fff;">
            
            <div class="modal-header">
                <h5>Confirm Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to update business details?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button"  id="confirmSaveBtn">
                    Yes, Save
                </button>
            </div>

        </div>
    </div>
</div>

<script>

// 👉 EDIT MODE TOGGLE
document.addEventListener("DOMContentLoaded", function () {

    const editBtn = document.getElementById('editBtn');
    const form = editBtn.closest('form');
    const fields = document.querySelectorAll('.editable');

    let isEditMode = false;

    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

    editBtn.addEventListener('click', function () {

        // 👉 FIRST CLICK (Update → Enable edit)
        if (!isEditMode) {

            fields.forEach(f => {
                f.removeAttribute('readonly');
                f.removeAttribute('disabled');
            });

            editBtn.innerText = 'Save';
            isEditMode = true;

        } else {

            // 👉 SECOND CLICK (Save → Open modal)
            confirmModal.show();
        }
    });

    // 👉 FINAL SAVE CONFIRM
    document.getElementById('confirmSaveBtn').addEventListener('click', function () {

        confirmModal.hide();

        // form submit
        form.submit();
    });

});

function previewImage(event,id){
    const file = event.target.files[0];
    const img = document.getElementById(id);

    if(file){
        img.src = URL.createObjectURL(file);
        img.style.display = 'block';
    }
}

function togglePreview(id){
    const img = document.getElementById(id);
    img.style.display = img.style.display === 'none' ? 'block' : 'none';
}
</script>

@endsection