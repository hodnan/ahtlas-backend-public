<?php

namespace App\Http\Controllers\Modules\People\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;

class BathroomController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index($username)
    {
        $results = DB::connection('mis_primary')->select(
            '
                SELECT [CD_AGENT_]
                       ,[NO_V_EXCODE]
                       ,[NO_RESPONSAVEL]
                       ,[NO_JORNADA]
                       ,A.[SK_V_EXCODE] AS COD_PAUSA
                       ,CAST(DATEADD(DAY,[CD_DATE_STIME_],\'1900-01-01\') AS DATE) AS DATE_
                       ,CONVERT(VARCHAR, DATEADD(SECOND, [CD_HOUR_STIME_], 0), 108) AS [CD_HOUR_STIME_]
                       ,CONVERT(VARCHAR, DATEADD(SECOND, [CD_HOUR_ETIME_], 0), 108) AS [CD_HOUR_ETIME_]
                       ,[NU_DURATION_]
                FROM [DB_RH].[dbo].[TB_ABS_ADER_FT_V_ACTIVITYLOG] AS A
                LEFT JOIN [DB_RH].[dbo].[TB_WFM_DM_V_EXCEPT] AS B
                ON A.[SK_V_EXCODE] = B.[SK_V_EXCODE]
                WHERE [CD_AGENT_] = ? 
                AND [NO_V_EXCODE] = ?
            ',
            [$username, 'Banheiro']
        );

        if ($results) {
            // Transformando o resultado em um array associativo
            $results = json_decode(json_encode($results), true);

            // Gerando o CSV a partir do array
            $csvContent = makeCsv($results);

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="Banheiro-'.$username.'.csv"',
            ];

            return response()->make($csvContent, 200, $headers);
        } else {
            return ApiResponser::error('Dados não localizados');
        }
    }
}
