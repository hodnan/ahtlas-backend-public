<?php

namespace App\Http\Controllers\Modules\TacticalCenter\Report;

use App\Models\Modules\TacticalCenter\Report\ReportRead;
use App\Http\Controllers\Controller;
use App\Models\Modules\TacticalCenter\Report\Report;
use App\Services\Core\User\MetadataService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;

class ReportReadController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $dateRef = Carbon::now();
            $monthRef = Carbon::now()->startOfMonth();
            $username =  str_replace("ext", "usr", strtolower($request->username));
            $meta = MetadataService::getMetadata($request, $username);

            $file = explode('.', $request->fileName);
            $file = $file[0];

            $errors = null;

            if (in_array($username, config('ahtlas.reports.debug_users'))) {
                $errors =  ['errors' => [
                    'Cubo - '. $request->reportId,
                    'GIP - Chamado aberto',
                    'FPW - Chamado aberto',
                ]];
            }

            $data = [
                'month_ref' => $monthRef,
                'date_ref' => $dateRef,
                'username' => $username,
                'report_id' => $request->reportId,
                'file_name' => $file,
                'meta' => $meta,
                'errors' => $errors,
            ];

            ReportRead::create($data);

            return ApiResponser::success(null, null, $errors);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar pedido', [['Erro ao carregar lista ']]);
        }



        return  $data;
    }

    /**
     * Display the specified resource.
     */
    public function show(ReportRead $reportRead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReportRead $reportRead)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReportRead $reportRead)
    {
        //
    }
}
