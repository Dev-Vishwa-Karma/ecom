@extends('layouts.app')

@section('title','Customer Profile')

@section('content')
<style>
    @media (max-width: 768px) {
    .layout {
        border: 1px solid #ff8c00;
        flex-direction: column;
        align-items: center;
        padding: 25px;
        border-radius: 30px;
    }
}
</style>
<div style="display:flex; gap:40px;">
    @php
        $profileImage = auth()->user()->images()->first();
    @endphp

    <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">

        {{-- PROFILE IMAGE CIRCLE --}}
        <img id="profileCircle"
     src="{{ $profileImage ? $profileImage->image : asset('profile.jpeg') }}"
     style="width:150px; height:150px; border-radius:50%; object-fit:cover; cursor:pointer; border:1px solid #ccc;"
     onclick="document.getElementById('imageInput').click();">

        {{-- UPLOAD / CHANGE / SAVE IMAGE FORM --}}
        <form method="POST" action="{{ route('user.images.store') }}" enctype="multipart/form-data" id="uploadForm">
            @csrf
            <input type="file" name="image" id="imageInput" accept="image/*" style="display:none" onchange="previewImage(event)">

            {{-- Show Upload Image button only if no image --}}
            @if(!$profileImage)
                <button type="button" id="uploadBtn" onclick="document.getElementById('imageInput').click();">Upload Image</button>
            @endif

            {{-- Change + Save buttons, only visible after selecting a file --}}
            <div id="changeSaveBtns" style="display:none; margin-top:10px; flex-direction: column; gap:5px;">
                <button type="button" onclick="document.getElementById('imageInput').click();">Change Image</button>
                <button type="submit">Save Image</button>
            </div>
        </form>

        {{-- Update + Delete buttons after image saved --}}
        @if($profileImage)
            <div id="actionBtns" style=" flex-direction: row; gap:5px; display:flex;">
                <button type="button" onclick="document.getElementById('imageInput').click();"><i class="bi bi-pencil-fill"></i></button>

                <form method="POST" action="{{ route('user.images.delete', $profileImage->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete image?')"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        @else
            <div id="actionBtns" style="margin-top:10px; flex-direction: column; gap:5px; display:none;"></div>
        @endif

    </div>

    {{-- USER DETAILS --}}
    <div>
        @if(session('success'))
            <div style="color:green; margin-bottom:15px;">
                {{ session('success') }}
            </div>
        @endif
        <div style="margin-bottom:30px; padding:15px; ">
            <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
            <p><strong>Mobile:</strong> {{ auth()->user()->mobile }}</p>
            <p><strong>Address:</strong> {{ auth()->user()->address }}</p>
        </div>
    </div>
</div>

<script>
function previewImage(event){
    const file = event.target.files[0];
    if(!file) return;

    const reader = new FileReader();
    reader.onload = function() {
        document.getElementById('profileCircle').src = reader.result;

        // Hide upload button
        const uploadBtn = document.getElementById('uploadBtn');
        if(uploadBtn) uploadBtn.style.display = 'none';

        // Show Change + Save buttons
        document.getElementById('changeSaveBtns').style.display = 'flex';

        // Hide action buttons while previewing
        const actionBtns = document.getElementById('actionBtns');
        if(actionBtns) actionBtns.style.display = 'none';
    }
    reader.readAsDataURL(file);
}

// Prevent form submit if no file selected
document.getElementById('uploadForm').addEventListener('submit', function(e){
    const input = document.getElementById('imageInput');
    if(!input.files || input.files.length === 0){
        e.preventDefault();
        alert('Please select an image first!');
        return false;
    }
});
</script>
@endsection