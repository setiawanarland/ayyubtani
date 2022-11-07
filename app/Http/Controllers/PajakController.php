<?php

namespace App\Http\Controllers;

use App\Models\pajak;
use App\Http\Requests\StorepajakRequest;
use App\Http\Requests\UpdatepajakRequest;
use App\Http\Response\GeneralResponse;
use Validator;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PajakController extends Controller
{
    public function index()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Daftar Pajak'];

        return view('pajak.index', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function list()
    {
        $response = (new PajakController)->getList();
        return $response;
    }

    public function getList()
    {
        $produk = DB::table("pajaks")
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
            'nama_pajak' => 'required',
            'satuan_pajak' => 'required',
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

        $request = Request::create("/api/pajak/create", 'POST', $filtered);
        $response = Route::dispatch($request);

        return $response;
    }

    public function create(Request $request)
    {
        $data = new pajak();
        $data->nama_pajak = $request->nama_pajak;
        $data->satuan_pajak = $request->satuan_pajak;
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
        $data = pajak::where('id', $id)->first();
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
            'nama_pajak' => 'required',
            'satuan_pajak' => 'required',
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

        $request = Request::create("/api/pajak/edit/$id", 'POST', $filtered);
        $response = Route::dispatch($request);

        return $response;
    }

    public function edit(Request $request, $id)
    {
        $data = pajak::where('id', $id)->first();
        $data->nama_pajak = $request->nama_pajak;
        $data->satuan_pajak = $request->satuan_pajak;
        $data->save();
        // return $data;

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 403);
        }
    }

    public function active(Request $request)
    {
        $id = request('id');

        $pajak = pajak::get();
        foreach ($pajak as $key => $value) {
            $pajakActive = pajak::where('id', $value->id)->first();
            $pajakActive->active = '0';
            $pajakActive->save();
        }
        $data = pajak::where('id', $id)->first();
        $data->active = ($data->active == '0') ? '1' : '0';

        $data->save();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function delete(Request $request, $id)
    {
        $data = pajak::where('id', $id)->first();
        $data->delete();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }
}
