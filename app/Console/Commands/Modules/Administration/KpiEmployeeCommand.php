<?php

namespace App\Console\Commands\Modules\Administration;

use App\Models\Modules\Administration\Intelligence\KpiEmployee;
use App\Services\Modules\Administration\KpiEmployeeService;
use Illuminate\Console\Command;

use Carbon\Carbon;


use Illuminate\Support\Facades\Log;

class KpiEmployeeCommand  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpi:employee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calcula e salva kpi aberto por setorN1 e setorN2';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $months = [
            [
                'start' => Carbon::today()->subMonth()->firstOfMonth()->toDateString(),
                'end' => Carbon::today()->subMonth()->endOfMonth()->toDateString()
            ],
            [
                'start' => Carbon::today()->firstOfMonth()->toDateString(),
                'end' => Carbon::today()->endOfMonth()->toDateString()
            ],
        ];

        // Pega lista de kpis ativos no Robbyson
        $kpis = KpiEmployeeService::getKpis();

        foreach ($months as $month) {
            try {
                KpiEmployee::whereBetween('month_ref', [$month['start'], $month['end']])->delete();
            } catch (\Throwable $th) {
                Log::error('KpiS1S2Command', ['delete error', $th->getMessage()]);
            }

            foreach ($kpis as $value) {
                KpiEmployeeService::setKpiEmployeeH0SubResults($month['start'],  $value['indicator_id'],999);
                KpiEmployeeService::setKpiEmployeeH1SubResults($month['start'],  $value['indicator_id'],999);
                KpiEmployeeService::setKpiEmployeeH2SubResults($month['start'],  $value['indicator_id'],999);

                KpiEmployeeService::setKpiEmployeeH0ConResults($month['start'],  $value['indicator_id'],999);
                KpiEmployeeService::setKpiEmployeeH1ConResults($month['start'],  $value['indicator_id'],999);
                KpiEmployeeService::setKpiEmployeeH2ConResults($month['start'],  $value['indicator_id'],999);
            }
        }

        return Command::SUCCESS;
    }
}
