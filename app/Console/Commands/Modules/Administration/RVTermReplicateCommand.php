<?php

namespace App\Console\Commands\Modules\Administration;


use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Services\Modules\Administration\TermInterface;
use App\Services\Modules\Administration\TermService;
use Illuminate\Http\Request;
use Illuminate\Console\Command;

use Carbon\Carbon;


use Illuminate\Support\Facades\Log;

class RVTermReplicateCommand  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rv:replicate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copia os termos aprovados para o próximo mês';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // Copia o mês anterior 
        // $monthRef =  Carbon::today()->subMonth()->firstOfMonth();
        // $monthRefNew = $monthRef->copy()->addMonth()->firstOfMonth();
        // $monthRefNewString = $monthRefNew->copy()->format('Y-m');

        // Copia o mês atual para o posterior 
        $monthRef = Carbon::today()->firstOfMonth();
        $monthRefNew = $monthRef->copy()->addMonth()->firstOfMonth();
        $monthRefNewString = $monthRefNew->copy()->format('Y-m');

        $terms = Term::where('month_ref', $monthRef)->where('status', TermInterface::STATUS_APPROVED)->get();

        foreach ($terms as $term) {
            try {
                $termNew['month_ref'] = $monthRefNew;
                $termNew['owner'] = $term->owner;
                $termNew['sector_n1_id'] = $term->sector_n1_id;
                $termNew['sector_n2_id'] = $term->sector_n2_id;
                $termNew['campaign'] = $term->campaign;
                $termNew['payment_id'] = $term->payment_id;
                $termNew['position'] = $term->position['id'];
                $termNew['level'] = $term->level['id'];
                $termNew['roof'] = $term->roof;
                $termNew['apprentice'] = $term->apprentice;
                $termNew['mock'] = $term->mock;
                $termNew['delta'] = $term->delta;
                $termNew['term_name'] = $monthRefNewString
                    . "_" . str_pad($term->sector_n1_id, 6, '0', STR_PAD_LEFT)
                    . "_" . str_pad($term->sector_n2_id, 6, '0', STR_PAD_LEFT)
                    . "_V1"
                    . "_" . TermInterface::POSITIONS[$termNew['position']]['short']
                    . "_" . TermInterface::LEVELS[$termNew['level']]['short']
                    . "_" . $termNew['campaign'];
                $termNew['version'] = 1;
                $termNew['notes'] = $term->notes;
                $termNew['basket'] = $term->basket;
                $termNew['accelerator'] = $term->accelerator;
                $termNew['deflator'] = $term->deflator;
                $termNew['elimination'] = $term->elimination;
                $termNew['status'] = TermInterface::STATUS_STANDBY;
                $termNew['created_by'] = $term->updated_by;
                $termNew['updated_by'] = $term->updated_by;
                $termNew['created_meta'] = $term->created_meta;

                // Criando uma instância de Request com os dados de $termNew
                $request = Request::create('', 'POST', $termNew);

                // Chamando o serviço de armazenamento com a Request

                TermService::termStore($request);
            } catch (\Throwable $th) {
                Log::error('rv:replicate', ['duplicar termo', $th->getMessage()]);
            }
        }
        return Command::SUCCESS;
    }
}
