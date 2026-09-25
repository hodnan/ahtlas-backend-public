<?php

namespace App\Http\Controllers\Modules\TacticalCenter\Bulletin;

use App\Models\Modules\TacticalCenter\Bulletin\Backoffice;
use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\TacticalCenter\Bulletin\BackofficeRequest;
use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Employee\SectorN1;
use App\Services\Modules\TacticalCenter\Bulletin\BulletinBackofficeService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;

class BackofficeFilterController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(BackofficeRequest $request)
    {
        try {
            $dataStart = $request->date_start;
            $dataEnd = $request->date_end;         

            $sectorsN1 = BulletinBackofficeService::getSectorsN1($dataStart, $dataEnd);
            $managers = BulletinBackofficeService::getManagers($dataStart, $dataEnd);
            $environments = BulletinBackofficeService::getEnvironment($dataStart, $dataEnd);
            $queues = BulletinBackofficeService::getQueue($dataStart, $dataEnd);
            $mailings = BulletinBackofficeService::getMailing($dataStart, $dataEnd);
           

            return ApiResponser::success(null, null, [
                'managers' => $managers,
                'sectorsN1' => $sectorsN1,
                'environments' => $environments,
                'queues' => $queues,
                'mailings' => $mailings,
               
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }

   
}
