<?php

namespace App\Console\Commands\Modules\Administration;

use App\Models\Modules\Administration\Intelligence\KpiResult;
use Illuminate\Console\Command;

use App\Models\Modules\Employee\Employee;
use App\Services\Modules\Administration\KpiSourceService;
use Carbon\Carbon;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class KpiResultCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpi:result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Salva os resultados dos kpis e atualiza na tabela de kpis relacionados ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        KpiSourceService::setResult();
        KpiSourceService::setUpdates();

        return Command::SUCCESS;
    }
}
