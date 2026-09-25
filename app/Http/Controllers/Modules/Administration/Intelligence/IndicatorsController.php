<?php

namespace App\Http\Controllers\Modules\Administration\Intelligence;

use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Intelligence\IndicatorRequest;
use App\Services\Modules\Administration\IndicatorInterface;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class IndicatorsController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $statuses = $request->isMethod('get') ? IndicatorInterface::STATUSES : [];
            $directions = $request->isMethod('get') ? IndicatorInterface::DIRECIONS : [];
            $symbols = $request->isMethod('get') ? IndicatorInterface::SYMBOLS : [];
            $calcs = $request->isMethod('get') ? IndicatorInterface::CALCS : [];
            $indicators = Indicator::orderBy('id', 'desc')->get();

            return ApiResponser::success(null, null, ['statuses' => $statuses, 'directions' => $directions, 'symbols' => $symbols, 'calcs' => $calcs, 'indicators' => $indicators]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ',[['Erro ao carregar lista']]);
        }
    }

    public function store(IndicatorRequest $request)
    {
        try {
            Indicator::create($request->all());
            return ApiResponser::success('Indicador criado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar pedido', [['Erro ao criar indicador']]);
        }
    }

    public function update(Request $request)
    {
        try {
            $indicator = Indicator::find($request->id);
            $indicator->name = $request->name;
            $indicator->active = $request->active;
            $indicator->direction = $request->direction;
            $indicator->symbol = $request->symbol;
            $indicator->calc = $request->calc;
            $indicator->is_percent = $request->is_percent;
            $indicator->kpi = $request->kpi;
            $indicator->rv = $request->rv;
            $indicator->bonus = $request->bonus;
            $indicator->notes = $request->notes;
            $indicator->save();
            return ApiResponser::success('Indicador atualizado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar pedido', [['Erro ao atualizar indicador']]);
        }
    }
}
