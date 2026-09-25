<?php

namespace App\Jobs\Modules\Management\ControlCenter;

use App\Mail\ControlCenterTrackingEmail;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingStage;
use App\Services\Modules\Management\ControlCenter\ControlCenterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ControlCenterTrackingEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $stageId;
    public $to;
    public $cc;
    public $bcc;

    public $tries = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(int $stageId, $mails)
    {
        $this->stageId = $stageId;
        $this->to = $mails['to'];
        $this->cc = $mails['cc'];
        $this->bcc = $mails['bcc'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $stage = ControlCenterService::getStageById($this->stageId);
    
            $emailView = new ControlCenterTrackingEmail($stage);

            Mail::to($this->to)
                ->cc($this->cc)
                ->bcc($this->bcc)
                ->send($emailView);
        } catch (\Throwable $th) {
            Log::error("CCP send mail $this->stageId", [$th->getMessage(),  $stage]);
        }
       
    }
}
