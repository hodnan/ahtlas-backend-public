<?php

namespace App\Http\Controllers\Core\Notify;

use App\Http\Controllers\Controller;
use App\Models\Core\Notify\Notification;
use App\Services\Core\Notify\NotifyInterface;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class NotificationController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $statuses =  $request->isMethod('get') ? NotifyInterface::STATUSES : null;
            $categories =  $request->isMethod('get') ? NotifyInterface::CATEGORIES : null;
            return ApiResponser::success(null, null, [
                'statuses' => $statuses,
                'categories' => $categories,
            ]);
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [[['Erro ao carregar lista']]]);
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
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        //
    }
}
