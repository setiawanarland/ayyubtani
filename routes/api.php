<?php

use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdukController;
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
});
