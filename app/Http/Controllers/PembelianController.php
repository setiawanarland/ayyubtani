<?php

namespace App\Http\Controllers;

use App\Http\Response\GeneralResponse;
use App\Models\DetailPembelian;
use App\Models\Pembelian;
use App\Models\DetailPembelianTemp;
use App\Models\Hutang;
use App\Models\pajak;
use App\Models\PembelianTemp;
use App\Models\Produk;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Validator;

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

        $pajak = DB::table('pajaks')
            ->select('nama_pajak')
            ->where('active', '1')
            ->first();

        return view('pembelian.index', compact('page_title', 'page_description', 'breadcrumbs', 'supplier', 'produk', 'pajak'));
    }

    public function list()
    {
        $response = (new PembelianController)->getList();
        return $response;
    }

    public function getList()
    {
        $temp = DetailPembelianTemp::select('detail_pembelians_temp.*', 'produks.nama_produk', 'produks.kemasan', 'produks.satuan', 'produks.harga_beli')
            ->join('produks', 'detail_pembelians_temp.produk_id', 'produks.id')
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
        $qty = $produk->qty_perdos * $request->ket;
        $hargaSatuan = $produk->harga_beli;
        $jumlah = $hargaSatuan * $qty;
        $jumlahDisc = $jumlah * $request->disc / 100;
        $jumlahAfterDisc = $jumlah - $jumlahDisc;

        // $dataDetail = DetailPembelianTemp::where('produk_id', $request->produk_id)->first();
        // if ($dataDetail != null) {
        //     return (new GeneralResponse)->default_json(false, "Barang sudah ada!", null, 422);
        // }

        $data = new DetailPembelianTemp();
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
        $data = DetailPembelianTemp::where('id', $id)->first();
        $data->delete();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function tempReset(Request $request)
    {
        $data = DetailPembelianTemp::truncate();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function preview(Request $request)
    {
        $data = [];

        // get supplier information
        $supplier = DB::table('suppliers')
            ->select('nama_supplier', 'alamat')
            ->where('id', $request->supplier)
            ->first();
        $data['supplier'] = $supplier;

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
        $jatuhTempo = date('d/m/Y', strtotime('+1 months', strtotime($request->tanggal_beli)));

        // set data
        $data['invoice'] = $request->invoice;
        $data['tanggal_beli'] = $request->tanggal_beli;
        $data['jatuh_tempo'] = $jatuhTempo;
        $data['dpp'] = $request->dpp;
        $data['ppn'] = $request->ppn;
        $data['total_disc'] = $request->total_disc;
        $data['grand_total'] = $request->grand_total;

        return $data;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function produkNew(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required',
            'kemasan' => 'required',
            'satuan' => 'required',
            'jumlah_perdos' => 'required|numeric|min:1',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['invalid' => $validator->errors()]);
        }

        $produk = new Produk();
        $produk->nama_produk = $request->nama_produk;
        $produk->kemasan = $request->kemasan;
        $produk->satuan = $request->satuan;
        $produk->jumlah_perdos = floatval(preg_replace('/[^\d\.]+/', '', $request->jumlah_perdos));
        $produk->harga_beli = intval(preg_replace("/\D/", "", $request->harga_beli));
        $produk->harga_jual = intval(preg_replace("/\D/", "", $request->harga_jual));
        $produk->harga_perdos = intval(preg_replace("/\D/", "", $request->harga_perdos));
        $produk->save();
        // return $produk;

        $qty = $request->jumlah_perdos * $request->stok_masuk;
        $hargaSatuan = intval(preg_replace("/\D/", "", $request->harga_beli));
        $jumlah = $hargaSatuan * $qty;
        $jumlahDisc = $jumlah * intval(preg_replace("/\D/", "", $request->disc_harga)) / 100;
        $jumlahAfterDisc = $jumlah - $jumlahDisc;

        $data = new DetailPembelianTemp();
        $data->produk_id = $produk->id;
        $data->qty = $qty;
        // $data->harga_satuan = $hargaSatuan;
        $data->ket = $request->stok_masuk;
        $data->disc = $request->disc_harga;
        $data->jumlah = $jumlahAfterDisc;
        $data->save();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $dataPembelian = [];
        $dataPembelian['bulan'] = date('m', strtotime($request->tanggal_beli));
        $dataPembelian['tahun'] = date('Y', strtotime($request->tanggal_beli));

        $pembelian = new Pembelian();
        $pembelian->supplier_id = $request->supplier;
        $pembelian->invoice = $request->invoice;
        $pembelian->tanggal_beli = date('Y-m-d', strtotime($request->tanggal_beli));
        $pembelian->bulan = $dataPembelian['bulan'];
        $pembelian->tahun = $dataPembelian['tahun'];
        $pembelian->dpp = intval(preg_replace("/\D/", "", $request->dpp));
        $pembelian->ppn = intval(preg_replace("/\D/", "", $request->ppn));
        $pembelian->total_disc = intval(preg_replace("/\D/", "", $request->total_disc));
        $pembelian->grand_total = intval(preg_replace("/\D/", "", $request->grand_total));
        $pembelian->save();

        foreach ($request->produk_id as $key => $value) {
            $detailPembelian = new DetailPembelian();
            $detailPembelian->pembelian_id = $pembelian->id;
            $detailPembelian->produk_id = $value;
            $detailPembelian->qty = floatval(preg_replace('/[^\d\.]+/', '', $request->qty[$key]));
            // $detailPembelian->qty = 0;
            $detailPembelian->ket = $request->ket[$key];
            // $detailPembelian->disc = $request->disc[$key];
            $detailPembelian->disc = 0;
            // $detailPembelian->jumlah = intval(preg_replace("/\D/", "", $request->jumlah[$key]));
            $detailPembelian->jumlah = 0;
            $detailPembelian->save();

            $produk = Produk::where('id', $value)->first();
            $stok = $produk->stok;
            $qty = $produk->qty;
            $qtyMasuk = floatval(preg_replace('/[^\d\.]+/', '', $request->qty[$key]));
            $stokMasuk = intval(preg_replace("/\D/", "", $request->ket[$key]));
            $produk->qty = $qty + $qtyMasuk;
            $produk->stok = $stok + $stokMasuk;
            $produk->save();
        }

        $dataHutang = [];
        $dataHutang['bulan'] = date('m', strtotime($request->tanggal_beli));
        $dataHutang['tahun'] = date('Y', strtotime($request->tanggal_beli));

        $hutang = new Hutang();
        $hutang->pembelian_id = $pembelian->id;
        $hutang->bulan = $dataHutang['bulan'];
        $hutang->tahun = $dataHutang['tahun'];
        $hutang->ket = '';
        $hutang->debet = intval(preg_replace("/\D/", "", $request->grand_total));
        $hutang->kredit = 0;
        $hutang->sisa = intval(preg_replace("/\D/", "", $request->grand_total)) - $hutang->kredit;
        $hutang->save();

        $temp = DetailPembelianTemp::truncate();

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
        $breadcrumbs = ['Daftar Pembelian'];

        return view('pembelian.daftar', compact('page_title', 'page_description', 'breadcrumbs',));
    }

    public function listPembelian()
    {
        $response = (new PembelianController)->getListPembelian();
        return $response;
    }

    public function getListPembelian()
    {
        $data = Pembelian::select('pembelians.*', 'suppliers.nama_supplier',)
            ->join('suppliers', 'pembelians.supplier_id', 'suppliers.id')
            ->where('pembelians.tahun', session('tahun'))
            ->orderBy('pembelians.bulan', 'ASC')
            ->orderBy('pembelians.tahun', 'ASC')
            ->orderBy('pembelians.id', 'ASC')
            ->orderBy('pembelians.tanggal_beli', 'ASC')
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
     * @param  \App\Models\pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $data = [];

        $pembelian = Pembelian::where('id', $id)->first();
        $supplier = Supplier::select('nama_supplier', 'alamat')->where('id', $pembelian->supplier_id)->first();
        $detailPembelian = DetailPembelian::where('pembelian_id', $pembelian->id)->get();

        foreach ($detailPembelian as $key => $value) {
            $produk = Produk::select('nama_produk', 'kemasan')->where('id', $value->produk_id)->first();
            $value->nama_produk = $produk->nama_produk;
            $value->kemasan_produk = $produk->kemasan;
        }

        $data['pembelian'] = $pembelian;
        $data['supplier'] = $supplier;
        $data['detailPembelian'] = $detailPembelian;

        return $data;
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
