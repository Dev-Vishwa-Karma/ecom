<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
   protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        $schedule->command('orders:status-cron')
            ->cron('*/10 * * * *') // run every 10 minutes
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/order-status-cron.log'));
    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
