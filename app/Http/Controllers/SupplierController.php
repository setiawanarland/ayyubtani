<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Response\GeneralResponse;
use App\Models\Supplier;
use Illuminate\Support\Facades\Route;
use Validator;

class SupplierController extends Controller
{
    public function index()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Daftar Supplier'];

        return view('supplier.index', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function list()
    {
        $response = (new SupplierController)->getList();
        return $response;
    }

    public function getList()
    {
        $supplier = DB::table("suppliers")
            ->get();

        if ($supplier) {
            return (new GeneralResponse)->default_json(true, 'success', $supplier, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_supplier' => 'required',
            'alamat' => 'required',
            'npwp' => 'required',
            'nik' => 'required',
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

        $request = Request::create("/api/supplier/create", 'POST', $filtered);
        $response = Route::dispatch($request);

        return $response;
    }

    public function create(Request $request)
    {
        $data = new Supplier();
        $data->nama_supplier = $request->nama_supplier;
        $data->alamat = $request->alamat;
        $data->npwp = $request->npwp;
        $data->nik = $request->nik;
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
        $data = Supplier::where('id', $id)->first();
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
            'nama_supplier' => 'required',
            'alamat' => 'required',
            'npwp' => 'required',
            'nik' => 'required',
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

        $request = Request::create("/api/supplier/edit/$id", 'POST', $filtered);
        $response = Route::dispatch($request);

        return $response;
    }

    public function edit(Request $request, $id)
    {
        $data = Supplier::where('id', $id)->first();
        $data->nama_supplier = $request->nama_supplier;
        $data->alamat = $request->alamat;
        $data->npwp = $request->npwp;
        $data->nik = $request->nik;
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
        $data = Supplier::where('id', $id)->first();
        $data->delete();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }
}
