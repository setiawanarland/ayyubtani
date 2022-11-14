<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use App\Http\Requests\StorePiutangRequest;
use App\Http\Requests\UpdatePiutangRequest;
use App\Http\Response\GeneralResponse;
use DB;

class PiutangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Daftar Piutang'];

        $data = [];

        $debet = 0;
        $kredit = 0;
        $sisa = 0;

        $totalPiutang = 0;
        $totalSisa = 0;

        $kios = DB::table('kios')->get();
        foreach ($kios as $key => $value) {
            $debet = 0;
            $kredit = 0;
            $sisa = 0;

            $penjualan = DB::table("penjualans")
                ->select('penjualans.id')
                ->join('kios', 'penjualans.kios_id', 'kios.id')
                ->where('penjualans.kios_id', $value->id)
                ->orderBy('penjualans.bulan', 'ASC')
                ->orderBy('penjualans.tahun', 'ASC')
                ->get();
            $value->penjualan = $penjualan;
            foreach ($penjualan as $index => $val) {
                $piutang = DB::table("piutangs")
                    ->join('penjualans', 'piutangs.penjualan_id', 'penjualans.id')
                    ->where('piutangs.penjualan_id', $val->id)
                    ->orderBy('piutangs.bulan', 'ASC')
                    ->orderBy('piutangs.tahun', 'ASC')
                    ->first();

                $debet += $piutang->debet;
                $kredit += $piutang->kredit;
                $sisa += $piutang->sisa;
                // return $sisa;
            }

            $value->debet = $debet;
            $value->kredit = $kredit;
            $value->sisa = $sisa;

            $totalPiutang += $value->debet;
            $totalSisa += $value->sisa;
        }

        $data['kios'] = $kios;
        $data['total_piutang'] = $totalPiutang;
        $data['total_sisa'] = $totalSisa;
        // return $data;

        return view('piutang.index', compact('page_title', 'page_description', 'breadcrumbs', 'data'));
    }

    public function list()
    {
        $response = (new PiutangController)->getList();
        return $response;
    }

    public function getList()
    {
        $piutang = DB::table("piutangs")
            ->join('penjualans', 'piutangs.penjualan_id', 'penjualans.id')
            ->orderBy('piutangs.bulan', 'ASC')
            ->orderBy('piutangs.tahun', 'ASC')
            ->get();

        if ($piutang) {
            return (new GeneralResponse)->default_json(true, 'success', $piutang, 200);
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
     * @param  \App\Http\Requests\StorePiutangRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePiutangRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Piutang  $piutang
     * @return \Illuminate\Http\Response
     */
    public function show(Piutang $piutang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Piutang  $piutang
     * @return \Illuminate\Http\Response
     */
    public function edit(Piutang $piutang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePiutangRequest  $request
     * @param  \App\Models\Piutang  $piutang
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePiutangRequest $request, Piutang $piutang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Piutang  $piutang
     * @return \Illuminate\Http\Response
     */
    public function destroy(Piutang $piutang)
    {
        //
    }
}
