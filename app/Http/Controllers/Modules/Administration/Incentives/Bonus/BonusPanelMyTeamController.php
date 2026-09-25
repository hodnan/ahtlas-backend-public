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
use App\Services\Modules\Administration\BonusPanelService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Colors\Rgb\Channels\Red;

class BonusPanelMyTeamController extends Controller
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
            $activeUser = $request->activeUser ?? null;
            $manager = Auth::user()->username ;

            // lista os meses conforme ano fiscal
            $months = BonusBlockService::getMonths($activefiscalYear) ;

            // verifica se o mês passado na url existe na lista de meses do ano fiscal 
            $month_exists = array_search($request->activeMonth, array_column($months, 'value')) !== false;

            // se não existir mês na reques e existir na lista de meses do anos fical retorna o mês da url, se não pega o mês padrão do ano fiscal
            $activeMonth = $request->activeMonth &&  $month_exists ? $request->activeMonth : BonusBlockService::getActiveMonth($activefiscalYear);

            $statuses = $request->isMethod('get') ?  BonusInterface::YEAR_STATUSES : [];
            
            $fiscalYears = BonusFiscalYear::get();

            $team =  Employee::with(['avatar'])
            ->where('active', 1)
            ->where('hierarchical_level', '>=', 2)
            ->where(function ($query) use ($manager) {               
                $query->whereRaw("'$manager' in (manager_n2_id, manager_n3_id,manager_n4_id, manager_n5_id)");   
            })
            ->orderBy('hierarchical_level')
            ->orderBy('name')
            ->pluck('username');

            ;
                        
            $panels = BonusPanel::with(['owner'])
            ->where('fiscal_year_id', $activefiscalYear)
            ->where(function($query) use ($activeUser){
                if($activeUser){

                    $query->where('owner_id', $activeUser);
                }
            })
            ->whereIn('owner_id',$team)
            ->get();

            $owners = BonusPanelService::getManagerTeam($manager);

            return ApiResponser::success(null, null, [
                'activeMonth' => $activeMonth,
                'activefiscalYear' => $activefiscalYear,
                'activeUser' => $activeUser,
                'year_statuses' => $statuses,
                'fiscalYears' => $fiscalYears,             
                'owners' => $owners,            
                'months' => $months,                
                'panels' => $panels,                
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [['Erro ao carregar lista']]);
        }
    }

  
}
