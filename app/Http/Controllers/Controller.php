<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // public function __construct()
    // {
    //     session(['tahun' => date('Y')]);
    // }

    public function setTahun()
    {
        session()->forget(['tahun']);
        session(['tahun' => request('tahun', date('Y'))]);
        return redirect()->back();
    }

    public function setBulan()
    {
        session()->forget(['bulan']);
        session(['bulan' => request('bulan', date('M'))]);
        return redirect()->back();
    }
}
