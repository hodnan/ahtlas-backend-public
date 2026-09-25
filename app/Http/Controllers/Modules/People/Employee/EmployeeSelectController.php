<?php

namespace App\Http\Controllers\Modules\People\Employee;

use App\Http\Controllers\Controller;
use App\Models\Modules\Employee\Employee;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Log;

class EmployeeSelectController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $employee = Employee::with(['avatar'])
            ->take(6)
            ->where(function ($query) use ($request) {
                $search = $request->search ?? '';
                $hierarchicalLevel = $request->hierarchical_level ?? '';

                if ($search) {
                    $query->where(function ($queryN2) use ($search) {
                        $queryN2->where('name', 'ILIKE', "%{$search}%");
                        $queryN2->orWhere('username', 'ILIKE', "%{$search}%");
                        $queryN2->orWhere('position_summary', 'ILIKE', "%{$search}%");
                    });
                }

                if ($hierarchicalLevel && !empty($hierarchicalLevel)) {
                    $query->whereIn('hierarchical_level', $hierarchicalLevel);
                }

                if ($request->accept_1 && $request->accept_2) {

                    $class = "App\Services\Modules" . $request->accept_1;
                    $method = $request->accept_2;

                    if (class_exists($class) && method_exists($class, $method)) {
                        $result = call_user_func([$class, $method]);
                        $query->whereIn('username', $result);
                    }
                }

                $query->where('active', 1);
                $query->where('type', '<>', 'cs');
            })
            ->orWhere(function ($query) use ($request) {
                $username = $request->username ?? '';

                if (is_array($username) && count($username) > 0) {
                    $query->orWhereIn('username', $username);
                }
                if ($username && is_string($username)) {
                    $query->orWhere('username', $username);
                }
            })
            ->when($request->username, function ($query) use ($request) {
                $username = $request->username;

                if (is_array($username) && count($username) > 0) {
                    Log::info('teste', [$username]);;
                    $query->orderByRaw("username = ANY(?) DESC", ['{' . implode(',', $username) . '}']);
                }

                if ($username && is_string($username)) {
                    $query->orderByRaw("username = ? DESC", [$username]);
                }
            })
            ->orderBy('hierarchical_level', 'desc')
            ->orderBy('name', 'asc');

        return ApiResponser::success(null, null, [
            'employee' => $employee->get(['username', 'name', 'position_summary', 'hierarchical_level', 'active']),

        ]);
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
    public function show(string $id)
    {
        //
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
