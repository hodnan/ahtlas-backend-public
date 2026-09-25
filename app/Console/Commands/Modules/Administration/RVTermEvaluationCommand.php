<?php

namespace App\Console\Commands\Modules\Administration;

use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Services\Modules\Administration\TermEvaluationService;
use App\Services\Modules\Administration\TermInterface;
use Illuminate\Console\Command;

class RVTermEvaluationCommand  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rv:evaluation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera a apuração dos termos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $terms = Term::where('evaluation', 0)
            ->where('status', TermInterface::STATUS_APPROVED)
            ->get([
                'id',
                'owner',
                'month_ref',
                'sector_n1_id',
                'sector_n2_id',
                'payment_id',
                'position',
                'level',
                'roof',
                'apprentice',
                'basket',
                'accelerator',
                'deflator',
                'elimination',
                'status',
                'created_at',
                'updated_at',
            ]);

        foreach ($terms as $term) {
            TermEvaluationService::setEvaluationTargets($term);
            TermEvaluationService::setResults($term->id);
            TermEvaluationService::setBasketRange($term);
            TermEvaluationService::setEvaluationCalc($term->id);
            TermEvaluationService::setFinalResult($term->id, $term->roof);
        }

        return Command::SUCCESS;
    }
}
