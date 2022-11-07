<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request as RequestFacades;
use App\Http\Controllers\KiosController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SupplierController;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Dashboard'];

        $data = [];
        $produk = (new ProdukController)->getList();
        foreach ($produk as $key => $value) {
            $data[] = $value;
        }

        $dataProduk = count($data[1]['data']);

        $data = [];
        $kios = (new KiosController)->getList();
        foreach ($kios as $key => $value) {
            $data[] = $value;
        }

        $dataKios = count($data[1]['data']);

        return view('Pages.dashboard', compact('page_title', 'page_description', 'breadcrumbs', 'dataProduk', 'dataKios'));
    }
}
