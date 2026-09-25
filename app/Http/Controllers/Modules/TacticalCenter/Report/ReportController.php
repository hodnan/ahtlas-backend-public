<?php

namespace App\Http\Controllers\Modules\TacticalCenter\Report;

use App\Models\Modules\TacticalCenter\Report\Report;
use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\TacticalCenter\Report\ReportRequest;
use App\Models\Modules\Employee\Employee;
use App\Services\Modules\TacticalCenter\Report\ReportInterface;
use App\Services\Modules\TacticalCenter\Report\ReportService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $userType = Auth::user()->type;

            $group_default = $request->isMethod('get') ?  ReportInterface::GOUPS_DEFAULT[$userType] : [];

            $request['group'] =  !$request['group'] && !$request['filter'] ? $group_default : $request['group'];

            $statuses = $request->isMethod('get') ?  ReportInterface::STATUSES : [];
            $intervals = $request->isMethod('get') ?  ReportInterface::INTERVALS : [];
            $groups =  $request->isMethod('get') ?  ReportInterface::GROUPS : [];
            $reportTypes = $request->isMethod('get') ? ReportInterface::REPORT_TYPES : [];
            $owners = $request->isMethod('get') ? ReportService::getOwners() : [];
            $isOwner = ReportService::isOwner();
            $reports = ReportService::getReports($request->all());

            return ApiResponser::success(null, null, [
                'statuses' =>  $statuses,
                'intervals' =>  $intervals,
                'groups' => $groups,
                'group_default' => $group_default,
                'reportTypes' => $reportTypes,
                'reports' => $reports,
                'owners' => $owners,
                'isOwner' => $isOwner,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportRequest $request)
    {
        try {
            $requestData = $request->all();
            $data = [];
            $data['title'] = $requestData['title'] ?? null;
            $data['type'] =  $requestData['type'] ?? null;
            $data['group'] =  $requestData['group'] ?? null;
            $data['schedule_time'] =  $requestData['schedule_time'] ?? null;
            if (isset($requestData['url']) && $requestData['type'] == ReportInterface::REPORT_TYPE_DASH) {
                $data['report'] =  $requestData['url'];
            }
            $data['atd'] =  $requestData['atd'] ?? null;
            $data['interval_type'] =  $requestData['interval_type'] ?? null;
            $data['interval_values'] =  $requestData['interval_values'] ?? null;

            $data['active'] =  $requestData['active'];
            $data['notes'] =  $requestData['notes'] ?? null;

            $report = Report::create($data);

            $owners = collect($requestData['owners'])->map(function ($item) {
                $temp['username'] = $item;
                return  $temp;
            });

            $report->owners()->createMany($owners);
            if ($requestData['type'] == ReportInterface::REPORT_TYPE_CUBE) {
                ReportService::storeFile($request,  $report->id);
            }
            return ApiResponser::success('Relatório criado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao criar relatório ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        try {
            $report = Report::with(['owners'])->where('uuid', $uuid)->first();

            $report->type_value = $report->getTypeValueAttribute();
            return ApiResponser::success(null, null, ['report' => $report]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar relatório ', ['generic' => [['Erro ao carregar relatório']]]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReportRequest $request, Report $report)
    {
        try {
            $requestData = $request->all();

            $report->title = $requestData['title'] ?? $report->title;

            $report->group =  $requestData['group'] ?? $report->group;
            $report->schedule_time =  $requestData['schedule_time'] ?? $report->schedule_time;
            if (isset($requestData['url']) &&  $requestData['type'] == ReportInterface::REPORT_TYPE_DASH) {
                $report->report = $requestData['url'];
            }
            $report->atd =  $requestData['atd'] ?? $report->atd;
            $report->type =  $requestData['type'] ??  $report->type;
            $report->interval_type =  $requestData['interval_type'] ??  $report->interval_type;
            $report->interval_values =  $requestData['interval_values'] ?? $report->interval_values;

            $report->active =  $requestData['active'] ?? $report->active;
            $report->notes =  $requestData['notes'] ?? $report->notes;
            $report->save();

            $owners = collect($requestData['owners'])->map(function ($item) {
                $temp['username'] = $item;
                return  $temp;
            });

            $report->owners()->delete();
            $report->owners()->createMany($owners);

            if ($report->type['id'] == ReportInterface::REPORT_TYPE_CUBE) {
                ReportService::storeFile($request,  $report->id);
            }
            return ApiResponser::success('Relatório atualizado com sucesso', null, null);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao atualizar relatório ', [[['Erro ao carregar lista ']]]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function download(Report $report)
    {
        try {
            return  ReportService::downloadFile($report->id);
        } catch (\Throwable $th) {
            return ApiResponser::error('Erro ao baixar relatório ', [[['Falha ao baixar arquivo']]]);
        }
    }
}
