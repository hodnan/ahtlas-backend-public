<?php

namespace App\Http\Controllers\Addons\Assistant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Addons\Assistant\AssistantSession;
use Illuminate\Support\Facades\Redirect;

class AssistantSessionController extends Controller
{
    public function chat(Request $request)
    {
        try {
            /** 
             * @disregard 
             */
            $username = auth()->user()->username;
            $assistant = AssistantSession::firstOrCreate(['username' => $username]);;

            $assistant->bearer =  $assistant->getBearer();
            $assistant->session_id =  $assistant->getSession();

            $assistant->save();

            $response = $assistant->sendMessage($request->comment);
            return  $response;
        } catch (\Exception $e) {
            return $e;
        }
    }
}
