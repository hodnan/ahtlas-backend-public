<?php

namespace App\Http\Controllers\Core\Notify;

use App\Http\Controllers\Controller;
use App\Models\Core\Notify\NotificationUser;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;

class NotificationUserController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $username = Auth::user()->username; 
            $messages = NotificationUser::with(['notification'])
            ->where('username', $username )
            ->where('received', 0)
            ->get(['id', 'notification_id', 'read']);

            foreach ($messages as $message) {
                $message->update(['received' => 1]);
            }

            return $messages;
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista', [[['Erro ao carregar lista']]]);
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
    public function show(Request $request)
    {
        try {
            $username = Auth::user()->username; 
            $messages = NotificationUser::with(['notification'])
            ->where('username', $username )
            ->take(50)
            ->get();
            foreach ($messages as $message) {
                $message->update(['received' => 1]);
            }
            return $messages;
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista', [[['Erro ao exibir dado']]]);
        }   
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NotificationUser $notificationUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NotificationUser $notificationUser)
    {
        //
    }
}
