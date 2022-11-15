<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Http\Requests\StorePenjualanRequest;
use App\Http\Requests\UpdatePenjualanRequest;
use App\Http\Response\GeneralResponse;
use App\Models\DetailPenjualan;
use App\Models\DetailPenjualanTemp;
use App\Models\Kios;
use App\Models\pajak;
use App\Models\Piutang;
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
            ->select('id', 'nama_produk', 'kemasan', 'stok')
            ->get();

        $pajak = DB::table('pajaks')
            ->select('nama_pajak')
            ->where('active', '1')
            ->first();

        $lastPenjualan = Penjualan::max('id');

        $invoice = "AT-" . substr(date('Y'), -2) . "/" . sprintf("%05s", abs($lastPenjualan + 1));

        return view('penjualan.index', compact('page_title', 'page_description', 'breadcrumbs', 'kios', 'produk', 'pajak', 'invoice'));
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
        $hargaSatuan = $produk->harga_jual;
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
    public function store(Request $request)
    {
        $dataPenjualan = [];
        $dataPenjualan['bulan'] = date('m', strtotime($request->tanggal_jual));
        $dataPenjualan['tahun'] = date('Y', strtotime($request->tanggal_jual));


        $penjualan = new Penjualan();
        $penjualan->kios_id = $request->kios;
        $penjualan->invoice = $request->invoice;
        $penjualan->tanggal_jual = date('Y-m-d', strtotime($request->tanggal_jual));
        $penjualan->bulan = $dataPenjualan['bulan'];
        $penjualan->tahun = $dataPenjualan['tahun'];
        $penjualan->dpp = intval(preg_replace("/\D/", "", $request->dpp));
        $penjualan->ppn = intval(preg_replace("/\D/", "", $request->ppn));
        $penjualan->total_disc = intval(preg_replace("/\D/", "", $request->total_disc));
        $penjualan->grand_total = intval(preg_replace("/\D/", "", $request->grand_total));
        $penjualan->save();

        foreach ($request->produk_id as $key => $value) {
            $detailPenjualan = new DetailPenjualan();
            $detailPenjualan->penjualan_id = $penjualan->id;
            $detailPenjualan->produk_id = $value;
            $detailPenjualan->qty = $request->qty[$key];
            $detailPenjualan->ket = $request->ket[$key];
            $detailPenjualan->disc = $request->disc[$key];
            $detailPenjualan->jumlah = intval(preg_replace("/\D/", "", $request->jumlah[$key]));
            $detailPenjualan->save();

            $produk = Produk::where('id', $value)->first();
            $stok = $produk->stok;
            $jumlahPerdos = $produk->jumlah_perdos;
            $stokkeluar = $request->qty[$key] / $jumlahPerdos;
            $produk->stok = $stok - $stokkeluar;
            $produk->save();
        }

        $dataPiutang = [];
        $dataPiutang['bulan'] = date('m', strtotime($request->tanggal_jual));
        $dataPiutang['tahun'] = date('Y', strtotime($request->tanggal_jual));

        $piutang = new Piutang();
        $piutang->penjualan_id = $penjualan->id;
        $piutang->bulan = $dataPiutang['bulan'];
        $piutang->tahun = $dataPiutang['tahun'];
        $piutang->ket = '';
        $piutang->debet = intval(preg_replace("/\D/", "", $request->grand_total));
        $piutang->kredit = 0;
        $piutang->sisa = intval(preg_replace("/\D/", "", $request->grand_total)) - $piutang->kredit;
        $piutang->save();

        $temp = DetailPenjualanTemp::truncate();

        if ($temp) {
            return (new GeneralResponse)->default_json(true, "Success", null, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", null, 404);
        }
    }

    public function daftar()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Daftar Penjualan'];

        return view('penjualan.daftar', compact('page_title', 'page_description', 'breadcrumbs',));
    }

    public function listPenjualan()
    {
        $response = (new PenjualanController)->getListPenjualan();
        return $response;
    }

    public function getListPenjualan()
    {
        $data = Penjualan::select('penjualans.*', 'kios.nama_kios',)
            ->join('kios', 'penjualans.kios_id', 'kios.id')
            // ->orderBy('penjualans.bulan', 'ASC')
            // ->orderBy('penjualans.tahun', 'ASC')
            ->orderBy('penjualans.id', 'DESC')
            ->get();


        if ($data) {
            return (new GeneralResponse)->default_json(true, 'success', $data, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\pembelian  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $data = [];

        $penjualan = Penjualan::where('id', $id)->first();
        $kios = Kios::select('nama_kios', 'alamat', 'kabupaten')->where('id', $penjualan->kios_id)->first();
        $detailPenjualan = DetailPenjualan::where('penjualan_id', $penjualan->id)->get();

        foreach ($detailPenjualan as $key => $value) {
            $produk = Produk::select('nama_produk', 'kemasan')->where('id', $value->produk_id)->first();
            $value->nama_produk = $produk->nama_produk;
            $value->kemasan_produk = $produk->kemasan;
        }

        $data['penjualan'] = $penjualan;
        $data['kios'] = $kios;
        $data['detailPenjualan'] = $detailPenjualan;

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Penjualan'];

        $penjualan = Penjualan::with('detailPenjualan', 'kios')
            ->where('penjualans.id', $id)
            ->first();

        $kios = DB::table('kios')
            ->select('id', 'nama_kios',)
            ->get();

        $produk = DB::table('produks')
            ->select('id', 'nama_produk', 'kemasan', 'stok')
            ->get();

        $pajak = DB::table('pajaks')
            ->select('nama_pajak')
            ->where('active', '1')
            ->first();

        // return $penjualan;
        return view('penjualan.edit', compact('page_title', 'page_description', 'breadcrumbs', 'kios', 'produk', 'pajak', 'penjualan'));
    }

    public function listEditPenjualan(Request $request)
    {
        $response = (new PenjualanController)->getListEditPenjualan($request->id);
        return $response;
    }

    public function getListEditPenjualan($id)
    {
        $data = DetailPenjualan::select('detail_penjualans.*', 'produks.nama_produk', 'produks.kemasan', 'produks.satuan', 'produks.harga_jual', 'produks.jumlah_perdos')
            ->join('produks', 'detail_penjualans.produk_id', 'produks.id')
            ->where('detail_penjualans.penjualan_id', $id)
            ->get();

        $pajak = pajak::select('nama_pajak', 'satuan_pajak')
            ->where('active', '1')
            ->first();

        foreach ($data as $key => $value) {
            $value->satuan_pajak = $pajak->satuan_pajak;
        }

        if ($data) {
            return (new GeneralResponse)->default_json(true, 'success', $data, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    public function addEdit(Request $request)
    {
        $produk = Produk::where('id', $request->produk_id)->first();
        $qty = $produk->jumlah_perdos * $request->ket;
        $hargaSatuan = $produk->harga_jual / $produk->jumlah_perdos;
        $jumlah = $hargaSatuan * $qty;
        $jumlahDisc = $jumlah * $request->disc / 100;
        $jumlahAfterDisc = $jumlah - $jumlahDisc;

        $dataDetail = DetailPenjualan::where('produk_id', $request->produk_id)
            ->where('penjualan_id', $request->penjualan_id)->first();
        if ($dataDetail != null) {
            return (new GeneralResponse)->default_json(false, "Barang sudah ada!", null, 422);
        }

        $data = new DetailPenjualan();
        $data->penjualan_id = $request->penjualan_id;
        $data->produk_id = $request->produk_id;
        $data->qty = $qty;
        // $data->harga_satuan = $hargaSatuan;
        $data->ket = "$request->ket Dos";
        $data->disc = $request->disc;
        $data->jumlah = $jumlahAfterDisc;
        $data->save();

        $penjualan = Penjualan::where('id', $request->penjualan_id)->first();
        $penjualan->grand_total = $penjualan->grand_total + $jumlahAfterDisc;
        $penjualan->save();

        if ($penjualan) {
            return (new GeneralResponse)->default_json(true, "Success", $penjualan, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $penjualan, 403);
        }
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        $penjualan = Penjualan::where('id', $request->id)->first();
        $penjualan->grand_total = intval(preg_replace("/\D/", "", $request->grand_total));
        $penjualan->save();

        foreach ($request->produk_id as $key => $value) {
            $detailPenjualan = DetailPenjualan::where('penjualan_id', $request->id)
                ->where('produk_id', $value)
                ->first();

            $detailPenjualan->qty = $request->qty[$key];
            $detailPenjualan->ket = $request->ket[$key];
            $detailPenjualan->disc = $request->disc[$key];
            $detailPenjualan->jumlah = intval(preg_replace("/\D/", "", $request->jumlah[$key]));
            $detailPenjualan->save();

            $produk = Produk::where('id', $value)->first();
            $marginStok = intval(preg_replace('/([^\-0-9\.,])/i', "", $request->margin_ket[$key]));
            $produk->stok = $produk->stok - $marginStok;
            $produk->save();
        }

        $piutang = Piutang::where('penjualan_id', $request->id)->first();
        $piutang->debet = $piutang->debet + intval(preg_replace('/([^\-0-9\.,])/i', "", $request->margin_grandtotal));
        $piutang->sisa = $piutang->sisa + intval(preg_replace('/([^\-0-9\.,])/i', "", $request->margin_grandtotal));
        $piutang->save();

        if ($piutang) {
            return (new GeneralResponse)->default_json(true, "Success", null, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", null, 404);
        }
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
