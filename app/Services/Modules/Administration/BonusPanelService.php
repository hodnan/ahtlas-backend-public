<?php

namespace App\Services\Modules\Administration;

use App\Http\Requests\Modules\Administration\Incentives\Bonus\BonusPanelRequest;
use App\Models\Modules\Administration\Incentives\Bonus\BonusBlockItem;
use App\Models\Modules\Administration\Incentives\Bonus\BonusBlockItemTarget;
use App\Models\Modules\Administration\Incentives\Bonus\BonusFiscalYear;
use App\Models\Modules\Administration\Incentives\Bonus\BonusPanel;
use App\Models\Modules\Administration\Incentives\Bonus\BonusPanelBlock;
use App\Models\Modules\Administration\Incentives\Bonus\BonusPanelHistorical;
use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Models\Modules\Employee\Employee;
use App\Services\Core\User\MetadataService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Type\Integer;

class BonusPanelService
{
    public static function update(BonusPanelRequest $request, bonusPanel $bonusPanel)
    {
        try {
            $bonusPanel->area = $request->area;
            $bonusPanel->status = $request->status;

            $blocks = $request->blocks;
            $bonusPanel->save();

            $historical = [
                'panel_id' => $bonusPanel->public_id,
                'status' => $request->status,
                'meta' =>  MetadataService::getMetadata($request),
            ];

            try {
                BonusPanelHistorical::create($historical);
                //code...
            } catch (\Throwable $th) {
                Log::info('', [$th->getMessage()]);
            }

            // atualiza os blocos 
            foreach ($blocks as $key => $block) {

                if ($block['delete']) {

                    BonusPanelBlock::where('block_id', $block['block_id'])
                        ->where('panel_id', $bonusPanel->public_id)
                        ->first()
                        ->delete();
                }
                if (!$block['delete']) {

                    $data = [
                        'fiscal_year_id' =>  $bonusPanel->fiscal_year_id,
                        'panel_id' => $bonusPanel->public_id,
                        'block_id' => $block['block_id'],
                        'order' =>  $key,
                        'weight' =>  $block['weight'],
                    ];

                    $key = [
                        'panel_id' => $bonusPanel->public_id,
                        'block_id' => $block['block_id'],
                    ];

                    BonusPanelBlock::updateOrCreate($key, $data);
                }
            }

            // lista os usuários para copiar o painel 
            $copyTo = $request->copy;

            // Cria as cópias do painel 
            foreach ($copyTo as $ownerId) {
                BonusPanelService::panelCopy($bonusPanel->public_id,  $ownerId);
            }
        } catch (\Throwable $th) {
            new \Exception($th->getMessage());
        }
    }

    public static function show($panelId, $monthRef)
    {
        try {

            $bonusPanel = BonusPanel::with([
                'owner',
                'fiscalYear',
                'createdBy',
                'updatedBy',
                'historical',

            ])
                ->where('public_id', $panelId)
                ->first();


            // Pega a nota do painel 
            $bonusPanel->grade = BonusPanelService::getPanelGrade($panelId,  $monthRef);

            // Após carregar, percorre os blocks para somar a nota
            $bonusPanel->blocks =  BonusPanelService::getPanelBlocks($panelId,  $monthRef);

            $bonusPanel->team = BonusPanelService::getManagerTeam($bonusPanel->owner_id);

            return  $bonusPanel;
        } catch (\Throwable $e) {
        }
    }

    public static function panelCopy($panelId, $ownerId)
    {
        try {
            $panel = BonusPanel::where('public_id', $panelId)->first();
            $panel->toArray();

            $newPanel['fiscal_year_id'] = $panel['fiscal_year_id'];
            $newPanel['owner_id'] = $ownerId;
            $newPanel['hierarchical_level'] = $panel['hierarchical_level']['id'];
            $newPanel['area'] = $panel['area'];
            $newPanel['status'] = 0;

            $panelCheck =  BonusPanel::where('owner_id', $ownerId)
                ->where('fiscal_year_id', $panel['fiscal_year_id'])
                ->where('hierarchical_level', $panel['hierarchical_level']['id'])
                ->count();

            if ($panelCheck) {
                return 'já existe';
            }

            $newPanel =  BonusPanel::create($newPanel);

            $panelBlocks = BonusPanelBlock::where('panel_id', $panelId)->get();

            foreach ($panelBlocks as $block) {
                $newPanelBlock = [
                    "panel_id" => $newPanel->public_id,
                    "block_id" => $block->block_id,
                    "fiscal_year_id" => $block->fiscal_year_id,
                    "weight" => $block->weight,
                    "order" => $block->order,
                ];

                BonusPanelBlock::create($newPanelBlock);
            }
        } catch (\Throwable $th) {
            new \Exception($th->getMessage());
        }

        return $newPanel;
    }

    // pega a nota do painel 
    public static function getPanelGrade($panelId, $monthRef)
    {
        $blocks = BonusPanelBlock::where('panel_id', $panelId)
            ->get(['block_id', 'weight']);
        $gradeWeight = null;

        foreach ($blocks as $block) {
            // percore o reultado dos indicadores pada pegar as notas 

            $grade = BonusBlockService::getBlockGrade($block['block_id'], $monthRef);

            // se encontrato, soma as notas e aplica o peso do blodo para o painel 
            if ($grade) {
                $gradeWeightTemp = round(($grade *  ($block['weight'] / 100)), 2);
                $gradeWeight = $gradeWeight +  $gradeWeightTemp;
                // dump([$gradeWeight, $gradeWeightTemp]);
            }
        }

        return round($gradeWeight, 2);
    }

    public static function getPanelBlocks($panelId, $monthRef)
    {
        $panelBlocks = BonusPanelBlock::where('panel_id', $panelId)
            ->get(['block_id', 'weight']);

        $blocks = [];

        foreach ($panelBlocks as $panelBlock) {

            $block =  BonusBlockService::show($panelBlock['block_id'], $monthRef);


            $block->grade = BonusBlockService::getBlockGrade($panelBlock['block_id'], $monthRef);

            $block->weight = $panelBlock['weight'];
            $block->grade_weight = $block->grade * ($block->weight / 100);

            $blocks[] =  $block;
        }

        return $blocks;
    }

    public static function setPanelStandBy($blockId)
    {
        $panels = BonusPanelBlock::where('block_id', $blockId)->pluck('panel_id')->unique();

        foreach ($panels as $panel) {
            BonusPanel::where('public_id', $panel)->update(['status' => 0]);
        }
    }

    public static function getManagerTeam($username)
    {
        $owners = Employee::with(['avatar'])
            ->where('active', 1)
            ->where('hierarchical_level', '>=', 2)
            ->where(function ($query) use ($username) {
                $query->whereRaw("'$username' in (manager_n2_id, manager_n3_id,manager_n4_id, manager_n5_id)");   
            })
            ->orderBy('hierarchical_level')
            ->orderBy('name')
            ->get([
                'username',
                'name',
                'nickname',
                'position_summary',
                'hierarchical_level',
                'active',
            ]);

        return $owners;
    }
}
