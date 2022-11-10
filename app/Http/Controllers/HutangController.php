<?php

namespace App\Http\Controllers;

use App\Models\Hutang;
use App\Http\Requests\StoreHutangRequest;
use App\Http\Requests\UpdateHutangRequest;
use App\Http\Response\GeneralResponse;
use DB;

class HutangController extends Controller
{
    public function index()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Daftar Hutang'];

        return view('hutang.index', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function list()
    {
        $response = (new HutangController)->getList();
        return $response;
    }

    public function getList()
    {
        $hutang = DB::table("hutangs")
            ->join('pembelians', 'hutangs.pembelian_id', 'pembelians.id')
            ->orderBy('hutangs.bulan', 'ASC')
            ->orderBy('hutangs.tahun', 'ASC')
            ->get();

        if ($hutang) {
            return (new GeneralResponse)->default_json(true, 'success', $hutang, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
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
