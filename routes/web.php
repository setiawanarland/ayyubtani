<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KiosController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('set-login');
Route::get('/logout', [AuthController::class, 'logout'])->name('set-logout');


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('kios')->group(function () {
        Route::get('/', [KiosController::class, 'index'])->name('kios-list');
    });

    Route::prefix('produk')->group(function () {
        Route::get('/', [ProdukController::class, 'index'])->name('produk');
        Route::get('/list', [ProdukController::class, 'list'])->name('produk-list');
        Route::post('/produk-create', [ProdukController::class, 'store'])->name('produk-store');
        Route::get('/show/{id}', [ProdukController::class, 'show'])->name('get-produk');
        Route::post('/produk-update', [ProdukController::class, 'update'])->name('produk-update');
        Route::delete('/delete/{id}', [ProdukController::class, 'delete'])->name('produk-delete');
    });
});
