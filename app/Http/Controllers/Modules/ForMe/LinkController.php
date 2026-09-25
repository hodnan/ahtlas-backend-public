<?php

namespace App\Http\Controllers\Modules\ForMe;

use App\Models\Modules\ForMe\Link;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class LinkController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */

    public function linkDisplay()
    {
        try {
            $links = Link::get();
            return ApiResponser::success(null, null, ['links' =>$links ] );
        } catch (\Throwable $e) {
            return ApiResponser::error('Erro ao carregar lista ', [['Erro ao carregar lista ']]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(Link $link)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Link $link)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Link $link)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Link $link)
    {
        //
    }
}
