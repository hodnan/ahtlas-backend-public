<?php

namespace App\Jobs\Modules\Administration\Incentives\RV;

use App\Services\Modules\Administration\TermSignatureService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TermEmployeeToSignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $termId;
    public $tries = 7;
    /**
     * Create a new job instance.
     */
    public function __construct($termId)
    {
        $this->termId = $termId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
       
        TermSignatureService::setEmployeeToSign( $this->termId );
    }
}
