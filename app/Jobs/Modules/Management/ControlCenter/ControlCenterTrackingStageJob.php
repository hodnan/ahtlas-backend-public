<?php

namespace App\Jobs\Modules\Management\ControlCenter;

use App\Mail\ControlCenterTrackingEmail;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingDailyResultSector;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingModel;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingStage;
use App\Services\Modules\Management\ControlCenter\ControlCenterInterface;
use App\Services\Modules\Management\ControlCenter\ControlCenterService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ControlCenterTrackingStageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $stage;
    public $tracking;

    public $tries = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(int $stageId)
    {
        $this->stage = ControlCenterTrackingStage::find($stageId);
        $this->tracking = ControlCenterTrackingModel::where('month_ref', $this->stage->month_ref)
            ->where('sector_n1_id',  $this->stage->sector_n1_id)
            ->where('indicator_id', $this->stage->indicator_id)
            ->first();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->stage->validated) {
            return;
        }

        // 1º - Atualiza stage no acompanhamento
        try {
            $this->tracking->stage = $this->stage->stage['id'];
            $this->tracking->save();
        } catch (\Throwable $th) {
            Log::error("CCP atualiza o stage do acompanhamento (" . $this->stage->id . ")", [$th->getMessage()]);
        }

        // 2º - Gera lista de ofensores
        try {
            ControlCenterService::setTrackingEmployeeResult($this->tracking, $this->stage);
        } catch (\Throwable $th) {
            Log::error("CCP atualiza o stage do acompanhamento (" . $this->stage->id . ")", [$th->getMessage()]);
        }

        // 3º - Envia email do stage
        try {
            ControlCenterService::sendMail($this->tracking, $this->stage);
        } catch (\Throwable $th) {
            Log::error("CCP envia email (" . $this->stage->id . ")", [$th->getMessage()]);
        }

        // 4º - Muda o status do stage para validado
        try {
            $this->stage->status = ControlCenterInterface::STAGE_STATUS_VALIDATED;
            $this->stage->save();
        } catch (\Throwable $th) {
            Log::error("CCP envia email (" . $this->stage->id . ")", [$th->getMessage()]);
        }

        // 5º - Atualiza o gráfico
        try {
            $result = ControlCenterTrackingDailyResultSector::where('date_ref', $this->stage->date_ref)
                ->where('sector_n1_id',  $this->stage->sector_n1_id)
                ->where('indicator_id',  $this->stage->indicator_id)
                ->first();

            if ($result) {
                $result->stage = $this->stage->stage['id'];
                $result->save();
            }
        } catch (\Throwable $th) {
            Log::error("CCP envia email (" . $this->stage->id . ")", [$th->getMessage()]);
        }

        try {
            $monthRef = Carbon::parse($this->tracking->month_ref);
            $monthRef = $monthRef->format('Y-m-d');
    
            // Limpa o cache
            Cache::forget('CDC_TRACKINGS_' . $monthRef);
            Cache::forget('CDC_STAGES_' . $monthRef);
        } catch (\Throwable $th) {
            Log::error("CCP limpar cache (" . $this->stage->id . ")", [$this->tracking]);
        }
        

        
    }
}
