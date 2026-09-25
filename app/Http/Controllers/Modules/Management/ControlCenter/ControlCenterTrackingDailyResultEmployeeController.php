<?php

namespace App\Http\Controllers\Modules\Management\ControlCenter;

use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingDailyResultEmployee;
use App\Http\Controllers\Controller;
use App\Models\Modules\Management\ControlCenter\ControlCenterTrackingStage;
use App\Services\Modules\Management\ControlCenter\ControlCenterService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;

class ControlCenterTrackingDailyResultEmployeeController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $monthRef = Carbon::parse($request->month_ref);

            $stages = ControlCenterService::getStagesForFilter($monthRef);

            $results = ControlCenterService::getEmployeeResults($request->stage);

            return ApiResponser::success(null, null, [
                'results' => $results,
                'stages' => $stages
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
    public function show(ControlCenterTrackingDailyResultEmployee $controlCenterTrackingDailyResultEmployee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ControlCenterTrackingDailyResultEmployee $controlCenterTrackingDailyResultEmployee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function download(Request $request)
    {
        $monthRef = Carbon::now()->startOfMonth()->format('Y-m');

        $stageId = str_pad($request->stage, 5, '0', STR_PAD_LEFT);

        $stage = ControlCenterTrackingStage::with(['sectorN1', 'sectorN1Group', 'indicator'])
            ->find($request->stage)
            ->toArray();

        $sector = $stage['sector_n1_group'] ??  $stage['sector_n1'];
        $indicator = $stage['indicator']['name'];

        $fileName =  "stage_" . $stageId . "_" . $sector['name'] . "_" . $indicator;
        $fileName = preg_replace('/\s+/', '_', $fileName); // substitui espaços por "_"
        $fileName = preg_replace('/[^\w_]/', '', $fileName); // remove caracteres não permitidos, exceto "_"
        $fileName = preg_replace('/_+/', '_', $fileName);
        $fileName = $monthRef . "_" . strtoupper($fileName) . ".csv";
        $fileName = preg_replace('/_\./', '.', $fileName);

        $results = ControlCenterService::getEmployeeResults($request->stage)->toArray();

        $results =  array_map(function ($item) use ($sector) {

            $temp['month_ref'] = $item['month_ref'];
            $temp['date_ref'] = $item['date_ref'];
            $temp['sector_n1_id'] = $sector['id'];
            $temp['sector_n1_name'] = $sector['name'];
            $temp['indicator_id'] = $item['indicator_id'];
            $temp['indicator_name'] = $item['indicator']['name'];
            $temp['employee_username'] = $item['employee']['username'];
            $temp['employee_name'] = strtoupper($item['employee']['name']);
            $temp['employee_sector_n1_id'] = $item['sector_n1_id'];
            $temp['employee_sector_n1_name'] = $item['sector_n1']['name'];
            $temp['employee_status'] = $item['employee']['active_label']['label'];
            $temp['indicator_name'] = $item['indicator']['name'];
            $temp['goal'] = $item['goal'];
            $temp['result'] = $item['result'];
            $temp['quadrant'] = $item['quadrant'];
            $temp['stage'] = $item['stage']['id'];

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
