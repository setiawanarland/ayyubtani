<?php

namespace App\Http\Controllers;

use App\Http\Response\GeneralResponse;
use App\Models\KurangStok;
use App\Models\Produk;
use App\Models\TambahStok;
use DB;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function tambahStok()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Tambah Stok'];

        $produk = DB::table('produks')
            ->select('id', 'nama_produk', 'kemasan', 'stok', 'harga_jual', 'harga_perdos')
            ->get();

        return view('setting.tambah_stok', compact('page_title', 'page_description', 'breadcrumbs', 'produk',));
    }

    public function listTambahStok()
    {
        $response = (new SettingController)->getListTambahStok();
        return $response;
    }

    public function getListTambahStok()
    {
        $data = DB::table("tambah_stoks")
            ->select('tambah_stoks.*', 'produks.nama_produk', 'produks.kemasan')
            ->join('produks', 'tambah_stoks.produk_id', 'produks.id')
            ->orderBy('created_at', 'ASC')
            ->get();

        if ($data) {
            return (new GeneralResponse)->default_json(true, 'success', $data, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    public function processTambah(Request $request)
    {
        $produk = Produk::where('id', $request->produk)->first();

        $stokAfter = $produk->stok + $request->stok_tambah;
        $qtyAfter = $stokAfter * $produk->qty_perdos;

        $produk->stok = $stokAfter;
        $produk->qty = $qtyAfter;
        $produk->save();

        if ($produk) {
            $data = new TambahStok();
            $data->produk_id = $produk->id;
            $data->jumlah = $request->stok_tambah;
            $data->alasan = $request->ket;
            $data->save();
        }

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 200);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function kurangStok()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Kurang Stok'];

        $produk = DB::table('produks')
            ->select('id', 'nama_produk', 'kemasan', 'stok', 'harga_jual', 'harga_perdos')
            ->get();

        return view('setting.kurang_stok', compact('page_title', 'page_description', 'breadcrumbs', 'produk',));
    }

    public function listKurangStok()
    {
        $response = (new SettingController)->getListKurangStok();
        return $response;
    }

    public function getListKurangStok()
    {
        $data = DB::table("kurang_stoks")
            ->select('kurang_stoks.*', 'produks.nama_produk', 'produks.kemasan')
            ->join('produks', 'kurang_stoks.produk_id', 'produks.id')
            ->orderBy('created_at', 'ASC')
            ->get();

        if ($data) {
            return (new GeneralResponse)->default_json(true, 'success', $data, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    public function processKurang(Request $request)
    {
        $produk = Produk::where('id', $request->produk)->first();

        $stokAfter = $produk->stok - $request->stok_kurang;
        $qtyAfter = $stokAfter * $produk->qty_perdos;

        $produk->stok = $stokAfter;
        $produk->qty = $qtyAfter;
        // return $produk;
        $produk->save();

        if ($produk) {
            $data = new KurangStok();
            $data->produk_id = $produk->id;
            $data->jumlah = $request->stok_kurang;
            $data->alasan = $request->ket;
            $data->save();
        }

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 200);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function getProduk($id)
    {
        $data = Produk::where('id', $id)->first();
        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 200);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }
}
