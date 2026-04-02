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
        <!-- Seller Filter -->


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