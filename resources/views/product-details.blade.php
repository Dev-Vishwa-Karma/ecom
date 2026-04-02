@extends('layouts.app')

@section('title',$product->name)
<link rel="stylesheet" href="{{ asset('css/product-details.css') }}">

@section('content')

<style>

</style>

<div class="product-page">

{{-- LEFT SIDE --}}
<div>
    <img id="bigImage" src="{{ $product->images->first()?->image }}" class="big-image">
    <div class="thumb-images">
        @foreach($product->images as $img)
            <img src="{{ $img->image }}" onclick="changeImage('{{ $img->image }}')">
        @endforeach
    </div>
</div>

{{-- RIGHT SIDE --}}
<div>
    <h2>{{ $product->name }}</h2>
    <p>{{ $product->description }}</p>

    {{-- COLOR --}}
    <div class="variant-group" id="colorSection" style="display:none">
        <label>Color</label>
        <div class="variant-options" id="colorOptions"></div>
    </div>

    {{-- SIZE --}}
    <div class="variant-group" id="sizeSection" style="display:none">
        <label>Size</label>
        <div class="variant-options" id="sizeOptions"></div>
    </div>

    {{-- GENDER --}}
    <div class="variant-group" id="genderSection" style="display:none">
        <label>Gender</label>
        <div class="variant-options" id="genderOptions"></div>
    </div>

    {{-- QUANTITY --}}
    <div class="variant-group" id="quantitySection" style="display:none; margin-bottom:20px;">
        <label>Quantity</label>
        <input type="number" name="quantity" id="quantityInput" value="1" min="1">
    </div>

    <input type="hidden" id="hiddenVariantId" name="variant_id" value="">
    <input type="hidden" id="hiddenQuantity" name="quantity" value="">
    <input type="hidden" id="hiddenTotalPrice" name="total_price" value="">
    <h3 id="priceText" style="margin-bottom:20px;">Select variant</h3>

    <a id="buyBtn" href="javascript:void(0)" class="buy-btn">Buy Now</a>
</div>
</div>

<h2>More Products</h2>
<div class="cards">
    @foreach($products as $p)
        <a href="{{ route('product.details',$p->id) }}" class="card">
            <img src="{{ $p->images->first()?->image }}">
            <h4>{{ $p->name }}</h4>
            <p>₹{{ $p->variants->min('price') }}</p>
        </a>
    @endforeach
    <input type="hidden" id="sellerId" value="{{ $product->user_id }}">
</div>

<script>
const variants = @json($product->variants);
let selectedColor = null, selectedSize = null, selectedGender = null, selectedQuantity = 1;

function changeImage(src){ document.getElementById("bigImage").src = src; }

// GENERIC FUNCTION TO CREATE VARIANT BOXES
function createVariantBox(containerId, value, type){
    const container = document.getElementById(containerId);
    const div = document.createElement('div');
    div.className='variant-box';
    div.innerText=value;

    let stock=variants.filter(v=>{
        if(type==='color') return v.color===value;
        if(type==='size') return (!selectedColor || v.color===selectedColor) && v.size===value;
        if(type==='gender') return (!selectedColor || v.color===selectedColor) && (!selectedSize || v.size===selectedSize) && v.gender===value;
    }).reduce((a,v)=>a+v.quantity,0);

    if(stock===0){
        div.classList.add('disabled');
        div.innerHTML+=`<div class="variant-stock">Out of Stock</div>`;
    }
    else if(stock<=5){
        div.innerHTML+=`<div class="variant-stock">Only ${stock} left</div>`;
    }

    div.onclick=()=>{

        const buyBtn=document.getElementById('buyBtn');

        container.querySelectorAll('.variant-box').forEach(b=>b.classList.remove('active'));
        div.classList.add('active');

        if(type==='color'){
            selectedColor=value;
            selectedSize=null;
            selectedGender=null;
            document.getElementById('quantitySection').style.display='none';
            loadSizes();
        }

        if(type==='size'){
            selectedSize=value;
            selectedGender=null;
            document.getElementById('quantitySection').style.display='none';
            loadGender();
        }

        if(type==='gender'){
            selectedGender=value;

            if(stock===0){
                document.getElementById('quantitySection').style.display='none';
            }else{
                selectedQuantity=1;
                document.getElementById('quantityInput').value=1;
                document.getElementById('quantitySection').style.display='block';
            }

            showPrice();
        }

        if(stock===0){
            buyBtn.innerText='Notify Me';
            buyBtn.classList.add('disabled');
        }else{
            buyBtn.innerText='Buy Now';
            buyBtn.classList.remove('disabled');
        }

    }

    container.appendChild(div);
}

// LOAD VARIANTS
function loadColors(){
    const colors=[...new Set(variants.map(v=>v.color).filter(c=>c))];
    const colorContainer=document.getElementById('colorOptions');
    colorContainer.innerHTML='';

    if(colors.length>0){
        colors.forEach(c=>createVariantBox('colorOptions',c,'color'));
        document.getElementById('colorSection').style.display='block';
    }else{
        document.getElementById('colorSection').style.display='none';
        loadSizes();
    }
}

function loadSizes(){
    const sizes=[...new Set(variants.filter(v=>(!selectedColor||v.color===selectedColor)&&v.size).map(v=>v.size))];
    const sizeContainer=document.getElementById('sizeOptions');
    sizeContainer.innerHTML='';

    if(sizes.length>0){
        sizes.forEach(s=>createVariantBox('sizeOptions',s,'size'));
        document.getElementById('sizeSection').style.display='block';
    }else{
        document.getElementById('sizeSection').style.display='none';
        selectedSize=null;
        loadGender();
    }
}

function loadGender(){
    const genders=[...new Set(variants.filter(v=>(!selectedColor||v.color===selectedColor)&&(!selectedSize||v.size===selectedSize)&&v.gender).map(v=>v.gender))];
    const genderContainer=document.getElementById('genderOptions');
    genderContainer.innerHTML='';

    if(genders.length>0){
        genders.forEach(g=>createVariantBox('genderOptions',g,'gender'));
        document.getElementById('genderSection').style.display='block';
    }else{
        document.getElementById('genderSection').style.display='none';
        selectedGender=null;
    }
}

// PRICE
function showPrice(){

    const variant=variants.find(v=>
        (!selectedColor||v.color===selectedColor) &&
        (!selectedSize||v.size===selectedSize) &&
        (!selectedGender||v.gender===selectedGender)
    );

    const buyBtn=document.getElementById('buyBtn');

    if(!variant) return;

    if(variant.quantity===0){
        document.getElementById('priceText').innerText=`Out of Stock`;
        document.getElementById('quantitySection').style.display='none';
        buyBtn.innerText='Notify Me';
        buyBtn.classList.add('disabled');
        return;
    }

    const qty=selectedQuantity||1;
    const totalPrice=qty*parseFloat(variant.price);

    document.getElementById('priceText').innerText=`Price ₹${totalPrice.toFixed(2)} (${qty} pcs)`;

    document.getElementById('quantityInput').max=variant.quantity;

    document.getElementById('hiddenVariantId').value=variant.id;
    document.getElementById('hiddenQuantity').value=selectedQuantity;
    document.getElementById('hiddenTotalPrice').value=totalPrice.toFixed(2);
}

// QUANTITY CHANGE
document.getElementById('quantityInput').addEventListener('input',function(){

    let val=parseInt(this.value)||1;

    const variant=variants.find(v=>
        (!selectedColor||v.color===selectedColor) &&
        (!selectedSize||v.size===selectedSize) &&
        (!selectedGender||v.gender===selectedGender)
    );

    if(!variant) return;

    if(val>variant.quantity) val=variant.quantity;
    if(val<1) val=1;

    selectedQuantity=val;
    this.value=val;

    showPrice();
});

// BUY CLICK
document.getElementById('buyBtn').addEventListener('click',function(){

    const variant = variants.find(v =>
        (!selectedColor || v.color===selectedColor) &&
        (!selectedSize || v.size===selectedSize) &&
        (!selectedGender || v.gender===selectedGender)
    );

    if(!variant){
        alert('Please select variant');
        return;
    }

    // NOTIFY
    if(variant.quantity === 0){

        const sellerId = document.getElementById('sellerId').value;

        fetch("{{ route('notify.store') }}",{
            method:"POST",
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":"{{ csrf_token() }}"
            },
            body:JSON.stringify({
                seller_id:sellerId,
                variant_id:variant.id
            })
        })
        .then(res=>res.json())
        .then(data=>{
            alert(data.message);
        });

        return;
    }

    // BUY
    const qty = selectedQuantity || 1;
    const url = `{{ route('buy.now',$product->id) }}?variant_id=${variant.id}&quantity=${qty}`;
    window.location.href = url;

});

// INIT
window.addEventListener('load',()=>{ loadColors(); });

</script>

@endsection