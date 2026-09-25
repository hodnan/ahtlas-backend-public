<?php

namespace App\Http\Controllers\Modules\Management\ControlCenter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Management\ControlCenter\ControlCenterTrackingStageFilterRequest;
use App\Http\Requests\Modules\Management\ControlCenter\ControlCenterTrackingStageRequest;
use App\Jobs\Modules\Management\ControlCenter\ControlCenterTrackingStageJob;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingModel;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingStage;
use App\Services\Modules\Management\ControlCenter\ControlCenterInterface;
use App\Services\Modules\Management\ControlCenter\ControlCenterService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;

class ControlCenterTrackingStageController extends Controller implements ControlCenterInterface
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(ControlCenterTrackingStageFilterRequest $request)
    {
        try {
            $stages = ControlCenterService::getStages($request);
            $statuses = ControlCenterInterface::STAGE_STATUSES;

            return ApiResponser::success(null, null, [
                'stages' => $stages,
                'statuses' => $statuses,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(ControlCenterTrackingStageRequest $request, ControlCenterTrackingStage $stage)
    {
        try {
            // Se validado, salva com status "processando" 
            $stage->status = $request->status == self::STAGE_STATUS_VALIDATED ? self::STAGE_STATUS_PROCCESS : $request->status;
            $stage->validated = $request->status == self::STAGE_STATUS_VALIDATED;
            $stage->notes = $request->notes;
            $stage->save();

            if (env('APP_ENV') == 'production') {
                ControlCenterTrackingStageJob::dispatch($stage->id)
                    ->onQueue(ControlCenterInterface::QUEUE_STAGE);;
            }
            return ApiResponser::success('Acompanhamento criado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar Acompanhamento', [['Erro ao criar Acompanhamento']]);
        }
    }

    //    public static function download(ControlCenterTrackingStageFilterRequest $request)
    public static function download(Request $request)
    {
        $fileName =  "stages.csv";

        $results =  ControlCenterService::getStages($request)->toArray();

        $results =  array_map(function ($item) {

            $temp['id'] = $item['id'];
            $temp['month_ref'] = $item['month_ref'];
            $temp['date_ref_result'] = $item['date_ref'];
            $temp['date_ref_send'] = $item['created_at'];
            $temp['sector_n1_id'] = $item['sector_n1']['id'] ?? 0;
            $temp['sector_n1_name'] = $item['sector_n1']['name'] ?? '';
            $temp['indicator_id'] = $item['indicator_id'] ?? 0;
            $temp['indicator_name'] = $item['indicator']['name'] ?? '';
            $temp['goal'] = $item['goal'];
            $temp['factor_0'] = $item['factor_0'];
            $temp['factor_1'] = $item['factor_1'];
            $temp['result'] = $item['result'];
            $temp['stage'] = $item['stage']['id'];
            $temp['status'] = $item['status']['label'];

            return $temp;
        }, $results);

        $csvContent = makeCsv($results);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        return response()->make($csvContent, 200, $headers);
    }
}
