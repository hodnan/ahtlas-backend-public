<?php

namespace App\Http\Controllers\Modules\Administration\Incentives\Bonus;

use App\Models\Modules\Administration\Incentives\Bonus\BonusBlockItem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Administration\Incentives\Bonus\BonusBlockItemRequest;
use App\Services\Modules\Administration\BonusBlockService;
use App\Services\Modules\Administration\BonusPanelService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Log;

class BonusBlockItemController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BonusBlockItemRequest $request)
    {
        try {
            $blockItem = BonusBlockItem::create($request->all());

            BonusBlockService::storeItemTarget($blockItem);

            // muda o status dos paineis relacionados para standby 
            BonusPanelService::setPanelStandBy( $blockItem->block_id);

            return ApiResponser::success('Bloco criado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar item do Bloco ', [['Erro ao criar item do Bloco ']]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BonusBlockItem $bonusBlockItem, Request $request)
    {

        try {
            $month = $request->activeMonth;
            $bonusBlockItem->load(['indicator', 'owner', 'leader', 'targets', 'grade' => function ($query) use ($month) {
                $query->where('month_ref', $month);
            }]); 

        
            return ApiResponser::success(null, null, [
                'bonusBlockItem' => $bonusBlockItem,
            ]);


        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao exibir item do Bloco ', [['Erro ao exibir item do Bloco ']]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BonusBlockItemRequest $request, BonusBlockItem $bonusBlockItem)
    {
        try {
            $bonusBlockItem->owner_id = $request->owner_id;
            $bonusBlockItem->leader_id = $request->leader_id;
            $bonusBlockItem->area = $request->area;
            $bonusBlockItem->proof = $request->proof;
            $bonusBlockItem->accumulation_type = $request->accumulation_type;
            $bonusBlockItem->range = $request->range;
            $bonusBlockItem->weight = $request->weight;
            
            $bonusBlockItem->save();

            BonusBlockService::updateItemTarget($bonusBlockItem, $request->targets);
            // BonusPanelService::setPanelStandBy( $bonusBlockItem->block_id);

            return ApiResponser::success('Meta atualizada com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao atualizar item do Bloco ', [['Erro ao atualizar item do Bloco ']]);
        }
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BonusBlockItem $bonusBlockItem)
    {
        //
    }
}
