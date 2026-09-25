<?php

namespace App\Http\Controllers\Modules\Administration\Incentives\Bonus;

use App\Models\Modules\Administration\Incentives\Bonus\BonusPanel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Incentives\Bonus\BonusPanelRequest;
use App\Http\Requests\Modules\Administration\Incentives\Bonus\BonusPanelValidateRequest;
use App\Models\Modules\Administration\Incentives\Bonus\BonusFiscalYear;
use App\Models\Modules\Administration\Incentives\Bonus\BonusPanelBlock;
use App\Models\Modules\Administration\Incentives\Bonus\BonusPanelHistorical;
use App\Services\Core\User\MetadataService;
use App\Services\Modules\Administration\BonusBlockService;
use App\Services\Modules\Administration\BonusPanelService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Log;

class BonusPanelController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activefiscalYear = BonusFiscalYear::where('active', 1)->first();
        $fiscalYear = $request->fiscal_year_id ?? $activefiscalYear->public_id ?? null;
        $panels = BonusPanel::with(['owner'])->where('fiscal_year_id', $fiscalYear)->get();
        return ApiResponser::success(null, null, [
            'panels' => $panels,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BonusPanelRequest $request)
    {
        try {
            $panel =  BonusPanel::create($request->all());
            $blocks = $request->blocks;
            foreach ($blocks as $key => $block) {
                $block['panel_id'] = $panel->public_id;
                $block['fiscal_year_id'] = $panel->fiscal_year_id;
                $block['order'] = $key;
                BonusPanelBlock::create($block);
            }
            return ApiResponser::success('Painel criado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar Painel', [['Erro ao criar Painel',]]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BonusPanel $bonusPanel, Request $request)
    {
        try {
            $month = $request->activeMonth;

            $bonusPanel = BonusPanelService::show($bonusPanel->public_id,  $month);

            return ApiResponser::success(null, null, [
                'panel' => $bonusPanel,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao exibir Painel', [['Erro ao exibir Painel']]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BonusPanelRequest $request, bonusPanel $bonusPanel)
    {
        try {
            BonusPanelService::update($request,  $bonusPanel);



            return ApiResponser::success('Bloco atualizado com sucesso', null, null);
        } catch (\Throwable $e) {

            return ApiResponser::error('Erro ao atualizar painel ', [['Erro ao atualizar painel ']]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function validate(BonusPanelValidateRequest $request)
    {
        try {
            $bonusPanel = BonusPanel::where('public_id', $request->public_id)->first();
            $bonusPanel->status = $request->status;
            $bonusPanel->save();

            $historical = [
                'panel_id' => $bonusPanel->public_id,
                'status' => $request->status,
                'meta' =>  MetadataService::getMetadata($request),
            ];

            BonusPanelHistorical::create($historical);

            return ApiResponser::success('Bloco atualizado com sucesso', null, null);
        } catch (\Throwable $e) {

            return ApiResponser::error('Erro ao atualizar status ', [['Erro ao atualizar status']]);
        }
    }
}
