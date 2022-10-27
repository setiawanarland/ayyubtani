<?php

namespace App\Http\Controllers;

use App\Models\AuthModel;
use App\Http\Requests\StoreauthRequest;
use App\Http\Requests\UpdateauthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Auth;
use Validator;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        if ($request->session()->has('user')) {
            return redirect('/dashboard');
        }

        return view('Pages.login');
    }

    public function login(Request $request)
    {
        $auth = Auth::attempt($request->only('username', 'password'));

        if (!$auth) {
            return redirect()->back()->with('error', 'Pengguna atau password salah!');
        }

        $user = User::where('username', $request['username'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->access_token = $token;
        // return $user;
        session(['user' => $user]);
        session(['tahun' => date("Y")]);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/');
    }
}
