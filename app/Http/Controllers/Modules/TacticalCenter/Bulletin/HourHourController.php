<?php

namespace App\Http\Controllers\Modules\TacticalCenter\Bulletin;

use App\Models\Modules\TacticalCenter\Bulletin\HourHourDate;
use App\Http\Controllers\Controller;
use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Employee\SectorN1;
use App\Services\Modules\TacticalCenter\Bulletin\BulletinHourHourService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;

class HourHourController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $managers = Employee::with(['avatar'])
                ->where('active', 1)
                ->where('hierarchical_level', '>=', 2)
                ->get(['username', 'name', 'position_summary', 'hierarchical_level']);

            $hourHourDate = BulletinHourHourService::getBulletins($request->all());

            $sectorsN1 = BulletinHourHourService::getSectorsN1($request->all());

            return ApiResponser::success(null, null, [
                'managers' => $managers,
                'sectorsN1' => $sectorsN1,
                'hourHourDate' => $hourHourDate,
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
    public function show(string $dt_data, int $nu_setor)
    {
        try {
            $intraday = BulletinHourHourService::getBulletin($dt_data,  $nu_setor);

            return ApiResponser::success(null, null, [
                'intraday' => $intraday,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar intraday ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HourHourDate $HourHourDate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HourHourDate $HourHourDate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HourHourDate $HourHourDate)
    {
        //
    }
}
