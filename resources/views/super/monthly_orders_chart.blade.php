
<div class="container mt-4">
    <h3>Orders for {{ $month }}/{{ $year }}</h3>

    <div class="row mt-4">
        <div class="col-md-6">
            <canvas id="ordersChart" height="50"></canvas>
        </div>
        <div class="col-md-6">
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Delivered
                    <span class="badge bg-success rounded-pill">{{ $percentages['delivered'] ?? 0}}%</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Cancelled
                    <span class="badge bg-danger rounded-pill">{{ $percentages['cancelled'] ?? 0}}%</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Pending
                    <span class="badge bg-warning rounded-pill">{{ $percentages['pending'] ?? 0}}%</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Processing
                    <span class="badge bg-primary rounded-pill">{{ $percentages['processing'] ?? 0}}%</span>
                </li>
            </ul>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Orders chart
    const percentages = @json($percentages);
    const ordersCanvas = document.getElementById('ordersChart');
    if (ordersCanvas) {
        const ctxOrders = ordersCanvas.getContext('2d');
        new Chart(ctxOrders, {
            type: 'doughnut',
            data: {
                labels: ['Delivered','Cancelled','Pending','Processing'],
                datasets: [{
                    data: [
                        percentages['delivered'] ?? 0,
                        percentages['cancelled'] ?? 0,
                        percentages['pending'] ?? 0,
                        percentages['processing'] ?? 0
                    ],
                    backgroundColor: ['#28a745','#dc3545','#ffc107','#007bff']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { color: 'white' } }
                }
            }
        });
    }

    // Revenue chart
    const revenues = @json($revenues->toArray());
    const revenueCanvas = document.getElementById('revenueChart');
    if (revenueCanvas) {
        const ctxRevenue = revenueCanvas.getContext('2d');
        const monthNames = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        const labels = revenues.map(r => monthNames[r.month-1]+'-'+r.year);
        const data = revenues.map(r => r.total);

        new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue ($)',
                    data: data,
                    backgroundColor: 'rgba(255,140,0,0.7)',
                    borderColor: 'rgba(255,140,0,1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { labels: { color: 'white' } } },
                scales: {
                    x: { ticks: { color: 'white' }, grid: { color: '#444' } },
                    y: { ticks: { color: 'white' }, grid: { color: '#444' } }
                }
            }
        });
    }

});
</script>
@endsection