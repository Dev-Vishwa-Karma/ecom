<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SuperAdminChargeService;

class GenerateMonthlyCharges extends Command
{
    protected $signature = 'charges:generate';
    protected $description = 'Generate monthly super admin commission';

    public function handle(SuperAdminChargeService $service)
    {
        $month = now()->month;
        $year  = now()->year;

        $service->generate($month, $year);

        $this->info('Monthly charges generated successfully');
    }
}