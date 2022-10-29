<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KiosController extends Controller
{
    public function index()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Daftar Kios'];

        return view('kios.index', compact('page_title', 'page_description', 'breadcrumbs'));
    }
}
