<?php

namespace App\Services\Core\User;

use App\Models\Modules\Employee\Employee;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class MetadataService
{
    public static function getMetadata(Request $request = null, ?string $username = null)
    {
        $route = Route::current();

        $username = Auth::user() ? Auth::user()->username : $username;

        $Employee =  Employee::where('username', $username)->first();


        if ($Employee) {
            $meta['user']  = [
                'username' => $Employee['username'],
                'name' => $Employee['name'],
                'position_summary' => $Employee['position_summary'],
                'sector_n1_id' => $Employee['sector_n1_id'],
                'sector_n2_id' => $Employee['sector_n2_id'],
                'manager_n1_id' => $Employee['manager_n1_id'],
                'manager_n2_id' => $Employee['manager_n2_id'],
                'manager_n3_id' => $Employee['manager_n3_id'],
                'manager_n4_id' => $Employee['manager_n4_id'],
                'manager_n5_id' => $Employee['manager_n5_id'],
                'status' => $Employee['status'],
            ];
        } elseif ($username) {
            $meta['user'] = [
                'username' => $username,
                'name' => 'Não localizado'
            ];
        } else {
            $meta['user']  = [
                'username' => 'SYS00000',
                'name' => 'Ahtlas - WebApplication',
            ];
        }

        if ($request) {

            $meta['ip'] = $request->header('X-Forwarded-For') ?? $request->ip() ?? 'Não localizado';
            $meta['route_name'] = $route ? $route->uri : 'Não localizado';
            $hostname = gethostbyaddr($meta['ip']);
            $meta['hostname'] = ($hostname !== $meta['ip']) ? $hostname : 'Não localizado';
            $meta['browser_name'] = strtolower(Browser::browserName()) ?? 'Não localizado';
            $meta['platform_name'] = strtolower(Browser::platformName()) ?? 'Não localizado';
        }

        return $meta;
    }
}
