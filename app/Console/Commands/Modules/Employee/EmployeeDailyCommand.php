<?php

namespace App\Console\Commands\Modules\Employee;

use Illuminate\Console\Command;
use App\Services\Modules\Employee\EmployeeDailyService;


class EmployeeDailyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:daily-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        EmployeeDailyService::setEmployee();

        return Command::SUCCESS;
    }
}
