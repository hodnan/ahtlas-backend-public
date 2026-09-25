<?php

namespace App\Console\Commands\Modules\Administration;

use App\Models\Modules\Administration\Incentives\RV\Term;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RVTermEvaluationStopCommand  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rv:evaluation-stop';

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
        $monthRef = Carbon::now()->startOfMonth();

        Term::where('evaluation',0)
        ->where('month_ref', '<', $monthRef )
        ->update(['evaluation' => 1]);
       
        return Command::SUCCESS;
    }
}
