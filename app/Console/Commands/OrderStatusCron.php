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
        $statuses = config('order_status.statuses');

        foreach ($statuses as $current => $rule) {

            $next = $rule['next'] ?? null;
            if (!$next) continue; // safety

            // Determine timing
            if (isset($rule['hours'])) {
                $timeLimit = Carbon::now()->subHours($rule['hours']);
            } elseif (isset($rule['days'])) {
                $timeLimit = Carbon::now()->subDays($rule['days']);
            } else {
                continue;
            }

            // Update eligible orders
            $updated = Order::where('status', $current)
                ->where('status', '!=', 'cancelled') // never update cancelled
                ->where(function($q) use ($current, $timeLimit){
                    if ($current === 'pending') {
                        $q->where('created_at', '<=', $timeLimit);
                    } else {
                        $q->where('updated_at', '<=', $timeLimit);
                    }
                })
                ->update(['status' => $next]);

            if ($updated) {
                Log::info("OrderStatusCron: $updated orders updated from $current → $next at ".now());
            }
        }

        $this->info('OrderStatusCron ran successfully at '.now());
    }
}