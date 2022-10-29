<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request as RequestFacades;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Dashboard'];

        return view('Pages.dashboard', compact('page_title', 'page_description', 'breadcrumbs'));
    }
}
