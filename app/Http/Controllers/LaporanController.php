<?php

namespace App\Http\Controllers;

use App\Http\Response\GeneralResponse;
use DB;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function stok()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Laporan Stok'];

        return view('laporan.stok', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function list(Request $request)
    {
        $bulan = request('bulan');
        $data = '';
        $stokBeli = 0;
        $stokJual = 0;

        $produks = DB::table('produks')
            ->where('stok', '!=', 0)
            ->get();

        $pembelian = DB::table("pembelians")
            ->join('detail_pembelians', 'pembelians.id', 'detail_pembelians.pembelian_id')
            ->join('produks', 'detail_pembelians.produk_id', 'produks.id')
            ->where('tahun', session('tahun'))
            ->whereMonth('tanggal_beli', "$bulan")
            ->orderBy('nama_produk', 'ASC')
            ->get();

        foreach ($produks as $key => $value) {
            $stokBeli = 0;
            $stokJual = 0;

            $pembelian = DB::table('detail_pembelians')
                ->join('pembelians', 'detail_pembelians.pembelian_id', 'pembelians.id')
                ->where('tahun', session('tahun'))
                ->whereMonth('tanggal_beli', "$bulan")
                ->where('produk_id', $value->id)
                // ->where('produk_id', 168)
                ->get();

            $penjualan = DB::table('detail_penjualans')
                ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                ->where('tahun', session('tahun'))
                ->whereMonth('tanggal_jual', "$bulan")
                ->where('produk_id', $value->id)
                // ->where('produk_id', 168)
                ->get();
            // return $pembelian;

            foreach ($pembelian as $index => $val) {
                $stokBeli += intval(preg_replace("/\D/", "", $val->ket));
            }

            foreach ($penjualan as $index => $val) {
                $stokJual += intval(preg_replace("/\D/", "", $val->ket));
            }

            $value->pembelian = $stokBeli;
            $value->penjualan = $stokJual;
        }

        $data = DB::table("pembelians")
            ->join('detail_pembelians', 'pembelians.id', 'detail_pembelians.pembelian_id')
            ->join('produks', 'detail_pembelians.produk_id', 'produks.id')
            ->where('tahun', session('tahun'))
            ->whereMonth('tanggal_beli', "$bulan")
            ->orderBy('nama_produk', 'ASC')
            ->get();

        $produk = DB::table("pembelians")
            ->join('detail_pembelians', 'pembelians.id', 'detail_pembelians.pembelian_id')
            ->join('produks', 'detail_pembelians.produk_id', 'produks.id')
            ->orderBy('nama_produk', 'ASC')
            ->get();

        if ($produks) {
            return (new GeneralResponse)->default_json(true, 'success', $produks, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }
}
