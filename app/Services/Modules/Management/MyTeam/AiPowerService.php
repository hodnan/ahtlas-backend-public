<?php

namespace App\Services\Modules\Management\MyTeam;

use App\Mail\aiPowerEmployeeMail;
use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Management\MyTeam\AiEmployeePower;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\LazyCollection;

class AiPowerService
{
    public static function setManagers()
    {
        $monthRef = Carbon::now()->subMonths(1)->startOfMonth()->startOfDay();

        $sql = "UPDATE modules.ai_employee_powers AS aep
                SET
                    sector_n1_id = e.sector_n1_id,
                    manager_n1_id = e.manager_n1_id,
                    manager_n2_id = e.manager_n2_id,
                    manager_n3_id = e.manager_n3_id,
                    manager_n4_id = e.manager_n4_id,
                    manager_n5_id = e.manager_n5_id
                FROM modules.employees AS e
                WHERE aep.username = e.username
                AND aep.month_ref  >= '$monthRef'";

        DB::statement($sql);
    }

    public static function sendMail($monthRef)
    {
        $monthRef = Carbon::parse($monthRef)->format('Y-m-d');

        $managersN3 = AiEmployeePower::whereNotNUll('manager_n3_id')
            ->where('month_ref', $monthRef)
            ->where('manager_n3_id', '<>', 'bc0')
            ->distinct('manager_n3_id')
            ->pluck('manager_n3_id')
            ->toArray();

        $managersN2 = AiEmployeePower::whereNotNUll('manager_n2_id')
            ->where('month_ref', $monthRef)
            ->where('manager_n2_id', '<>', 'bc0')
            ->distinct('manager_n2_id')
            ->pluck('manager_n2_id')
            ->toArray();

        $managers = array_merge($managersN3, $managersN2);
        $managers = array_unique($managers);

        foreach ($managers as $value) {

            $employee = Employee::where('username', $value)->get(['username', 'name', 'nickname'])->first()->toArray();

            $data = AiEmployeePower::select(
                'MONTH_REF',
                'MANAGER_N3_ID',
                'HIERARCHICAL_LEVEL',
                'employee_ai_power_actions.ACTION',
                DB::raw('count(*) as volume')
            )
                ->leftJoin('employee_ai_power_actions', 'employee_ai_power_actions.ID', '=', 'employee_ai_powers.ACTION_ID')
                ->groupBy('MONTH_REF', 'MANAGER_N3_ID', 'HIERARCHICAL_LEVEL', 'employee_ai_power_actions.ACTION')
                ->where('month_ref', $monthRef)
                ->where(function ($query) use ($value) {
                    $query->orWhere('manager_n2_id', $value);
                    $query->orWhere('manager_n3_id', $value);
                })
                ->orderBy('MONTH_REF')
                ->orderBy('MANAGER_N3_ID')
                ->orderBy('HIERARCHICAL_LEVEL')
                ->orderBy('employee_ai_power_actions.ACTION')
                ->get()
                ->toArray();

            try {
                Mail::to(config('ahtlas.ai_power.mail_to'), config('ahtlas.ai_power.mail_to_name'))
                    ->cc(config('ahtlas.ai_power.mail_to'), config('ahtlas.ai_power.mail_to_name'))
                    ->queue((new aiPowerEmployeeMail($employee, $data, $monthRef))->onQueue('ia-power'));
            } catch (\Throwable $th) {
                return $th;
            }
        }
    }

    public static function list( $request)
    {
        $manager = $request['manager'] ?? Auth::user()->username;
        $sectorN1 = $request['sectorN1'] ?? null;
        $monthRef = isset($request['month_ref']) ? Carbon::parse($request['month_ref'])->startOfMonth()->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();

        // return [ $manager, $sectorN1,  $monthRef  ];
        try {
            return AiEmployeePower::with(['username', 'action', 'sectorN1'])
                ->where(function ($query) use ($manager) {
                    $query->orWhere('username', $manager);
                    $query->orWhere('manager_n1_id', $manager);
                    $query->orWhere('manager_n2_id', $manager);
                    $query->orWhere('manager_n3_id', $manager);
                    $query->orWhere('manager_n4_id', $manager);
                    $query->orWhere('manager_n5_id', $manager);
                })
                ->where(function ($query) use ($sectorN1) {
                    if ($sectorN1) {
                        $query->orWhere('sector_n1_id', $sectorN1);
                    }
                })
                // ->whereHas('action', function ($query) use ($action) {
                //     $query->where('action', $action);
                // })
                ->where('month_ref', $monthRef)
                ->get();
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
