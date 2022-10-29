<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request as RequestFacades;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // return RequestFacades::path();
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        return view('Pages.dashboard', compact('page_title', 'page_description'));
    }
}
