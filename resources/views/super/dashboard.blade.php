    @extends('layouts.app')

    @section('title', 'Super Admin Dashboard')

    @section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Welcome, Super Admin!</h2>
<div>   
        <div>
        <span style="color:#ff8c00; font-size:18px; font-weight:bold;">Total App Revenue:</span>
            <h1 style="color:#28a745;">₹{{ number_format($totalRevenue, 2) }}</h1>

        </div>
        @include('super.app_revenue_graph', ['revenues' => $revenues])


        @include('super.monthly_orders_chart', ['stats' => $stats, 'percentages' => $percentages, 'month' => $month, 'year' => $year])
    </div>
        


       

    </div>
    @endsection