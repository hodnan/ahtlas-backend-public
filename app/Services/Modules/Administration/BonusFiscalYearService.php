<?php

namespace App\Services\Modules\Administration;

use App\Models\Modules\Administration\Incentives\Bonus\BonusFiscalYear;
use Illuminate\Http\Request;

class BonusFiscalYearService
{

    public static function store(Request $request)
    {
        try {

            $fiscalYear = BonusFiscalYear::create($request->all());

            if ($fiscalYear->active['id'] == 1) {
                BonusFiscalYear::where('id', '<>', $fiscalYear->id)
                    ->update(['active' => 0]);
            }
        } catch (\Throwable $th) {
            new \Exception($th->getMessage());
        }
    }

    public static function update($fiscalYear, Request $request)
    {
        try {
            $fiscalYear->active = $request->active;
            $fiscalYear->save();

            if ($fiscalYear->active['id'] == 1) {
                BonusFiscalYear::where('id', '<>', $fiscalYear->id)
                    ->update(['active' => 0]);
            }
        } catch (\Throwable $th) {
            new \Exception($th->getMessage());
        }
    }
}
