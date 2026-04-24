<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrderStatusCron extends Command
{
    protected $signature = 'orders:status-cron';
    protected $description = 'Automatically update order statuses based on timing rules';

public function handle()
{
    $rules = config('order_status.statuses.dispatched');

    if (!$rules) {
        Log::warning('No dispatched rules found');
        return;
    }

    $query = Order::where('status', 'dispatched');

    // 🔥 dynamic time support
    if (isset($rules['minutes'])) {
        $query->where('updated_at', '<=', now()->subMinutes($rules['minutes']));
    }

    if (isset($rules['hours'])) {
        $query->where('updated_at', '<=', now()->subHours($rules['hours']));
    }

    if (isset($rules['days'])) {
        $query->where('updated_at', '<=', now()->subDays($rules['days']));
    }

    $updated = $query->update([
        'status' => $rules['next'] ?? 'delivered',
        'updated_at' => now()
    ]);

    if ($updated > 0) {
        Log::info("$updated orders moved to DELIVERED");
    } else {
        Log::info("No orders eligible");
    }

    Log::info('Order delivery cron executed at ' . now());

    $this->info('Done');
}

}
