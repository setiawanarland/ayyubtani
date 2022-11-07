<?php

namespace App\Http\Controllers;

use App\Http\Response\GeneralResponse;
use App\Models\Pembelian;
use App\Models\PembelianTemp;
use App\Models\Produk;
use Illuminate\Http\Request;
use DB;

class PembelianController extends Controller
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
        $breadcrumbs = ['Pembelian'];

        $supplier = DB::table('suppliers')
            ->select('id', 'nama_supplier',)
            ->get();

        $produk = DB::table('produks')
            ->select('id', 'nama_produk', 'kemasan')
            ->get();

        return view('pembelian.index', compact('page_title', 'page_description', 'breadcrumbs', 'supplier', 'produk'));
    }

    public function list()
    {
        $response = (new PembelianController)->getList();
        return $response;
    }

    public function getList()
    {
        $temp = PembelianTemp::select('pembelians_temp.*', 'produks.nama_produk', 'produks.kemasan', 'produks.satuan', 'produks.harga_jual')
            ->join('produks', 'pembelians_temp.produk_id', 'produks.id')
            ->get();

        if ($temp) {
            return (new GeneralResponse)->default_json(true, 'success', $temp, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    public function temp(Request $request)
    {
        $produk = Produk::where('id', $request->produk_id)->first();
        $qty = $produk->jumlah_perdos * $request->ket;
        $jumlah = $produk->harga_perdos * $request->ket;
        // return $jumlah;

        $data = new PembelianTemp();
        $data->produk_id = $request->produk_id;
        $data->qty = $qty;
        $data->ket = $request->ket;
        $data->disc = $request->disc;
        $data->jumlah = $jumlah;
        $data->save();
        // return $data;

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 403);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function show(pembelian $pembelian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function edit(pembelian $pembelian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pembelian $pembelian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function destroy(pembelian $pembelian)
    {
        //
    }
}
