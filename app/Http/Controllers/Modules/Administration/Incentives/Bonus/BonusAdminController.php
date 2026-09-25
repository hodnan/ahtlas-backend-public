<?php

namespace App\Http\Controllers\Modules\Administration\Incentives\Bonus;

use App\Http\Controllers\Controller;
use App\Models\Modules\Administration\Incentives\Bonus\BonusBlock;
use App\Models\Modules\Administration\Incentives\Bonus\BonusFiscalYear;
use App\Models\Modules\Administration\Incentives\Bonus\BonusPanel;
use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Models\Modules\Employee\Employee;
use App\Services\Modules\Administration\BonusBlockService;
use App\Services\Modules\Administration\BonusInterface;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Colors\Rgb\Channels\Red;

class BonusAdminController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        try {

            // pega o ano fiscal ativo
            $activefiscalYear = BonusFiscalYear::where('active', 1)->first(); 
            $activefiscalYear = $activefiscalYear ? $activefiscalYear->public_id : null;
            // se não existir ano fiscal ativo passado pela url paga o ativo no banco
            $activefiscalYear = $request->fiscalYear ?? $activefiscalYear ?? null;

            // lista os meses conforme ano fiscal
            $months = BonusBlockService::getMonths($activefiscalYear) ;

            // verifica se o mês passado na url existe na lista de meses do ano fiscal 
            $month_exists = array_search($request->activeMonth, array_column($months, 'value')) !== false;
           
            // se não existir mês na request e existir na lista de meses do anos fical retorna o mês da url, se não pega o mês padrão do ano fiscal
            $activeMonth =  $activefiscalYear && $request->activeMonth && $month_exists ? $request->activeMonth : BonusBlockService::getActiveMonth($activefiscalYear);
            
            $statuses = $request->isMethod('get') ?  BonusInterface::YEAR_STATUSES : [];
            $indicators = $request->isMethod('get') ? Indicator::where('active', 1)->where('bonus',1)->get() : [];
            $fiscalYears = BonusFiscalYear::get();
            $bonusBlock = BonusBlock::with(['owner', 'fiscalYear'])->where('fiscal_year_id', $activefiscalYear)->get();

            $hierarchicalLevel = $request->isMethod('get') ? BonusInterface::HIERARCHICAL_LEVEL : [];
            $blockRanges = $request->isMethod('get') ? BonusInterface::BLOCK_RANGES : [];
            $accumulationTypes = $request->isMethod('get') ? BonusInterface::ACCUMULATION_TYPES : [];
            $panelStatuses = $request->isMethod('get') ? BonusInterface::PANEL_STATUSES : [];
            $panels = BonusPanel::with(['owner'])->where('fiscal_year_id', $activefiscalYear)->get();


            $areas = Employee::select(DB::raw("replace(replace(position, 'Diretor ', 'Diretoria '), 'Diretora ', 'Diretoria ') as name"))
                ->where('position', 'like', 'Diretor%')
                ->where('active', true)
                ->groupBy(DB::raw("replace(replace(position, 'Diretor ', 'Diretoria '), 'Diretora ', 'Diretoria ')")) // Usando a mesma expressão do SELECT
                ->orderBy(DB::raw("replace(replace(position, 'Diretor ', 'Diretoria '), 'Diretora ', 'Diretoria ')")) // Usando a mesma expressão do SELECT
                ->get();


            $owners = Employee::with(['avatar'])
                ->where('active', 1)
                ->where('hierarchical_level', '>=', 2)
                ->orderBy('hierarchical_level')
                ->orderBy('name')
                
                ->get([
                    'username',
                    'name',
                    'nickname',
                    'position_summary',
                    'hierarchical_level',
                ]);

            return ApiResponser::success(null, null, [
                'activeMonth' => $activeMonth,
                'activefiscalYear' => $activefiscalYear,
                'year_statuses' => $statuses,
                'fiscalYears' => $fiscalYears,
                'indicators' => $indicators,
                'hierarchicalLevel' => $hierarchicalLevel,
                'bonusBlock' => $bonusBlock,
                'owners' => $owners,
                'blockRanges' => $blockRanges,
                'areas' => $areas,
                'months' => $months,
                'accumulationTypes' => $accumulationTypes,
                'panels' => $panels,
                'panelStatuses' => $panelStatuses,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista', [['Erro ao carregar lista']]);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
