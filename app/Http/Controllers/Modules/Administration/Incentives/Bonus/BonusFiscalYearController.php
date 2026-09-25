<?php

namespace App\Http\Controllers\Modules\Administration\Incentives\Bonus;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Incentives\Bonus\FiscalYearRequest;
use App\Models\Modules\Administration\Incentives\Bonus\BonusFiscalYear;
use App\Services\Modules\Administration\BonusBlockService;
use App\Services\Modules\Administration\BonusFiscalYearService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class BonusFiscalYearController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // try {
        //     $fiscalYears = BonusFiscalYear::get();
        //     $activefiscalYear = BonusFiscalYear::where('active', 1)->first();
        //     $activeMonth = BonusBlockService::getActiveMonth($activefiscalYear->public_id );
        //     $months = $request->active_month ?? BonusBlockService::getMonths($activefiscalYear->public_id);

        //     return ApiResponser::success(null, null, [
        //         'fiscalYears' => $fiscalYears,
        //         'activefiscalYear' => $activefiscalYear->public_id ?? null,
        //         'activeMonth' => $activeMonth ,
        //         'months' => $months ,
        //     ]);
        // } catch (\Throwable $e) {
        //     return ApiResponser::error('Erro ao carregar lista ', [['Erro ao carregar lista ]]);
        // }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FiscalYearRequest $request)
    {
        try {
            BonusFiscalYearService::store($request);

            $activefiscalYear = BonusFiscalYear::where('active', 1)->first();

            $activefiscalYear = $activefiscalYear ? $activefiscalYear->public_id : null;

            $activeMonth = $activefiscalYear ?  BonusBlockService::getActiveMonth($activefiscalYear) : null;

            return ApiResponser::success('Ano fiscal criado com sucesso', null, [
                'activefiscalYear' => $activefiscalYear,
                'activeMonth' => $activeMonth,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar Ano fiscal ', [['Erro ao criar Ano fiscal ']]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BonusFiscalYear $fiscalYear)
    {
        try {
            BonusFiscalYearService::update($fiscalYear, $request);
            $activefiscalYear = BonusFiscalYear::where('active', 1)->first();
            return ApiResponser::success('Ano fiscal atualizado com sucesso', null, [
                'activefiscalYear' => $activefiscalYear->public_id ?? null,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao atualizar Ano fiscal ', [['Erro ao atualizar Ano fiscal ']]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
