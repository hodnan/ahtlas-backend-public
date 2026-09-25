<?php

namespace App\Console\Commands\Modules\Administration;

use App\Models\Modules\Administration\Intelligence\KpiSector;
use App\Models\Modules\Administration\Intelligence\KpiSectorDaily;
use App\Services\Modules\Administration\KpiSectorService;
use Illuminate\Console\Command;

use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class KpiSectorDailyCommand  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpi:sector-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calcula e salva kpi por setorN1 ';

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
        $kpis = KpiSectorService::getKpis();

        foreach ($months as $month) {
            try {
                KpiSectorDaily::whereBetween('date_ref', [$month['start'], $month['end']])->delete();
            } catch (\Throwable $th) {
                Log::error('KpiEmployeeCommand', ['delete error', $th->getMessage()]);
            }

            foreach ($kpis as $value) {
                KpiSectorService::setKpiSectorDailyResults($month['start'],  $value['indicator_id']);
            }
        }

        return Command::SUCCESS;
    }
}
