<?php

namespace App\Services\Modules\Administration;

use App\Models\Modules\Administration\Intelligence\KpiRelated;
use App\Models\Modules\Administration\Intelligence\KpiResult;
use App\Models\Modules\Administration\Intelligence\KpiSource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class KpiSourceService
{
    public static function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();
            $data['db'] = KpiSourceInterface::SERVER . '.' . KpiSourceInterface::DB;

            $source = KpiSource::create($data);
            $source->source = 'TB_KPI_IND_' . str_pad($source->indicator, 5, '0', STR_PAD_LEFT) . '_ID_' . str_pad($source->id, 5, '0', STR_PAD_LEFT) . "_FT";
            $source->save();

            $indicator = $source->indicator;

            $sectors = collect($data['sectors'])->map(function ($item) use ($indicator) {
                $item['indicator_id'] =  $indicator;
                return  $item;
            });

            $source->sectors()->createMany($sectors);

            self::creteTable($source->source);

            DB::commit();
        } catch (\Throwable $e) {
            $source->sectors()->delete();
            self::dropTable($source->source);
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }


    public static function update(Request $request, KpiSource $kpiSource)
    {

        DB::beginTransaction();

        try {
            if ($request->rebuild) {
                $kpiSource->db = $data['db'] = KpiSourceInterface::SERVER . '.' . KpiSourceInterface::DB;
                $kpiSource->source = 'TB_KPI_IND_' . str_pad($kpiSource->indicator, 5, '0', STR_PAD_LEFT) . '_ID_' . str_pad($kpiSource->id, 5, '0', STR_PAD_LEFT) . "_FT";
                self::dropTable($kpiSource->source);
                self::creteTable($kpiSource->source);
            }

            $kpiSource->owner =  $request->owner;
            $kpiSource->report_id =  $request->report_id;
            $kpiSource->notes =  $request->notes;
            $kpiSource->sla =  $request->sla;
            $kpiSource->data_location =  $request->data_location;
            $kpiSource->active =  $request->active;

            $indicator = $kpiSource->indicator;

            $kpiSource->save();

            $sectors = collect($request->sectors)->map(function ($item) use ($indicator) {
                $item['indicator_id'] =  $indicator;
                return  $item;
            });

            $kpiSource->sectors()->delete();
            $kpiSource->sectors()->createMany($sectors);

            DB::commit();
        } catch (\Throwable $e) {

            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public static function creteTable(string $tableName)
    {
        try {
            if(env('APP_ENV') == 'production') {
            $tableName = strtoupper($tableName);
            $tableDbName =  KpiSourceInterface::DB . ".dbo.$tableName";

            Schema::connection('mis_primary')->create($tableDbName, function (Blueprint $table) use ($tableName) {
                $table->integer('ANO')->nullable(false);
                $table->integer('MES')->nullable(false);
                $table->integer('MATRICULA')->nullable();
                $table->integer('CD_SETOR')->nullable();
                $table->integer('CD_INDICADOR')->nullable(false);
                $table->integer('META')->nullable();
                $table->float('RESULTADO')->nullable();
                $table->integer('FATOR_0')->nullable();
                $table->integer('FATOR_1')->nullable();
                $table->integer('FATOR_2')->nullable();
                $table->integer('DATA_REF')->nullable(false);
                $table->integer('INSERIDO_POR')->nullable(false);
                $table->unique(['DATA_REF', 'CD_SETOR', 'CD_INDICADOR', 'MATRICULA'], "UQ_{$tableName}");
            });
            }
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function dropTable(string $tableName)
    {
        try {

            $tableName = strtoupper(KpiSourceInterface::DB . ".dbo.$tableName");

            Schema::connection('mis_primary')->dropIfExists($tableName);
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function setResult()
    {
        $dataInicio = Carbon::now()->subMonth()->startOfMonth();

        // deleta dados no periodo
        KpiResult::where('date_ref', '>=', $dataInicio)->delete();

        $dataAtual = Carbon::now();

        // Faz busca na base dia a dia
        while ($dataInicio->lte($dataAtual)) {

            $dataString = $dataInicio->toDateString();

            $data = DB::connection('mis_primary')
                ->table('DB_CORPORATIVO.dbo.TB_CORP_KPI_RESULT_DIA_FT')
                ->where('CD_DT_REF', DB::raw("DATEDIFF(day, 0, '$dataString')"))
                ->select([
                    DB::raw("DATEADD(day, 0, CD_DT_REF) as date_ref"),
                    DB::raw("'usr' + cast(CD_MATRICULA as varchar) as username"),
                    DB::raw("CASE WHEN CD_MATRICULA > 599999 then 'usr' + cast(CD_MATRICULA as varchar) else '5A' + RIGHT('000000' + RTRIM(CD_MATRICULA), 6) end as username"),
                    DB::raw('CD_SETOR as sector_n1_id'),
                    DB::raw('CD_INDICADOR as indicator_id'),
                    DB::raw('CD_RESULTADO as "result"'),
                    DB::raw('CD_FATOR_0 as factor_0'),
                    DB::raw('CD_FATOR_1 as factor_1'),
                    DB::raw('CD_FATOR_2 as factor_2'),
                    DB::raw('GETDATE() as created_at'),
                    DB::raw('GETDATE() as updated_at')
                ])
                ->get()->toArray();

            // Insere dados fracionados no banco 
            LazyCollection::make($data)
                ->chunk(999)
                ->each(function ($chunk) {
                    try {
                        $data = $chunk->map(function ($user) {
                            return (array) $user;
                        })->toArray();
                        KpiResult::insert($data);
                    } catch (\Throwable $e) {
                        Log::error($e->getMessage());
                    }
                });
            $dataInicio->addDay();
        }
    }

    public static function setUpdates($sourceId = null)
    {
        $kpis = KpiRelated::with(['source'])
        ->where(function($query) use ($sourceId) {
            if($sourceId ){
                $query->where('source_id',$sourceId);
            }
        })
        ->get(['id', 'sector_n1_id', 'indicator_id', 'source_id'])
        ->toArray();

        LazyCollection::make($kpis)
            ->chunk(1000)
            ->each(function ($chunk) {

                foreach ($chunk as  $item) {

                    try {
                        $last_update = KpiResult::where('sector_n1_id', $item['sector_n1_id'])
                            ->where('indicator_id', $item['indicator_id'])
                            ->max('date_ref');

                        $result = KpiResult::where('sector_n1_id', $item['sector_n1_id'])
                            ->where('indicator_id', $item['indicator_id'])
                            ->where('date_ref', $last_update)
                            ->selectRaw('SUM(factor_0) /nullif(SUM(factor_1),0) as "result"')
                            ->first();

                        $last_result = $result->result;

                        $sla =  $item['source']['sla'] ?? -2;
                        $targetDay = Carbon::now()->addDays($sla);

                        $lastUpdate = $last_update ? Carbon::parse($last_update) : Carbon::now()->subMonth()->startOfMonth();
                        $daysDifference = $targetDay->diffInDays($lastUpdate);

                        $kpi = KpiRelated::find($item['id']);
                        $kpi->last_update =  $last_update;
                        $kpi->last_result =  $last_result;
                        $kpi->delay =   (int) $daysDifference;
                        $kpi->save();

                    } catch (\Throwable $e) {
                        Log::error('KPi Related last result',['Erro ao carregar lista ']);
                    }
                }
            });
    }
}
