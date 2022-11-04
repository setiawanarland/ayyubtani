<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KiosController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SupplierController;
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

    Route::prefix('supplier')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('supplier');
        Route::get('/list', [SupplierController::class, 'list'])->name('supplier-list');
        Route::post('/supplier-create', [SupplierController::class, 'store'])->name('supplier-store');
        Route::get('/show/{id}', [SupplierController::class, 'show'])->name('get-supplier');
        Route::post('/supplier-update', [SupplierController::class, 'update'])->name('supplier-update');
        Route::delete('/delete/{id}', [SupplierController::class, 'delete'])->name('supplier-delete');
    });

    Route::prefix('produk')->group(function () {
        Route::get('/', [ProdukController::class, 'index'])->name('produk');
        Route::get('/list', [ProdukController::class, 'list'])->name('produk-list');
        Route::post('/produk-create', [ProdukController::class, 'store'])->name('produk-store');
        Route::get('/show/{id}', [ProdukController::class, 'show'])->name('get-produk');
        Route::post('/produk-update', [ProdukController::class, 'update'])->name('produk-update');
        Route::delete('/delete/{id}', [ProdukController::class, 'delete'])->name('produk-delete');
    });

    Route::prefix('kios')->group(function () {
        Route::get('/', [KiosController::class, 'index'])->name('kios');
        Route::get('/list', [KiosController::class, 'list'])->name('kios-list');
        Route::post('/kios-create', [KiosController::class, 'store'])->name('kios-store');
        Route::get('/show/{id}', [KiosController::class, 'show'])->name('get-kios');
        Route::post('/kios-update', [KiosController::class, 'update'])->name('kios-update');
        Route::delete('/delete/{id}', [KiosController::class, 'delete'])->name('kios-delete');
    });
});
