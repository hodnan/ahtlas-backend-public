<?php

namespace App\Console\Commands\Modules\Administration;

use App\Models\Modules\Administration\Intelligence\KpiEmployee;
use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Models\Modules\Administration\Incentives\RV\TermPanelSignature;
use App\Models\Modules\Administration\Incentives\RV\TermSignature;
use App\Services\Modules\Administration\KpiEmployeeService;
use App\Services\Modules\Administration\TermInterface;
use App\Services\Modules\Administration\TermSignatureService;
use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RVTermParticipaCommand  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rv:participa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Alimenta tabela com dados';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dataInicio = Carbon::parse('2019-07-01')->startOfMonth();

        $dataFim = Carbon::parse('2024-12-01')->startOfMonth();

        while ($dataInicio->lte($dataFim)) {

            $termos = DB::connection('mis_secondary')
                ->table('DB_PORTAL.dbo.VW_RV_TERMS_PANEL_SIGNATURES_PARTICIPA')
                ->where("month_ref", $dataInicio)

                ->get([
                    'month_ref',
                    'term_file',
                    'username',
                    'uuid',
                    'hostname',
                    'ip',
                    'available_at',
                    'accepted_at',
                    'accept',
                ]);

            foreach ($termos as $termo) {

                try {
                    $data = [
                        'month_ref' => $termo->month_ref,
                        'term_file' => $termo->term_file,
                        'username' => $termo->username,
                        'uuid' => $termo->uuid,
                        'hostname' => $termo->hostname,
                        'ip' => $termo->ip,
                        'available_at' => $termo->available_at,
                        'accepted_at' => $termo->accepted_at,
                        'accept' => $termo->accept,
                    ];

                    TermPanelSignature::create($data);
                } catch (\Throwable $th) {

                    Log::error('falha ao criar termo', [$th->getMessage()]);
                }
            }

            $dataInicio->addMonth();
        }

        return Command::SUCCESS;
    }
}
