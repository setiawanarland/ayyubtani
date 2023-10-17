<?php

namespace App\Http\Controllers;

use App\Http\Response\GeneralResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
        $validator = $request->validate([
            'username' => 'required',
            'password' => 'required|string|min:8',
        ]);

        $request = Request::create("/api/login", 'POST', [
            'username' => $request->username,
            'password' => $request->password
        ]);

        // return Auth::user()->username;
        $response = Route::dispatch($request);

        if ($response->status() == 200) {
            $data = response()->json($response);
            session(['user' => $data->original]);
            session(['tahun' => date("Y")]);
            session(['bulan' => date('F', mktime(0, 0, 0, date("m"), 10))]);

            return redirect('/dashboard');
        } else {
            return redirect()->back()->with('error', $response->original['message']);
        }
    }

    public function setLogin(Request $request)
    {
        $auth = Auth::attempt($request->only('username', 'password'));

        if (!$auth) {
            return (new GeneralResponse)->default_json(false, 'Pengguna atau password salah!', null, 401);
        }

        $user = User::where('username', $request['username'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        if ($token !== '') {
            return response()->json([
                'message' => 'Hi ' . $user->username . ', Berhasil Login',
                'access_token' => $token,
                'user' => $user,
                'token_type' => 'Bearer',
            ]);
        } else {
            return response()->json([
                'message' => 'Gagal Login',
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/');
    }
}
