<?php

namespace App\Http\Controllers\Modules\Administration\Incentives\RV;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Incentives\RV\TermRequest;
use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Models\Modules\Employee\SectorN2;
use App\Services\Modules\Administration\TermInterface;
use App\Services\Modules\Administration\TermService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Ramsey\Uuid\Type\Integer;

class TermController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $terms =  TermService::termsList($request->all());
            $statuses =  $request->isMethod('get') ? TermInterface::STATUSES : null;
            $levels =  $request->isMethod('get') ? TermInterface::LEVELS : null;
            $positions =  $request->isMethod('get') ? TermInterface::POSITIONS : null;
            $owners =  $request->isMethod('get') ? TermService::getOwners() : null;
            $payments =  $request->isMethod('get') ? TermService::getPayments() : null;
            $sectors =  $request->isMethod('get') ? TermService::getSectors() : null;
            // $sectorsN2 =  $request->isMethod('get') ? SectorN2::get(['id', 'name']):  null;
            $kpis =  $request->isMethod('get') ? TermService::getKpis() : null;

            return ApiResponser::success(null, null, [
                'terms' => $terms,
                'statuses' => $statuses,
                'levels' => $levels,
                'positions' => $positions,
                'owners' => $owners,
                'payments' => $payments,
                'sectors' => $sectors,
                // 'sectorsN2' => $sectorsN2,
                'kpis' => $kpis,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TermRequest $request)
    {
        try {
            TermService::termStore($request);
            return ApiResponser::success('Termo criado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar pedido', [['Erro ao criar pedido']]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $term = TermService::getTermById((int)$id);
            return ApiResponser::success(null, null, [
                'term' => $term,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao abrir termo', [['Erro ao abrir termo']]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TermRequest $request, int $term)
    {
      
        try {
            $term = Term::find($term);
            $data = $request->all();
        
            $term->owner = $data['owner'] ?? null;
            $term->payment_id = $data['payment_id'] ?? null;
            $term->roof = $data['roof'] ?? null;
            $term->apprentice = $data['apprentice'] ?? null;
            $term->notes = $data['notes'] ?? null;
            $term->basket = isset($data['basket']) ? TermService::getIndicatorData($data['basket']) : [];
            $term->accelerator = isset($data['accelerator']) ? TermService::getIndicatorData($data['accelerator']) : [];
            $term->deflator = isset($data['deflator']) ? TermService::getIndicatorData($data['deflator']) : [];
            $term->elimination = isset($data['elimination']) ? TermService::getIndicatorData($data['elimination']) : [];
            $term->status = $data['status'];
            if((int) $data['status'] == TermInterface::STATUS_PENDING)
            {
                $term->due_date_at = Carbon::now()->addWeekdays(3);
            }
            $term->save();

            TermService::storeTermFile($term->id);        
            return ApiResponser::success('Termo atualizado com sucesso',null,  null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao atualizar termo', [['Erro ao atualizar termo']]);
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
