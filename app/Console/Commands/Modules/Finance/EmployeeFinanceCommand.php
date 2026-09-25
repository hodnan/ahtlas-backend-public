<?php

namespace App\Console\Commands\Modules\Finance;

use App\Services\Modules\Employee\EmployeeFinanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;


class EmployeeFinanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fincance:employee-hc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $dateStart = Carbon::parse('2025-01-01')->startOfDay();
        $dateStart = Carbon::now()->subDay(5)->startOfDay();
        $dateEnd = Carbon::now()->subDay(1)->startOfDay();

        $positionLPU = EmployeeFinanceService::getPositionLpu();

        while ($dateStart->lte($dateEnd)) {

            foreach ($positionLPU as $item) {
                EmployeeFinanceService::getHcByPositionLpuAndDate(
                    $item->position_lpu,
                    $dateStart,
                    $item->CD_INDICADOR,
                    $item->NO_INDICADOR,
                );
            }

            $dateStart->addDay();
        }

        $monthStart = Carbon::parse($dateStart)->startOfMonth();
        $monthEnd = Carbon::parse($dateEnd)->startOfMonth();

        while ($monthStart->lte($monthEnd)) {

            EmployeeFinanceService::storeHCMonth($monthStart);

            $monthStart->addMonth();
        }

        return Command::SUCCESS;
    }
}
