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
        <div class="d-flex justify-content-between">
         <h4 style="color:#ff8c00;">Stripe Account Status</h4>

        <div id="stripeStatusBox">
            <span style="padding:6px 12px; border-radius:6px; background:#444; color:#fff;">
              Checking...
            </span>
        </div>
        </div>
        <a href="{{ route('admin.stripe.connect') }}" 
        style="display:inline-block; margin-top:10px; padding:10px 20px; background:#ff8c00; color:black; border-radius:8px; text-decoration:none; font-weight:bold;">
            Connect / Update Stripe
        </a>
    </div>

    <!-- Balance -->


    <div class="d-flex gap-4 flex-wrap mb-4">
        <div style="background:#1e1e1e; padding:20px; border-radius:12px; color:white; flex:1; min-width:250px;">
            <h4 style="color: #ff8c00;">Pending Balance</h4>
            <h2>₹{{ number_format($pending,2) }}</h2>
        </div>

        <div style="background:#1e1e1e; padding:20px; border-radius:12px; color:white; flex:1; min-width:250px;">
            <h4 style="color: #ff8c00;">Available Balance</h4>
            <h2>₹{{ number_format($available,2) }}</h2>
        </div>
    </div>


<!-- Table section -->
 <div style="background:#1e1e1e; padding:20px; border-radius:12px; margin-top:20px; ">
 <h4 style="color:#ff8c00;">Top Demanding Variants</h4>
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
                    <img class="rounded" src="{{ $image }}" width="110"><br>
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
 </div>
    <div class="alert alert-info">No notify requests found</div>
@endif
    </div>

    <div style="background:#1e1e1e; padding:20px; border-radius:12px; margin-top:20px;">
    
    <h4 style="color:#ff8c00; margin-bottom:20px;">
        Monthly Revenue
    </h4>

    <canvas id="revenueChart" height="100"></canvas>

</div>


@endsection
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const revenueData = @json($revenues);

    const labels = revenueData.map(r => {
        const date = new Date(r.year, r.month - 1);
        return date.toLocaleString('default', { month: 'short' });
    });

    const values = revenueData.map(r => parseFloat(r.total));
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const canvas = document.getElementById('revenueChart');

    if (!canvas) return; // safety

    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (₹)',
                data: values,
                borderColor: '#ff8c00',
                backgroundColor: 'rgba(255, 140, 0, 0.15)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#ff8c00',
                pointBorderColor: '#fff',
                pointRadius: 4
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: { color: '#fff' }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#ccc' },
                    grid: { color: '#333' }
                },
                y: {
                    ticks: { color: '#ccc' },
                    grid: { color: '#333' }
                }
            }
        }
    });

});
</script>
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