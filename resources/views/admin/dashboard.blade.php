<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<style>

</style>
    <div class="container-fluid">
        <h2 class="mb-4">Welcome, Admin!</h2>

        {{-- STRIPE CONNECT SECTION --}}
<div style="background:#1e1e1e; padding:20px; border-radius:12px; margin-bottom:20px; color:white;">
    <h4 style="color:#ff8c00;">Stripe Account Status</h4>

    <div id="stripeStatusBox">
    <span style="padding:6px 12px; border-radius:6px; background:#444; color:#fff;">
        Checking...
    </span>
    </div>

    <a href="{{ route('admin.stripe.connect') }}" 
       style="display:inline-block; margin-top:10px; padding:10px 20px; background:#ff8c00; color:black; border-radius:8px; text-decoration:none; font-weight:bold;">
        Connect / Update Stripe
    </a>
</div>


<!-- Table section -->
 <h3>Top Requested Variants (Notify Me)</h3>
@if($topDemand ?? false && $topDemand->isNotEmpty())
   <table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>Product</th>
            <th>Variant</th>
            <th>Requests</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topDemand as $item)
        <tr>
            <!-- Product Column -->
            <td>
                <div class="d-flex">
                <div style="margin-right: 20px;"> 
                    @php
                    $product = $item->variant?->product;
                    $image = $product?->images->first()?->image ?? null;
                @endphp
                @if($image)
                    <img src="{{ $image }}" width="110"><br>
                @endif
                
                </div>
                
                <div>
                    <strong>{{ $product?->name ?? '—' }}</strong><br>
                Price: ₹{{ number_format($item->variant?->price ?? 0, 2) }}
                </div>
                </div>
            </td>

            <!-- Variant Column -->
            <td>
                @if($item->variant)
                    Color: {{ $item->variant->color ?? '—' }}<br>
                    Size: {{ $item->variant->size ?? '—' }}<br>
                    Gender: {{ $item->variant->gender ?? '—' }}
                @else
                    —
                @endif
            </td>

            <!-- Requests Column -->
            <td class="text-center">{{ $item->notify_count }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
    <div class="alert alert-info">No notify requests found</div>
@endif
    </div>
@endsection

</body>
</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {

    fetch("{{ route('admin.stripe.status') }}")
        .then(res => res.json())
        .then(data => {

            let html = '';

            // Not connected
            if (data.status === 'not_connected') {
                html = `<span style="background:red; color:white; padding:6px 12px; border-radius:6px;">
                            Not Connected
                        </span>`;
            } 

            //  Onboarding pending
            else if (!data.details_submitted) {
                html = `<span style="background:orange; color:black; padding:6px 12px; border-radius:6px;">
                            Pending (Complete Onboarding)
                        </span>`;
            } 

            //  Not ready for payments
            else if (!data.charges_enabled) {
                html = `<span style="background:#ffc107; color:black; padding:6px 12px; border-radius:6px;">
                            Verification Pending
                        </span>`;
            } 

            //  Fully active
            else {
                html = `<span style="background:green; color:white; padding:6px 12px; border-radius:6px;">
                            Active (Ready to Receive Payments)
                        </span>`;
            }

            document.getElementById('stripeStatusBox').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('stripeStatusBox').innerHTML =
                `<span style="background:red; color:white; padding:6px 12px; border-radius:6px;">
                    Error
                </span>`;
        });

});

</script>