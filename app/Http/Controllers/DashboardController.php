<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request as RequestFacades;
use App\Http\Controllers\KiosController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SupplierController;
use App\Models\Kios;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Produk;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Dashboard'];

        $dataProduk = count(Produk::get());
        $dataKios = count(Kios::get());
        $dataPembelian = count(Pembelian::where('tahun', session('tahun'))->get());
        $dataPenjualan = count(Penjualan::where('tahun', session('tahun'))->get());

        return view('Pages.dashboard', compact('page_title', 'page_description', 'breadcrumbs', 'dataProduk', 'dataKios', 'dataPembelian', 'dataPenjualan'));
    }
}
