<?php

namespace App\Http\Controllers\Modules\Administration\Incentives\Bonus;

use App\Models\Modules\Administration\Incentives\Bonus\BonusBlock;
use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Incentives\Bonus\BonusBlockRequest;
use App\Models\Modules\Administration\Incentives\Bonus\BonusFiscalYear;
use App\Services\Modules\Administration\BonusBlockService;
use App\Services\Modules\Administration\BonusPanelService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Log;

class BonusBlockController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activefiscalYear = BonusFiscalYear::where('active', 1)->first();

        $fiscalYear = $request->fiscal_year_id ?? $activefiscalYear->public_id ?? null;

        // Log::info('Fiscal Year: ' . $fiscalYear);

        $bonusBlock = BonusBlock::where('fiscal_year_id', $fiscalYear)->get();

        return ApiResponser::success(null, null, [
            'bonusBlock' => $bonusBlock,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BonusBlockRequest $request)
    {
        try {
            BonusBlock::create($request->all());

            return ApiResponser::success('Bloco criado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar Bloco ', [['Erro ao criar Bloco']]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BonusBlock $bonusBlock, Request $request)
    {
        try {
            $month = $request->activeMonth;

            $bonusBlock = BonusBlockService::show($bonusBlock->public_id,  $month);

            return ApiResponser::success(null, null, [
                'block' => $bonusBlock,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao exibir Bloco ', [['Erro ao exibir Bloco ']]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BonusBlockRequest $request, BonusBlock $bonusBlock)
    {
        try {
            $bonusBlock->name = $request->name;
            $bonusBlock->default = $request->default;
            $bonusBlock->owner_id = $request->owner_id;
            $bonusBlock->order = $request->order;
            $bonusBlock->weight = $request->weight;
            $bonusBlock->save();

            BonusPanelService::setPanelStandBy( $bonusBlock->public_id);

            return ApiResponser::success('Bloco atualizado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao atualizar Bloco ', [['Erro ao atualizar Bloco ']]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BonusBlock $bonusBlock)
    {
        //
    }
}
