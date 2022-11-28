<?php

namespace App\Http\Controllers;

use App\Models\LimitPutang;
use App\Http\Requests\StoreLimitPutangRequest;
use App\Http\Requests\UpdateLimitPutangRequest;

class LimitPutangController extends Controller
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
     * @param  \App\Http\Requests\StoreLimitPutangRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLimitPutangRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LimitPutang  $limitPutang
     * @return \Illuminate\Http\Response
     */
    public function show(LimitPutang $limitPutang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LimitPutang  $limitPutang
     * @return \Illuminate\Http\Response
     */
    public function edit(LimitPutang $limitPutang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLimitPutangRequest  $request
     * @param  \App\Models\LimitPutang  $limitPutang
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLimitPutangRequest $request, LimitPutang $limitPutang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LimitPutang  $limitPutang
     * @return \Illuminate\Http\Response
     */
    public function destroy(LimitPutang $limitPutang)
    {
        //
    }
}
