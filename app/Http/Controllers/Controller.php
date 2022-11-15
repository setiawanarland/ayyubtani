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
}
