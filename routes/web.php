<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HutangController;
use App\Http\Controllers\KiosController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PajakController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SettingController;
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
Route::get('set-tahun', [Controller::class, 'setTahun'])->name('set-tahun');
Route::get('set-bulan', [Controller::class, 'setBulan'])->name('set-bulan');

Route::get('/produk/test', [ProdukController::class, 'test'])->name('test');


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
        Route::get('/cetak', [ProdukController::class, 'cetak'])->name('cetak-produk');
        Route::post('/rekap-tahunan', [ProdukController::class, 'rekapTahunan'])->name('rekap-tahunan');
    });

    Route::prefix('kios')->group(function () {
        Route::get('/', [KiosController::class, 'index'])->name('kios');
        Route::get('/list', [KiosController::class, 'list'])->name('kios-list');
        Route::post('/kios-create', [KiosController::class, 'store'])->name('kios-store');
        Route::get('/show/{id}', [KiosController::class, 'show'])->name('get-kios');
        Route::post('/kios-update', [KiosController::class, 'update'])->name('kios-update');
        Route::delete('/delete/{id}', [KiosController::class, 'delete'])->name('kios-delete');
    });

    Route::prefix('pembelian')->group(function () {
        Route::get('/', [PembelianController::class, 'index'])->name('pembelian');
        Route::get('/list', [PembelianController::class, 'list'])->name('pembeliantemp-list');
        Route::post('/temp', [PembelianController::class, 'temp'])->name('temp');
        Route::delete('/tempdelete/{id}', [PembelianController::class, 'tempDelete'])->name('temp-delete');
        Route::delete('/tempreset', [PembelianController::class, 'tempReset'])->name('temp-reset');
        Route::post('/preview', [PembelianController::class, 'preview'])->name('pembelian-preview');
        Route::post('/produk-new', [PembelianController::class, 'produkNew'])->name('pembelian-produk-new');
        Route::post('/store', [PembelianController::class, 'store'])->name('pembelian-store');
        Route::get('/daftar', [PembelianController::class, 'daftar'])->name('daftar-pembelian');
        Route::get('/list-pembelian', [PembelianController::class, 'listPembelian'])->name('pembelian-list');
        Route::get('/show/{id}', [PembelianController::class, 'show'])->name('pembelian-show');
        Route::get('/po', [PembelianController::class, 'po'])->name('po');
        Route::delete('/delete/{id}', [PembelianController::class, 'destroy'])->name('pembelian-delete');
    });

    Route::prefix('hutang')->group(function () {
        Route::get('/', [HutangController::class, 'index'])->name('hutang');
        Route::get('/list', [HutangController::class, 'list'])->name('hutang-list');
    });

    Route::prefix('penjualan')->group(function () {
        Route::get('/', [PenjualanController::class, 'index'])->name('penjualan');
        Route::get('/list', [PenjualanController::class, 'list'])->name('penjualantemp-list');
        Route::post('/temp', [PenjualanController::class, 'temp'])->name('temp');
        Route::delete('/tempdelete/{id}', [PenjualanController::class, 'tempDelete'])->name('temp-delete');
        Route::delete('/tempreset', [PenjualanController::class, 'tempReset'])->name('temp-reset');
        Route::post('/preview', [PenjualanController::class, 'preview'])->name('penjualan-preview');
        Route::post('/rekap-po', [PenjualanController::class, 'rekapPo'])->name('rekap-po');
        Route::get('/print', [PenjualanController::class, 'print'])->name('penjualan-print');
        Route::get('/get-produk/{produkId}', [PenjualanController::class, 'getProduk'])->name('get-produk');
        Route::get('/get-stok/{produkId}', [PenjualanController::class, 'getStok'])->name('get-stok');
        Route::get('/get-limit-piutang/{kiosId}', [PenjualanController::class, 'getLimitPiutang'])->name('get-limit-piutang');
        Route::post('/store', [PenjualanController::class, 'store'])->name('penjualan-store');
        Route::get('/daftar', [PenjualanController::class, 'daftar'])->name('daftar-penjualan');
        Route::get('/list-penjualan', [PenjualanController::class, 'listpenjualan'])->name('penjualan-list');
        Route::get('/show/{id}', [PenjualanController::class, 'show'])->name('penjualan-show');
        Route::get('/edit/{id}', [PenjualanController::class, 'edit'])->name('penjualan-edit');
        Route::get('/list-penjualan-edit', [PenjualanController::class, 'listEditPenjualan'])->name('penjualan-edit-list');
        Route::post('/add-edit', [PenjualanController::class, 'addEdit'])->name('add-edit');
        Route::post('/update', [PenjualanController::class, 'update'])->name('penjualan-update');
        Route::delete('/delete/{id}', [PenjualanController::class, 'destroy'])->name('penjualan-delete');
        Route::get('/test', [PenjualanController::class, 'test'])->name('test');
        Route::get('/export', [PenjualanController::class, 'export'])->name('export');
    });

    Route::prefix('piutang')->group(function () {
        Route::get('/', [PiutangController::class, 'index'])->name('piutang');
        Route::get('/list', [PiutangController::class, 'list'])->name('piutang-list');
        Route::get('/detail-piutang/{kiosId}', [PiutangController::class, 'detailPiutang'])->name('detail-piutang');
        Route::post('/bayar-piutang', [PiutangController::class, 'bayarPiutang'])->name('bayar-piutang');
    });

    Route::prefix('laporan')->group(function () {
        Route::get('/stok', [LaporanController::class, 'stok'])->name('laporan-stok');
        Route::get('/stok-list', [LaporanController::class, 'list'])->name('stok-list');
        Route::get('/stok-rekap', [LaporanController::class, 'rekap'])->name('stok-rekap');
        Route::get('/produk-jual', [LaporanController::class, 'produkJual'])->name('laporan-produk');
        Route::get('/produk-jual-laporan', [LaporanController::class, 'listProdukJual'])->name('laporan-produk-jual');
        Route::get('/produk-jual-rekap', [LaporanController::class, 'rekapProdukJual'])->name('produk-rekap-jual');
        Route::get('/produk-beli', [LaporanController::class, 'produkBeli'])->name('laporan-produk');
        Route::get('/produk-beli-laporan', [LaporanController::class, 'listProdukBeli'])->name('laporan-produk-beli');
        Route::get('/produk-beli-rekap', [LaporanController::class, 'rekapProdukBeli'])->name('produk-rekap-beli');
        Route::get('/penjualan', [LaporanController::class, 'penjualan'])->name('laporan-penjualan');
        Route::get('/penjualan-laporan', [LaporanController::class, 'listPenjualan'])->name('laporan-penjualan');
        Route::get('/penjualan-rekap', [LaporanController::class, 'rekapPenjualan'])->name('penjualan-rekap');
        Route::get('/pembelian', [LaporanController::class, 'pembelian'])->name('laporan-pembelian');
        Route::get('/pembelian-laporan', [LaporanController::class, 'listPembelian'])->name('laporan-pembelian');
        Route::get('/pembelian-rekap', [LaporanController::class, 'rekapPembelian'])->name('pembelian-rekap');
        Route::get('/pembayaran-piutang', [LaporanController::class, 'pembayaranPiutang'])->name('pembayaran-piutang');
        Route::get('/pembayaran-piutang-list', [LaporanController::class, 'listPembayaranPiutang'])->name('pembayaran-piutang-list');
    });

    Route::prefix('pajak')->group(function () {
        Route::get('/', [PajakController::class, 'index'])->name('pajak');
        Route::get('/list', [PajakController::class, 'list'])->name('pajak-list');
        Route::post('/pajak-create', [PajakController::class, 'store'])->name('pajak-store');
        Route::get('/show/{id}', [PajakController::class, 'show'])->name('get-pajak');
        Route::post('/pajak-update', [PajakController::class, 'update'])->name('pajak-update');
        Route::delete('/delete/{id}', [PajakController::class, 'delete'])->name('pajak-delete');
        Route::post('/active/{id}', [PajakController::class, 'active'])->name('pajak-active');
    });

    Route::prefix('setting')->group(function () {
        Route::get('/tambah-stok', [SettingController::class, 'tambahStok'])->name('tambah-stok');
        Route::get('/list-tambah-stok', [SettingController::class, 'listTambahStok'])->name('list-tambah-stok');
        Route::post('/process-tambah', [SettingController::class, 'processTambah'])->name('process-tambah');
        Route::get('/kurang-stok', [SettingController::class, 'kurangStok'])->name('kurang-stok');
        Route::get('/list-kurang-stok', [SettingController::class, 'listKurangStok'])->name('list-kurang-stok');
        Route::post('/process-kurang', [SettingController::class, 'processKurang'])->name('process-kurang');
        Route::get('/produk/{id}', [SettingController::class, 'getProduk'])->name('get-produk');
    });
});
