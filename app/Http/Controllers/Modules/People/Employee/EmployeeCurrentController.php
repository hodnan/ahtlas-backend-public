<?php

namespace App\Http\Controllers\Modules\People\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\People\Employee\EmployeeCurrentRequest;
use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Employee\SectorN1;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeCurrentController extends Controller
{
    use ApiResponser;
  
    public function index(EmployeeCurrentRequest $request)
    {
        $active = isset($request['active']) ? $request['active'] : null;
        $name = isset($request['name']) ? $request['name'] : null;
        $username = isset($request['username']) ?  str_replace("ext", "usr", strtolower($request['username'] )): null;
        $manager = !isset($request['manager']) && !isset($request['filter']) ? Auth::user()->manager_n1_id : $request['manager'];
        $sectorN1 = $request['sector_n1_id'];

        try {
            $managers = Employee::with(['avatar'])
                ->where('active', 1)
                ->where('hierarchical_level', '>=', 1)
                ->orderBy('hierarchical_level')
                ->orderBy('name')
                ->get(['username', 'name', 'position_summary', 'hierarchical_level']);

            $sectorsN1 = SectorN1::where('active', 1)
                ->orderby('name')
                ->get(['id', 'name', 'uf']);

            $employees = Employee::with(['managerN1', 'managerN2', 'managerN3',  'managerN5', 'sectorN1', 'sectorN1'])
                ->where(function ($query) use ($active) {
                    if($active){
                        $query->where('active', $active);
                    }
                })
                ->where(function ($query) use ($name) {
                    if($name){
                        $query->where('name', 'ILIKE', "%$name%");
                    }
                })
                ->where(function ($query) use ($username) {
                    if($username){
                        $query->where('username', 'like', "%$username%");
                    }
                })
                ->where(function ($query) use ($manager) {
                    if ($manager) {
                        $query->whereRaw("'$manager' in (manager_n1_id,manager_n2_id, manager_n3_id,manager_n4_id, manager_n5_id)");                    
                    }
                })
                ->where(function ($query) use ($sectorN1) {
                    if ($sectorN1) {
                        $query->where('sector_n1_id', $sectorN1);
                    }
                })
                ->orderBy('position_summary')
                ->orderBy('name')
                // ->take('30')
                ->get();

            return ApiResponser::success(null, null, [
                'aa' => $request->all(),
                'managers' => $managers,
                'sectorsN1' => $sectorsN1,
                'employees' => $employees,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $username)
    {
        $employee = Employee::with(['avatar', 'sectorN1', 'sectorN2', 'managerN1', 'managerN2', 'managerN3', 'managerN4', 'managerN5'])
        ->where('username', $username)->first();

        return ApiResponser::success(null, null, [      
            'employee' =>  $employee,
            
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
