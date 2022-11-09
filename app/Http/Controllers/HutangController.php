<?php

namespace App\Http\Controllers;

use App\Models\Hutang;
use App\Http\Requests\StoreHutangRequest;
use App\Http\Requests\UpdateHutangRequest;

class HutangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreHutangRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHutangRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hutang  $hutang
     * @return \Illuminate\Http\Response
     */
    public function show(Hutang $hutang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Hutang  $hutang
     * @return \Illuminate\Http\Response
     */
    public function edit(Hutang $hutang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateHutangRequest  $request
     * @param  \App\Models\Hutang  $hutang
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHutangRequest $request, Hutang $hutang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hutang  $hutang
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hutang $hutang)
    {
        //
    }
}
