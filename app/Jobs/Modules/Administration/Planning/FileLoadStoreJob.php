<?php

namespace App\Jobs\Modules\Administration\Planning;

use App\Services\Modules\Administration\PlanningFileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FileLoadStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;
    public $tries = 7;
    public $timeout = 3600 * 2; // 2 horas
    /**
     * Create a new job instance.
     */
    public function __construct($id)
    {  
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

      $charge = new PlanningFileService($this->id);
     
      $charge->fileValidate();
      $charge->walkCSV();
      
    }
}
