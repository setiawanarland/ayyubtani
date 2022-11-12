<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Http\Requests\StorePenjualanRequest;
use App\Http\Requests\UpdatePenjualanRequest;
use App\Http\Response\GeneralResponse;
use App\Models\DetailPenjualanTemp;
use App\Models\pajak;
use App\Models\Produk;
use DB;
use Illuminate\Http\Request;

class PenjualanController extends Controller
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
        $breadcrumbs = ['Penjualan'];

        $kios = DB::table('kios')
            ->select('id', 'nama_kios',)
            ->get();

        $produk = DB::table('produks')
            ->select('id', 'nama_produk', 'kemasan')
            ->get();

        $pajak = DB::table('pajaks')
            ->select('nama_pajak')
            ->where('active', '1')
            ->first();

        return view('penjualan.index', compact('page_title', 'page_description', 'breadcrumbs', 'kios', 'produk', 'pajak'));
    }

    public function list()
    {
        $response = (new PenjualanController)->getList();
        return $response;
    }

    public function getList()
    {
        $temp = DetailPenjualanTemp::select('detail_penjualan_temps.*', 'produks.nama_produk', 'produks.kemasan', 'produks.satuan', 'produks.harga_jual')
            ->join('produks', 'detail_penjualan_temps.produk_id', 'produks.id')
            ->get();

        $pajak = pajak::select('nama_pajak', 'satuan_pajak')
            ->where('active', '1')
            ->first();

        foreach ($temp as $key => $value) {
            $value->satuan_pajak = $pajak->satuan_pajak;
        }

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
        $hargaSatuan = $produk->harga_jual / $produk->jumlah_perdos;
        $jumlah = $hargaSatuan * $qty;
        $jumlahDisc = $jumlah * $request->disc / 100;
        $jumlahAfterDisc = $jumlah - $jumlahDisc;

        $dataDetail = DetailPenjualanTemp::where('produk_id', $request->produk_id)->first();
        if ($dataDetail != null) {
            return (new GeneralResponse)->default_json(false, "Barang sudah ada!", null, 422);
        }

        $data = new DetailPenjualanTemp();
        $data->produk_id = $request->produk_id;
        $data->qty = $qty;
        // $data->harga_satuan = $hargaSatuan;
        $data->ket = $request->ket;
        $data->disc = $request->disc;
        $data->jumlah = $jumlahAfterDisc;
        $data->save();
        // return $data;

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 403);
        }
    }

    public function tempDelete(Request $request, $id)
    {
        $data = DetailPenjualanTemp::where('id', $id)->first();
        $data->delete();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function tempReset(Request $request)
    {
        $data = DetailPenjualanTemp::truncate();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function preview(Request $request)
    {
        $data = [];

        // get kios information
        $kios = DB::table('kios')
            ->where('id', $request->kios)
            ->first();
        $data['kios'] = $kios;

        // get produks information
        $produks = [];
        foreach ($request->produk_id as $key => $value) {
            $produks[] = DB::table('produks')
                ->where('id', $value)
                ->first();
        }

        // add qty, ket, jumlah by produk
        foreach ($produks as $key => $value) {
            $value->qty = $request->qty[$key];
            $value->ket = $request->ket[$key];
            $value->disc = $request->disc[$key];
            $value->jumlah = $request->jumlah[$key];
        }
        $data['produks'] = $produks;

        // set jatuhTempo 
        $jatuhTempo = date('d/m/Y', strtotime('+1 months', strtotime($request->tanggal_jual)));

        // set data
        $data['invoice'] = $request->invoice;
        $data['tanggal_jual'] = $request->tanggal_jual;
        $data['jatuh_tempo'] = $jatuhTempo;
        $data['dpp'] = $request->dpp;
        $data['ppn'] = $request->ppn;
        $data['total_disc'] = $request->total_disc;
        $data['grand_total'] = $request->grand_total;

        return $data;
    }

    public function getStok(Request $request, $id)
    {
        $produk = Produk::select('nama_produk', 'kemasan', 'stok')->where('id', $id)->first();

        if ($produk->stok == 0) {
            return (new GeneralResponse)->default_json(false, "Stok kosong!", $produk, 401);
        }

        if ($produk->stok < $request->qty) {
            return (new GeneralResponse)->default_json(false, "Stok tersisa {$produk->stok}!", $produk, 401);
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
     * @param  \App\Http\Requests\StorePenjualanRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePenjualanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function show(Penjualan $penjualan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function edit(Penjualan $penjualan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePenjualanRequest  $request
     * @param  \App\Models\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePenjualanRequest $request, Penjualan $penjualan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Penjualan $penjualan)
    {
        //
    }
}
