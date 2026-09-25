<?php

namespace App\Console\Commands\Modules\Administration;

use App\Models\Modules\Administration\Intelligence\KpiEmployee;
use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Models\Modules\Administration\Incentives\RV\TermSignature;
use App\Services\Modules\Administration\KpiEmployeeService;
use App\Services\Modules\Administration\TermInterface;
use App\Services\Modules\Administration\TermSignatureService;
use Illuminate\Console\Command;

use Carbon\Carbon;


use Illuminate\Support\Facades\Log;

class RVTermToSignatureCommand  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rv:term-to-signature';

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
        $monthRef = Carbon::today()->firstOfMonth()->toDateString();

        // Lista os termos do mês
        $terms = Term::where('month_ref', $monthRef)
            ->where('status', TermInterface::STATUS_APPROVED)
            ->pluck('id');

        foreach ($terms as $termId) {
            try {
                // gera lista de termos para serem assinados 
                TermSignatureService::setEmployeeToSign($termId);
            } catch (\Throwable $th) {
                Log::error('rv:term-to-signature', ['falha ao gerar lista', $th->getMessage()]);
            }
        }

        // lista os termos reprovados
        $reprovedTerms = Term::where('month_ref', $monthRef)
            ->where('status', TermInterface::STATUS_REPROVED)
            ->pluck('id');

        // remove da lista os termos reprovados que não foram assinados 
        foreach ($reprovedTerms as $termId) {
            try {
                TermSignature::where('term_id', $termId)
                ->whereNull('uuid')
                ->delete();
            } catch (\Throwable $th) {
                Log::error('rv:term-to-signature', ['falha ao apagar reprovados', $th->getMessage()]);
            }
        }

        return Command::SUCCESS;
    }
}
