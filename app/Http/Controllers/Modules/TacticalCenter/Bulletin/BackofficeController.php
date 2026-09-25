<?php

namespace App\Http\Controllers\Modules\TacticalCenter\Bulletin;

use App\Models\Modules\TacticalCenter\Bulletin\Backoffice;
use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\TacticalCenter\Bulletin\BackofficeRequest;
use App\Services\Modules\TacticalCenter\Bulletin\BulletinBackofficeService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BackofficeController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(BackofficeRequest $request)
    {
        try {
            $dateStart = $request->date_start;
            $dateEnd = $request->date_end;         
            
            $backoffice = BulletinBackofficeService::getBackoffices($request->all());
            $sectorsN1 = BulletinBackofficeService::getSectorsN1($dateStart, $dateEnd);
            $environments = BulletinBackofficeService::getEnvironment($dateStart, $dateEnd);
            $queues = BulletinBackofficeService::getQueue($dateStart, $dateEnd);
            $mailings = BulletinBackofficeService::getMailing($dateStart, $dateEnd);
            $statuses = BulletinBackofficeService::getStatuses();

            return ApiResponser::success(null, null, [
                'sectorsN1' => $sectorsN1,
                'environments' => $environments,
                'queues' => $queues,
                'mailings' => $mailings,
                'statuses' => $statuses,
                'backoffice' => $backoffice,
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
    public function show(Backoffice $backoffice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Backoffice $backoffice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Backoffice $backoffice)
    {
        //
    }
}
// 