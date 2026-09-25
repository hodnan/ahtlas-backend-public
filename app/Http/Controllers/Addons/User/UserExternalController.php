<?php

namespace App\Http\Controllers\Addons\User;

use App\Http\Controllers\Controller;
use App\Services\Core\User\UserService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class UserExternalController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        UserService::updateOrCreateExternalUser();
        UserService::updateManagerExternalUser();
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
