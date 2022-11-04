<?php

use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KiosController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function (Request $request) {
    return "ok api";
});
Route::post('/login', [AuthController::class, 'setLogin']);
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('produk')->group(function () {
        Route::get('/list', [ProdukController::class, 'getList'])->name('get-list');
        Route::post('/create', [ProdukController::class, 'create'])->name('produk-create');
        Route::post('/edit/{id}', [ProdukController::class, 'edit'])->name('produk-edit');
    });

    Route::prefix('supplier')->group(function () {
        Route::get('/list', [SupplierController::class, 'getList'])->name('get-list');
        Route::post('/create', [SupplierController::class, 'create'])->name('supplier-create');
        Route::post('/edit/{id}', [SupplierController::class, 'edit'])->name('supplier-edit');
    });

    Route::prefix('kios')->group(function () {
        Route::get('/list', [KiosController::class, 'getList'])->name('get-list');
        Route::post('/create', [KiosController::class, 'create'])->name('kios-create');
        Route::post('/edit/{id}', [KiosController::class, 'edit'])->name('kios-edit');
    });
});
