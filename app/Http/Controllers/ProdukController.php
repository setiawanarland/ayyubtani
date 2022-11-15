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
}
