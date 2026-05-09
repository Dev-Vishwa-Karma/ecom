@extends('layouts.app')

@section('title','Commission Dashboard')

@section('content')

<div class="container">

<h2>Charges Dashboard</h2>

<div style="background:#1f1f1f; padding:15px; border-radius:10px; margin-bottom:20px;">

<form method="GET" action="">
    <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">

        <!-- Month -->
        <select name="month"
            style="padding:8px 10px; border-radius:6px; border:1px solid #444; background:#2b2b2b; color:#fff;">
            <option value="">All Months</option>
            @for($m=1; $m<=12; $m++)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                    {{ date('F', mktime(0,0,0,$m,1)) }}
                </option>
            @endfor
        </select>

        <!-- Status -->
        <select name="status"
            style="padding:8px 10px; border-radius:6px; border:1px solid #444; background:#2b2b2b; color:#fff;">
            <option value="">All Status</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        </select>

        <!-- Search -->
        <input 
            type="text" 
            name="search"
            value="{{ request('search') }}"
            placeholder="Search name, email..."
            style="padding:8px 12px; border-radius:6px; border:1px solid #444; background:#2b2b2b; color:#fff; width:220px;"
        >

        <!-- Filter Button -->
        <button type="submit"
            style="padding:8px 14px; background:#ff8c00; color:#fff; border:none; border-radius:6px; cursor:pointer;">
            Apply
        </button>

        <!-- Reset -->
        <a href="{{ url()->current() }}"
            style="padding:8px 14px; background:#ff8c00; color:#fff; border-radius:6px; text-decoration:none;">
            Reset
        </a>

    </div>
</form>

</div>
<table >

<thead>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Month/Year</th>
    <th>Sales Revenue</th>
    <th>Commission (10%)</th>
    <th>Status</th>
</tr>
</thead>

<tbody>

@foreach($data as $row)

<tr>

<td>{{ $row->name }}</td>
<td>{{ $row->email }}</td>
<td>{{ $row->mobile }}</td>

<td>
    {{ $row->month }}/{{ $row->year }}
</td>

<td style="color:#4caf50;">
    ₹{{ number_format($row->total_amount,2) }}
</td>

<td style="color:#ff9800;">
    ₹{{ number_format($row->commission,2) }}
</td>

    <td>
        @if($row->status == 'paid')
            <span style="color:lightgreen;">Paid at </span>
            <span>{{$row->paid_at}}</span>
        @elseif($row->status == 'pending')
            <span style="color:orange;">Pending</span>
            @if($row->month != now()->month || $row->year != now()->year)
            <input style="cursor: pointer;" type='checkbox' onchange="markPaid({{ $row->charge_id }}, this.checked)" > Paid</input>
            @else
            <span></span>
            @endif
        @else
            <span style="color:red;">{{ ucfirst($row->status) }}</span>
        @endif
</td>


</tr>

@endforeach

</tbody>
</table>
</div>

<script>
function markPaid(id, checked) {

    if (checked) {

        fetch(`/super/admin/charges/${id}/paid`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload(); // simple fix
            }
        });

    }
}
</script>

@endsection