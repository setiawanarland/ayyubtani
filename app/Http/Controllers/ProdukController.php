<?php

namespace App\Http\Controllers;

use App\Http\Response\GeneralResponse;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use DB;
use Validator;

class ProdukController extends Controller
{
    public function index()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Daftar Produk'];

        return view('produk.index', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function list()
    {
        $response = (new ProdukController)->getList();
        return $response;
    }

    public function getList()
    {
        $produk = DB::table("produks")
            ->orderBy('nama_produk', 'ASC')
            ->get();

        if ($produk) {
            return (new GeneralResponse)->default_json(true, 'success', $produk, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required',
            'kemasan' => 'required',
            'satuan' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['invalid' => $validator->errors()]);
        }

        $data = $request->all();

        $filtered = array_filter(
            $data,
            function ($key) {
                if (!in_array($key, ['_token', 'id'])) {
                    return $key;
                };
            },
            ARRAY_FILTER_USE_KEY
        );

        $request = Request::create("/api/produk/create", 'POST', $filtered);
        $response = Route::dispatch($request);

        return $response;
    }

    public function create(Request $request)
    {
        $data = new Produk();
        $data->nama_produk = $request->nama_produk;
        $data->kemasan = $request->kemasan;
        $data->satuan = $request->satuan;
        $data->jumlah_perdos = intval($request->jumlah_perdos);
        $data->harga_beli = intval(preg_replace("/\D/", "", $request->harga_beli));
        $data->harga_jual = intval(preg_replace("/\D/", "", $request->harga_jual));
        $data->harga_perdos = intval(preg_replace("/\D/", "", $request->harga_perdos));
        $data->save();
        // return $data;

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 403);
        }
    }

    public function show(Request $request, $id)
    {
        $data = Produk::where('id', $id)->first();
        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function update(Request $request)
    {
        $id = request('id');
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required',
            'kemasan' => 'required',
            'satuan' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['invalid' => $validator->errors()]);
        }

        $data = $request->all();

        $filtered = array_filter(
            $data,
            function ($key) {
                if (!in_array($key, ['_token', 'id'])) {
                    return $key;
                };
            },
            ARRAY_FILTER_USE_KEY
        );

        $request = Request::create("/api/produk/edit/$id", 'POST', $filtered);
        $response = Route::dispatch($request);

        return $response;
    }

    public function edit(Request $request, $id)
    {
        $data = Produk::where('id', $id)->first();
        $data->nama_produk = $request->nama_produk;
        $data->kemasan = $request->kemasan;
        $data->satuan = $request->satuan;
        $data->jumlah_perdos = intval($request->jumlah_perdos);
        $data->harga_beli = intval(preg_replace("/\D/", "", $request->harga_beli));
        $data->harga_jual = intval(preg_replace("/\D/", "", $request->harga_jual));
        $data->harga_perdos = intval(preg_replace("/\D/", "", $request->harga_perdos));
        $data->save();
        // return $data;

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 403);
        }
    }

    public function delete(Request $request, $id)
    {
        $data = Produk::where('id', $id)->first();
        $data->delete();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function test()
    {
        // $hasil = [];
        // $data = [];
        // $produk = Produk::where('satuan', 'btl')->get();
        // foreach ($produk as $key => $value) {
        //     $data[] = explode(' ', $value->kemasan);
        // }

        // foreach ($data as $key => $value) {
        //     // return $value[0];
        //     $hasil[] = ($value[0] * $value[3]) / 1000;
        // }

        // foreach ($produk as $key => $value) {
        //     // return "ok";
        //     $baru = $hasil[$key];
        //     $dataProduk = Produk::where('id', $value->id)->first();
        //     $dataProduk->satuan = "ltr";
        //     $dataProduk->qty_perdos = $baru;
        //     $dataProduk->save();
        // }


        // return $produk;

        $data = [
            49066200.0,
            54661200.0,
            41023350.0,
            88377600.0,
            54988850.0,
            84531800.0,
            67209400.0,
            35685600.0,
            36611600.0,
            103831000.0,
            48191000.0,
            14448850.0,
            20428850.0,
            24191850.0,
            21628200.0,
            83323600.0,
            39352500.0,
            83323600.0,
            38282500.0,
            86715600.0,
            83323600.0,
            46762500.0,
            34890500.0,
            250352000.0,
            197453200.0,
            201460000.0,
            131640000.0,
            5350000.0,
            19705400.0,
            38558500.0,
            3630000.0,
            2000000.0,
            1015200.0,
            5740000.0,
            6520000.0,
            27595200.0,
            9549200.0,
            11889200.0,
            18913200.0,
            3209200.0,
            3048000.0,
            1460000.0,
            19605000.0,
            1180000.0,
            3148000.0,
            3260000.0,
            2870000.0,
            32600000.0,
            6937200.0,
            4940000.0,
            14640000.0,
            10498000.0,
            5814000.0,
            3494000.0,
            3260000.0,
            1148000.0,
            1944000.0,
            1944000.0,
            15700000.0,
            1435000.0,
            3260000.0,
            13040000.0,
            1303800.0,
            5900000.0,
            12690600.0,
            11043200.0,
            4305000.0,
            7300400.0,
            3780000.0,
            17160000.0,
            7668000.0,
            16518000.0,
            9914600.0,
            17657000.0,
            6328800.0,
            9934400.0,
            59952200.0,
            10280000.0,
        ];

        $result = [];
        $dpp = 0;

        foreach ($data as $key => $value) {
            $dpp = round($value / 1.11, 1);
            $result['dpp'][] = $dpp;
            $result['ppn'][] = round($value - $dpp, 1);
        }

        return $result;
    }
}
