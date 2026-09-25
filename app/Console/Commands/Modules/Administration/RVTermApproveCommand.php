<?php

namespace App\Console\Commands\Modules\Administration;

use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Services\Core\User\MetadataService;
use App\Services\Modules\Administration\TermEvaluationService;
use App\Services\Modules\Administration\TermInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RVTermApproveCommand  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rv:approve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aprova termos na data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = Carbon::now()->format('Y-m-d');

        $terms = Term::where('status', TermInterface::STATUS_PENDING)->where('due_date_at', '<', $date )->get();
    
        foreach ($terms as $term) {
          $term->status =  TermInterface::STATUS_APPROVED;
          $term->approved_meta = MetadataService::getMetadata();
          $term->approved_by = 'SYS00000';
          $term->approved_at = Carbon::now();
          $term->save();   
        }
        return Command::SUCCESS;
    }
}
