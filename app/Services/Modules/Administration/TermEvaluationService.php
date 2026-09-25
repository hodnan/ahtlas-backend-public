<?php

namespace App\Services\Modules\Administration;

use App\Models\Modules\Administration\Intelligence\KpiEmployee;
use App\Models\Modules\Administration\Incentives\RV\Term;
use App\Models\Modules\Administration\Incentives\RV\TermEvaluation;
use App\Models\Modules\Administration\Incentives\RV\TermSignature;
use App\Models\Modules\Employee\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class TermEvaluationService
{

    public static function termsList($request = null)
    {
        $filterSectorsN1 = isset($request['sectorsN1']) ? explode(',', $request['sectorsN1']) : null;

        $filterDateFef = isset($request['month_ref']) ? Carbon::parse($request['month_ref'])->startOfMonth() : Carbon::now()->startOfMonth();

        return  TermSignature::with(['user','term' => function ($query) {
            $query->select(
                'rv_terms.id',
                'rv_terms.month_ref',
                'rv_terms.term_name',
                'rv_terms.sector_n1_id',
                'rv_terms.sector_n2_id',
                'rv_terms.approved_at',
                'rv_terms.status',
                
            ); // Selecione apenas os campos necessários, omitindo o campo `term`
        }])
        
            ->where('rv_terms_signatures.month_ref', $filterDateFef)
            ->whereHas('term', function ($query) use ($filterSectorsN1) {
                if (!empty($filterSectorsN1)) {
                    $query->whereIn('sector_n1_id', $filterSectorsN1);
                }
            })
            ->where(function ($query) {
                $query->whereHas('term', function ($query) {
                    $query->where('status', TermInterface::STATUS_APPROVED);
                });
                $query->orWhere(function ($query) {
                    $query->whereNotNull('uuid');
                });
            })
            ->join('rv_terms_evaluations', function ($join) {
                $join->on('rv_terms_evaluations.username', '=', 'rv_terms_signatures.username')
                ->on('rv_terms_evaluations.term_id', '=', 'rv_terms_signatures.term_id')
                  
                    ->where('rv_terms_evaluations.indicator_id', -1000);
            })
            ->select([
                'rv_terms_signatures.id',
                'rv_terms_signatures.uuid',
                'rv_terms_signatures.term_id',
                'rv_terms_signatures.month_ref',
                'rv_terms_signatures.username',
                'rv_terms_signatures.accept',
                'rv_terms_signatures.updated_at',
                'rv_terms_signatures.created_at',
                'rv_terms_evaluations.evaluation_details',
            ])
            // ->makeHidden(['term.term'])
            ->get();
        ;
    }


    public static  function setEvaluationTargets($term)
    {
        // Pega lista de pessoas que são elegiveis desse termo e alimenta TermEvaluation
        $employees = TermSignature::where('term_id', $term->id)->pluck('username');

        // Pega os indicadores do termo
        $directives = self::getDirectives($term);


        try {
            $directives = $directives->map(function ($item) {
                $value = isset($item['value']) ? $item['value'] : null;
                return [
                    'indicator_id' => $item['indicator_id'],
                    'directive_type' => $item['directive_type'],
                    'directive_operation' => $item['directive_type'] != TermInterface::DIRECTIVE_BASKET ? $item['operation'] : null,
                    'directive_target' => $item['directive_type'] != TermInterface::DIRECTIVE_BASKET ? $item['target'] : null,
                    'directive_target_type' => isset($item['directive_target_type']) ? $item['directive_target_type'] : null,
                    'directive_value' => $item['directive_type'] != TermInterface::DIRECTIVE_BASKET ? $value / 100 : null,
                ];
            })->unique();
        } catch (\Throwable $th) {
            Log::error('TermEvaluationService', ['setEvaluationTargets - directives', $th->getMessage()]);
        }

        foreach ($employees as $employee) {
            foreach ($directives as $directive) {
                $data['month_ref'] = $term->month_ref;
                $data['term_id'] = $term->id;
                $data['sector_n1_id'] = $term->sector_n1_id;
                $data['sector_n2_id'] = $term->sector_n2_id;
                $data['position'] = $term->position['id'];
                $data['username'] = $employee;
                $data['indicator_id'] = $directive['indicator_id'];
                $data['directive_type'] = $directive['directive_type'];
                $data['directive_operation'] = $directive['directive_operation'];
                $data['directive_target'] = $directive['directive_target'];
                $data['directive_target_type'] = $directive['directive_target_type'];
                $data['directive_value'] = $directive['directive_value'];

                $unique = [
                    'month_ref' => $data['month_ref'],
                    'term_id' => $data['term_id'],
                    'sector_n1_id' => $data['sector_n1_id'],
                    'sector_n2_id' => $data['sector_n2_id'],
                    'username' => $employee,
                    'indicator_id' => $directive['indicator_id'],
                    'directive_type' => $directive['directive_type'],
                ];

                try {
                    TermEvaluation::updateOrCreate($unique, $data);
                } catch (\Throwable $th) {
                    Log::error('TermEvaluationService', ['setEvaluationTargets - insert', $th->getMessage()]);
                }
            }
        }
    }

    public static function getDirectives($term)
    {

        $baskets = collect($term->basket)->map(function ($item) {
            // if (is_array($item)) {
            $item['directive_type'] = TermInterface::DIRECTIVE_BASKET;
            $item['directive_target_type'] = isset($item['calc']['id']) ? $item['calc']['id'] : null;

            // }
            return $item;
        });

        $accelerator = collect($term->accelerator)->map(function ($item) {
            // if (is_array($item)) {
            $item['directive_type'] = TermInterface::DIRECTIVE_ACCELERATOR;
            $item['directive_target_type'] = isset($item['calc']['id']) ? $item['calc']['id'] : null;
            // }
            return $item;
        });

        $deflator = collect($term->deflator)->map(function ($item) {
            // if (is_array($item)) {
            $item['directive_type'] = TermInterface::DIRECTIVE_DEFLATOR;
            $item['directive_target_type'] = isset($item['calc']['id']) ? $item['calc']['id'] : null;
            // }
            return $item;
        });

        $elimination = collect($term->elimination)->map(function ($item) {
            // if (is_array($item)) {
            $item['directive_type'] = TermInterface::DIRECTIVE_ELIMINATION;
            $item['directive_target_type'] = isset($item['calc']['id']) ? $item['calc']['id'] : null;
            // }
            return $item;
        });

        $rvResult =  [
            'indicator_id' => -1000, // Indicador Apuração de RV
            'directive_type' => TermInterface::DIRECTIVE_RESULT,
            'range' => null,
            'operation' => null,
            'target' => null,
            'value' => null,
        ];


        $rvMonitoring = [
            'indicator_id' => 747, // Indicador zerado na Monitoria
            'directive_type' => TermInterface::DIRECTIVE_ELIMINATION,
            'directive_target_type' => IndicatorInterface::CALC_SUM,
            'range' => null,
            'operation' => '>=',
            'target' => 1,
            'value' => 1,
        ];

        $directives = $baskets->merge($accelerator)
            ->merge($deflator)
            ->merge($elimination)
            ->collect()
            ->push($rvMonitoring)
            ->sortBy(['directive_type', 'range'])
            ->push($rvResult);


        return   $directives;
    }

    public static function setResults($term_id)
    {
        $evaluations = TermEvaluation::where('term_id', $term_id)
            ->where('indicator_id', '<>', '-1000')
            ->get();


        foreach ($evaluations as $evaluation) {


            $kpi = KpiEmployee::where('consolidated', 0)
                ->where('month_ref',  $evaluation->month_ref)
                ->where('username',  $evaluation->username)
                ->where('sector_n1_id',  $evaluation->sector_n1_id)
                ->where('sector_n2_id', $evaluation->sector_n2_id)
                ->where('indicator_id',  $evaluation->indicator_id)
                ->where('hierarchical_level',  $evaluation->position)
                ->first();

            if ($kpi) {
                $evaluation->result_target = $evaluation->directive_target_type == IndicatorInterface::CALC_AVG ? $kpi['avg'] : $kpi['result'];
                $evaluation->result_value = $kpi['result'];
                $evaluation->save();
            }
        }
    }

    public static function setBasketRange($term)
    {
        // Calcula qual o rank o colaborador está em relação a cesta de indicadores
        $baskets = collect($term->basket);

        $evaluations = TermEvaluation::where('term_id', $term->id)
            ->where('directive_type', TermInterface::DIRECTIVE_BASKET)
            ->get(['id', 'term_id', 'indicator_id', 'result_target', 'result_value']);


        foreach ($evaluations  as $evaluation) {
            if ($evaluation->result_target) {

                $directives = self::getBasketRange($evaluation->indicator_id, $evaluation->result_target, $baskets);
                $directives['evaluation_value'] = $directives['directive_value'] * $evaluation->result_value;

                try {
                    TermEvaluation::find($evaluation->id)->update($directives);
                } catch (\Throwable $th) {
                    Log::error('TermEvaluationService', ['setEvaluationTargets', $th->getMessage()]);
                }
            }
        }
    }

    public static function getBasketRange($indicatorId, $resultTarget, $baskets)
    {

        $baskets = $baskets->sortBy('range');

        $range = $baskets->map(function ($i) use ($resultTarget, $indicatorId) {
            if ($i['indicator_id'] == $indicatorId) {
                $temp = [];

                switch ($i['operation']) {
                    case '>=':
                        $temp['directive_range'] = $resultTarget >= $i['target'] ? $i['range'] : null;
                        $temp['directive_operation'] = $resultTarget >= $i['target'] ? $i['operation'] : null;
                        $temp['directive_target'] = $resultTarget >= $i['target'] ? $i['target'] : null;
                        $temp['directive_value'] = $resultTarget >= $i['target'] ? $i['value'] : null;
                        break;
                    case '<=':
                        $temp['directive_range'] = $resultTarget <= $i['target'] ? $i['range'] : null;
                        $temp['directive_operation'] = $resultTarget <=  $i['target'] ? $i['operation'] : null;
                        $temp['directive_target'] = $resultTarget <=  $i['target'] ? $i['target'] : null;
                        $temp['directive_value'] = $resultTarget <=  $i['target'] ? $i['value'] : null;
                        break;
                }

                return $temp;
            }
        })
            ->filter(function ($item) {
                return isset($item['directive_value']) && !is_null($item['directive_value']);
            })
            ->first();

        $default = [
            'directive_range' => null,
            'directive_operation' => null,
            'directive_target' => null,
            'directive_value' => null
        ];


        return  $range ??  $default;
    }

    public static function setEvaluationCalc($termId)
    {
        // Verifica se a meta do indicador foi atingida
        $evaluations = TermEvaluation::where('term_id', $termId)
            ->where('directive_type', '<>', TermInterface::DIRECTIVE_BASKET)
            ->get(['id', 'directive_type', 'directive_operation', 'directive_value', 'directive_target', 'result_target']);

        foreach ($evaluations  as $evaluation) {
            if ($evaluation->result_target) {

                $evaluation_value =  self::getDirectiveCalc(
                    $evaluation->directive_operation,
                    $evaluation->directive_target,
                    $evaluation->result_target
                );

                $data['evaluation_value'] = $evaluation_value;

                try {
                    TermEvaluation::find($evaluation->id)->update($data);
                } catch (\Throwable $th) {
                    Log::error('TermEvaluationService', ['setEvaluationCalc', $th->getMessage()]);
                }
            }
        }
    }

    public static function getDirectiveCalc($directiveOperation, $directiveTarget, $resultTarget)
    {
        // Verifica se a meta do indicador foi atingida 
        switch ($directiveOperation) {

            case '>=':
                $data = $resultTarget >= $directiveTarget ? '1' : '0';
                break;
            case '<=':
                $data = $resultTarget <= $directiveTarget ? '1' : '0';
                break;
            case '=':
                $data = $resultTarget == $directiveTarget ? '1' : '0';
                break;
        }

        return strval($data);
    }

    public static function  setFinalResult($termId, $roof)
    {
        $evaluations = TermEvaluation::where('term_id', $termId)
            ->where('directive_type', TermInterface::DIRECTIVE_RESULT)
            ->get(['id', 'term_id', 'username']);

        foreach ($evaluations  as $evaluation) {
            $data = self::getFinalResult($termId, $evaluation->username, $roof);

            $evaluation->evaluation_value = $data['evaluation_value'];
            $evaluation->evaluation_details =  $data;

            $evaluation->save();
        }
    }

    public static function  getFinalResult($termId, $username, $roof)
    {
        $evaluations = TermEvaluation::with(['indicator'])
            ->where('term_id', $termId)
            ->where('username', $username)
            ->get([
                'term_id',
                'username',
                'directive_type',
                'directive_value',
                'evaluation_value',
                'result_target',
                'result_value',
                'indicator_id'
            ]);



        $data = [
            'roof' => $roof ? (float) $roof : null,
            'basket' => 0,
            'accelerator' => 0,
            'deflator' => 0,
            'elimination' => 0,
            'evaluation_value' => 0,
            'elimination_reasons' => [],
            'indicators_results' => [],
        ];

        foreach ($evaluations as $evaluation) {


            $evaluation->indicator->target = (float) $evaluation->result_target;
            $evaluation->indicator->value = (float) $evaluation->result_value;

            if ($evaluation->directive_type != TermInterface::DIRECTIVE_RESULT) {
                $data['indicators_results'][] =  $evaluation->indicator->toArray();
            }

            if ($evaluation->directive_type == TermInterface::DIRECTIVE_ELIMINATION && $evaluation->evaluation_value > 0) {
                $data['elimination_reasons'][] =  $evaluation->indicator->toArray();
            }

            $data['basket'] = $evaluation->directive_type == TermInterface::DIRECTIVE_BASKET
                ? $evaluation->evaluation_value + $data['basket']
                :  $data['basket'];

            $data['accelerator'] = $evaluation->directive_type == TermInterface::DIRECTIVE_ACCELERATOR
                && $evaluation->evaluation_value > 0
                ? $evaluation->directive_value + $data['accelerator']
                :  $data['accelerator'];

            $data['deflator'] =  $evaluation->directive_type == TermInterface::DIRECTIVE_DEFLATOR
                && $evaluation->evaluation_value > 0
                ? $evaluation->directive_value + $data['deflator']
                :  $data['deflator'];

            $data['elimination'] = $evaluation->directive_type == TermInterface::DIRECTIVE_ELIMINATION
                && $evaluation->evaluation_value > 0
                ? $evaluation->evaluation_value + $data['elimination']
                :  $data['elimination'];

            $data['evaluation_value'] =  $data['elimination'] > 0
                ? 0
                : $data['basket'] + ($data['basket'] * $data['accelerator']) + ($data['basket'] * ($data['deflator'] * -1));
        }

        $data['evaluation_value'] = $data['evaluation_value'] >= $roof && $roof ? (float) $roof : $data['evaluation_value'];
        $data['indicators_results'] = collect($data['indicators_results'])->unique()->values()->toArray();
        $data['elimination'] = $data['elimination_reasons'] ? $data['basket'] * -1 : 0;

        return $data;
    }
}
