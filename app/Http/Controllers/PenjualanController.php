<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Http\Requests\StorePenjualanRequest;
use App\Http\Requests\UpdatePenjualanRequest;
use App\Http\Response\GeneralResponse;
use App\Models\DetailPenjualan;
use App\Models\DetailPenjualanTemp;
use App\Models\Kios;
use App\Models\LimitPutang;
use App\Models\pajak;
use App\Models\Pembayaran;
use App\Models\Piutang;
use App\Models\Produk;
use DB;
use DOMDocument;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\ArrayToXml\ArrayToXml;
use Storage;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Penjualan'];

        $kios = DB::table('kios')
            ->select('id', 'nama_kios', 'pemilik', 'alamat', 'kabupaten', 'nik', 'npwp')
            ->get();

        $produk = DB::table('produks')
            ->where('active', '1')
            ->orderBy('nama_produk', 'ASC')
            ->orderBy('kemasan', 'ASC')
            ->get();

        foreach ($produk as $key => $value) {
            $satuanKemasan = ($value->satuan == "ltr") ? "Btl" : "Bks";
            $ketKemasan = $value->jumlah_perdos;
            $qtyKemasan = $value->qty_kemasan;
            $qtyTotal = $value->qty;
            $ketTotal = round($qtyTotal / $qtyKemasan);
            $ketLeft = $ketTotal % $ketKemasan;
            $stok = ($ketLeft > 0) ? "" . ($ketTotal - $ketLeft) / $ketKemasan . " Dos " . $ketLeft . " " . $satuanKemasan . "" : "" . ($ketTotal - $ketLeft) / $ketKemasan . " Dos";
            $value->stok = $stok;
        }

        $pajak = DB::table('pajaks')
            ->select('satuan_pajak')
            ->where('active', '1')
            ->first();


        $pembayaran = DB::table('pembayarans')->get();

        $lastPenjualan = Penjualan::where('tahun', session('tahun'))->get();
        // $lastPenjualan = Penjualan::max('invoice');

        $invoice = "AT" . substr(session('tahun'), -2) . "-" . sprintf("%05s", count($lastPenjualan) + 1);
        // $invoice = "V" . substr(session('tahun'), -2) . "-" . sprintf("%05s", count($lastPenjualan) + 1);
        // $invoice = $lastPenjualan + 1;

        return view('penjualan.index', compact('page_title', 'page_description', 'breadcrumbs', 'kios', 'produk', 'pajak', 'pembayaran', 'invoice'));
    }

    public function list()
    {
        $response = (new PenjualanController)->getList();
        return $response;
    }

    public function getList()
    {
        $temp = DetailPenjualanTemp::select('detail_penjualan_temps.*', 'produks.nama_produk', 'produks.kemasan', 'produks.satuan', 'produks.harga_jual', 'harga_perdos')
            ->join('produks', 'detail_penjualan_temps.produk_id', 'produks.id')
            ->get();

        $pajak = pajak::select('nama_pajak', 'satuan_pajak')
            ->where('active', '1')
            ->first();

        foreach ($temp as $key => $value) {
            $value->satuan_pajak = $pajak->satuan_pajak;
        }

        if ($temp) {
            return (new GeneralResponse)->default_json(true, 'success', $temp, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    public function temp(Request $request)
    {
        if (($request->harga_lama != $request->harga_satuan)) {
            $produk = Produk::where('id', $request->produk_id)->first();
            $produk->harga_jual = floatval(preg_replace('/[^\d\.]+/', '', $request->harga_satuan));
            $produk->harga_perdos = floatval(preg_replace('/[^\d\.]+/', '', $request->harga_perdos));
            $produk->save();
        }

        $produk = Produk::where('id', $request->produk_id)->first();
        $qty = $produk->qty_perdos * $request->ket;
        // $hargaSatuan = $produk->harga_jual;
        // $jumlah = $produk->harga_perdos * $request->ket;
        $jumlah = $produk->harga_perdos;
        // harga dpp dari ppn 10%
        // $dpp = 100 / 110 * $jumlah;
        // $dpp = $jumlah / 1.11;
        // harga dpp dari ppn 11%
        // $dpp = 100 / 110 * $jumlah;
        // $dpp = $jumlah / 1.11;
        $dpp = $jumlah;
        $jumlahDisc = $request->disc;
        $jumlahAfterDisc = ($dpp - $jumlahDisc) * $request->ket;
        // $jumlahAfterDisc = ($jumlah - $jumlahDisc) * $request->ket;
        // return "$jumlah, $jumlahDisc, $jumlahAfterDisc";

        $dataDetail = DetailPenjualanTemp::where('produk_id', $request->produk_id)->first();
        // if ($dataDetail != null) {
        //     return (new GeneralResponse)->default_json(false, "Barang sudah ada!", null, 422);
        // }
        $data = new DetailPenjualanTemp();
        $data->produk_id = $request->produk_id;
        $data->qty = $qty;
        // $data->harga_satuan = $hargaSatuan;
        $data->ket = $request->ket;
        $data->ket_kemasan = $request->ketKemasan;
        $data->disc = $request->disc;
        $data->jumlah = $jumlahAfterDisc;
        $data->save();
        // return $data;

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 403);
        }
    }

    public function tempDelete(Request $request, $id)
    {
        $data = DetailPenjualanTemp::where('id', $id)->first();
        $data->delete();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function tempReset(Request $request)
    {
        $data = DetailPenjualanTemp::truncate();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function preview(Request $request)
    {
        $data = [];

        // get kios information
        $kios = DB::table('kios')
            ->where('id', $request->kios)
            ->first();
        $data['kios'] = $kios;

        // get status pembayaran
        $pembayaran = DB::table('pembayarans')->where('id', $request->pembayaran)->first();

        // get produks information
        $produks = [];
        foreach ($request->produk_id as $key => $value) {
            $produks[] = DB::table('produks')
                ->where('id', $value)
                ->first();
        }

        // add qty, ket, jumlah by produk
        foreach ($produks as $key => $value) {
            $value->qty = $request->qty[$key];
            $value->ket = $request->ket[$key];
            $value->disc = $request->disc[$key];
            $value->jumlah = $request->jumlah[$key];
        }
        $data['produks'] = $produks;

        // set jatuhTempo 
        $jatuhTempo = ($request->pembayaran == 1) ? date('d/m/Y', strtotime('+1 months', strtotime($request->tanggal_jual))) : date('d/m/Y', strtotime($request->tanggal_jual));

        // set data
        $data['invoice'] = $request->invoice;
        $data['tanggal_jual'] = $request->tanggal_jual;
        $data['pembayaran'] = $pembayaran->nama_pembayaran;
        $data['jatuh_tempo'] = $jatuhTempo;
        $data['dpp'] = $request->dpp;
        $data['ppn'] = $request->ppn;
        $data['total_disc'] = $request->total_disc;
        $data['grand_total'] = $request->grand_total;

        return $data;
    }

    public function getProduk(Request $request, $id)
    {
        $produk = Produk::where('id', $id)->first();

        return (new GeneralResponse)->default_json(true, "success", $produk, 200);
    }

    public function getStok(Request $request, $id)
    {
        $produk = Produk::where('id', $id)->first();

        if ($produk->qty == 0) {
            return (new GeneralResponse)->default_json(false, "Stok kosong!", $produk, 401);
        }

        if ($produk->qty < $request->qty) {
            return (new GeneralResponse)->default_json(false, "Stok tersisa {$produk->stok}!", $produk, 401);
        }
    }

    public function getLimitPiutang(Request $request, $id)
    {
        $data = [];
        $grand_total = floatval(preg_replace('/[^\d\.]+/', '', $request->grandTotal));

        if ($request->pembayaran == 1) {
            $penjualan = Penjualan::where('kios_id', $id)
                ->where('status', 1)
                ->sum('grand_total');

            $limit = LimitPutang::first();

            $data['total_hutang'] = $penjualan;
            $data['tambahan_hutang'] = $grand_total;

            if (($penjualan + $grand_total) > $limit->limit) {
                return (new GeneralResponse)->default_json(false, "Limit terpenuhi", $data, 403);
            } else {
                return "no";
            }
        }
    }

    public function randomKios($namaPemilik)
    {
        $listKios = ['sangkala dg. nyonri', 'iwan', 'mansur', 'solle s', 'dg. beta p', 'hafid dg. naba'];
        $newlistKios = $listKios;
        if (($key = array_search($namaPemilik, $newlistKios)) !== false) {
            unset($newlistKios[$key]);
        }
        return $newlistKios[array_rand($newlistKios)];
    }


    public function store(Request $request)
    {
        // return $request->all();
        $dataPenjualan = [];
        $dataPenjualan['bulan'] = date('m', strtotime($request->tanggal_jual));
        $dataPenjualan['tahun'] = date('Y', strtotime($request->tanggal_jual));

        // if ($request->kios == 111) {

        //     $kios = Kios::where('id', $request->kios)->first();

        //     $randomKios = $this->randomKios($kios->pemilik);

        //     $kios->pemilik = $randomKios;
        //     $kios->save();
        // }

        $penjualan = new Penjualan();
        $penjualan->kios_id = $request->kios;
        $penjualan->pembayaran_id = $request->pembayaran;
        $penjualan->invoice = $request->invoice;
        $penjualan->tanggal_jual = date('Y-m-d', strtotime($request->tanggal_jual));
        $penjualan->bulan = $dataPenjualan['bulan'];
        $penjualan->tahun = $dataPenjualan['tahun'];
        $penjualan->dpp = floatval(preg_replace('/[^\d\.]+/', '', $request->dpp));
        $penjualan->ppn = floatval(preg_replace('/[^\d\.]+/', '', $request->ppn));
        $penjualan->grand_total = floatval(preg_replace('/[^\d\.]+/', '', $request->grand_total));
        // $penjualan->dpp = intval(preg_replace("/\D/", "", $request->dpp));
        // $penjualan->ppn = intval(preg_replace("/\D/", "", $request->ppn));
        // $penjualan->grand_total = intval(preg_replace("/\D/", "", $request->grand_total));
        $penjualan->total_disc = floatval(preg_replace('/[^\d\.]+/', '', $request->total_disc));
        $penjualan->status = $request->pembayaran;
        $penjualan->save();

        foreach ($request->produk_id as $key => $value) {
            $detailPenjualan = new DetailPenjualan();
            $detailPenjualan->penjualan_id = $penjualan->id;
            $detailPenjualan->produk_id = $value;
            $detailPenjualan->qty = floatval(preg_replace('/[^\d\.]+/', '', $request->qty[$key]));
            $detailPenjualan->ket = $request->ket[$key];
            $detailPenjualan->disc = floatval(preg_replace('/[^\d\.]+/', '', $request->disc[$key]));
            $detailPenjualan->jumlah = floatval(preg_replace('/[^\d\.]+/', '', $request->jumlah[$key]));
            $detailPenjualan->save();

            $produk = Produk::where('id', $value)->first();
            $stok = $produk->stok;
            $qty = $produk->qty;
            $jumlahPerdos = $produk->jumlah_perdos;
            $stokkeluar = intval(preg_replace("/\D/", "", $request->ket[$key]));
            $qtykeluar = floatval(preg_replace('/[^\d\.]+/', '', $request->qty[$key]));
            $produk->stok = $stok - $stokkeluar;
            $produk->qty = $qty - $qtykeluar;
            $produk->save();
        }

        $dataPiutang = [];
        $dataPiutang['bulan'] = date('m', strtotime($request->tanggal_jual));
        $dataPiutang['tahun'] = date('Y', strtotime($request->tanggal_jual));

        if ($request->pembayaran == 1) {
            $piutang = new Piutang();
            $piutang->penjualan_id = $penjualan->id;
            $piutang->kios_id = $request->kios;
            $piutang->tanggal_piutang = date('Y-m-d', strtotime($request->tanggal_jual));
            $piutang->bulan = $dataPiutang['bulan'];
            $piutang->tahun = $dataPiutang['tahun'];
            $piutang->invoice = $request->invoice;
            $piutang->total = floatval(preg_replace('/[^\d\.]+/', '', $request->grand_total));
            $piutang->kredit = floatval(preg_replace('/[^\d\.]+/', '', 0));
            $piutang->sisa = floatval(preg_replace('/[^\d\.]+/', '', $request->grand_total));
            $piutang->save();
        }

        $temp = DetailPenjualanTemp::truncate();

        if ($temp) {
            return (new GeneralResponse)->default_json(true, "Success", null, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", null, 404);
        }
    }

    public function daftar()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Daftar Penjualan'];

        $totalPenjualan = DB::table('penjualans')
            ->where('bulan',  (int) session('bulan'))
            ->where('tahun',  session('tahun'))
            ->sum('grand_total');

        return view('penjualan.daftar', compact('page_title', 'page_description', 'breadcrumbs', 'totalPenjualan'));
    }

    public function listPenjualan()
    {
        $response = (new PenjualanController)->getListPenjualan();
        return $response;
    }

    public function getListPenjualan()
    {
        $data = Penjualan::select('penjualans.*', 'kios.nama_kios', 'kios.pemilik', 'kios.kabupaten')
            ->join('kios', 'penjualans.kios_id', 'kios.id')
            ->where('penjualans.tahun', session('tahun'))
            ->orderBy('penjualans.tanggal_jual', 'DESC')
            // ->orderBy('penjualans.id', 'DESC')
            ->orderBy('penjualans.invoice', 'DESC')
            ->get();


        if ($data) {
            return (new GeneralResponse)->default_json(true, 'success', $data, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    public function show(Request $request, $id)
    {
        $data = [];

        $penjualan = Penjualan::where('id', $id)->first();
        $kios = Kios::select('pemilik', 'nama_kios', 'alamat', 'kabupaten', 'npwp', 'nik')->where('id', $penjualan->kios_id)->first();
        $detailPenjualan = DetailPenjualan::where('penjualan_id', $penjualan->id)->get();

        foreach ($detailPenjualan as $key => $value) {
            $produk = Produk::select('nama_produk', 'kemasan')->where('id', $value->produk_id)->first();
            $value->nama_produk = $produk->nama_produk;
            $value->kemasan_produk = $produk->kemasan;
            $hargaSatuan = ($value->jumlah / intval(preg_replace("/\D/", "", $value->ket))) / 1.11;
            $value->dppLain = 11 / 12 * ($value->jumlah / 1.11);
            $value->harga_jual = $hargaSatuan;
        }

        $data['penjualan'] = $penjualan;
        $data['kios'] = $kios;
        $data['detailPenjualan'] = $detailPenjualan;

        return $data;
    }

    public function edit(Request $request, $id)
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Penjualan'];

        $penjualan = Penjualan::with('detailPenjualan', 'kios')
            ->where('penjualans.id', $id)
            ->first();

        $kios = DB::table('kios')
            ->select('id', 'nama_kios',)
            ->get();

        $produk = DB::table('produks')
            ->select('id', 'nama_produk', 'kemasan', 'stok')
            ->get();

        $pajak = DB::table('pajaks')
            ->select('nama_pajak')
            ->where('active', '1')
            ->first();

        // return $penjualan;
        return view('penjualan.edit', compact('page_title', 'page_description', 'breadcrumbs', 'kios', 'produk', 'pajak', 'penjualan'));
    }

    public function print(Request $request)
    {
        $penjualan = Penjualan::where('id', $request->id)
            ->get();

        foreach ($penjualan as $key => $value) {
            $jatuhTempo = ($value->status == 1) ? date('d/m/Y', strtotime('+1 months', strtotime($value->tanggal_jual))) : date('d/m/Y', strtotime($value->tanggal_jual));
            $statusPembayaran = Pembayaran::where('id', $value->status)->first();
            $kios = Kios::where('id', $value->kios_id)->first();
            $detailPenjualan = DetailPenjualan::where('penjualan_id', $value->id)->get();

            foreach ($detailPenjualan as $index => $val) {
                $produk = Produk::where('id', $val->produk_id)->first();
                $val->nama_produk = $produk->nama_produk;
                $val->kemasan = $produk->kemasan;
                // $hargaSatuan = ("Btl" && str_contains($val->ket, "Btl")) ? $val->jumlah / intval(preg_replace("/\D/", "", $val->ket)) : ($val->jumlah / $produk->jumlah_perdos) / intval(preg_replace("/\D/", "", $val->ket));
                $hargaSatuan = $val->jumlah / intval(preg_replace("/\D/", "", $val->ket));
                $val->harga_jual = $hargaSatuan;
                // $val->dpp = 100 / 110 * $produk->harga_perdos;
                $val->dpp = $produk->harga_perdos / 1.11;
                $val->satuan = $produk->satuan;
            }
            $value->jatuh_tempo = $jatuhTempo;
            $value->pembayaran = $statusPembayaran->nama_pembayaran;
            $value->kios = $kios;
            $value->detail_penjualan = $detailPenjualan;
        }

        return $penjualan;
    }

    public function test(Request $request)
    {
        $detailPenjualan = DetailPenjualan::select('id', 'jumlah')->whereBetween('penjualan_id', [1772, 1816])->get();
        foreach ($detailPenjualan as $key => $value) {
            $data = DetailPenjualan::where('id', $value->id)
                ->first();
            $jumlah = $data->jumlah;
            $jumlah = substr($jumlah, -3);
            if ($jumlah != "0.0") {
                $data->jumlah = str_replace("1.0", "0.0", $data->jumlah);
                $data->save();
            }
            // $data->jumlah = round(($data->jumlah * 10 / 100) + $data->jumlah);
            // $data->save();
        }
        return $detailPenjualan;
    }

    public function rekapPo(Request $request)
    {
        // return $request->all();
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('CV AYYUB TANI')
            ->setLastModifiedBy('CV AYYUB TANI')
            ->setTitle('rekap po yang sudah diantar')
            ->setSubject('rekap po yang sudah diantar')
            ->setDescription('rekap po yang sudah diantar')
            ->setKeywords('pdf php')
            ->setCategory('rekap po yang sudah diantar');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $sheet->getRowDimension(1)->setRowHeight(17);
        $sheet->getRowDimension(2)->setRowHeight(17);
        $sheet->getRowDimension(3)->setRowHeight(7);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        //Margin PDF
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $sheet->setCellValue('A1', 'REKAP PO YANG SUDAH DIANTAR')->mergeCells('A1:F1');
        $sheet->setCellValue('A2', 'CV. AYYUB TANI')->mergeCells('A2:F2');

        $sheet->setCellValue('A5', 'No')->mergeCells('A5:A5');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->setCellValue('B5', 'Nama Produk');
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->setCellValue('C5', 'Kemasan');
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->setCellValue('D5', 'Isi Perdos');
        $sheet->getColumnDimension('D')->setWidth(8);
        $sheet->setCellValue('E5', 'Harga Satuan');
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->setCellValue('F5', 'Harga Perdos');
        $sheet->getColumnDimension('F')->setWidth(20);

        $cell = 5;

        $sheet->getStyle('A1:A3')->getFont()->setSize(12);
        $sheet->getStyle('A:F')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:F5')->getFont()->setBold(true);
        $sheet->getStyle('A5:F5')->getAlignment()->setVertical('center')->setHorizontal('center');
        // $sheet->getStyle('A5:A' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        // $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        // $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        // $sheet->getStyle('C5:C' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        // $sheet->getStyle('D5:D' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        // $sheet->getStyle('E5:E' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        // $sheet->getStyle('F5:F' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        // $sheet->getStyle('G5:G5')->getAlignment()->setVertical('center')->setHorizontal('center');
        // $sheet->getStyle('G5:G' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        // $sheet->getStyle('E6:F' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('right');
        // $sheet->getStyle('E6:F' . (count($data['produks']) + $cell))->getNumberFormat()->setFormatCode('#,##0');
        // $sheet->getStyle('H6:F' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('right');
        // $sheet->getStyle('H6:F' . (count($data['produks']) + $cell))->getNumberFormat()->setFormatCode('#,##0');
        // $sheet->getStyle('A1:A3')->getAlignment()->setVertical('center')->setHorizontal('center');

        // $merge = 0;
        // $prevValue = '';
        // $number = 0;

        // foreach ($request->data as $index => $value) {
        //     if (strtolower($prevValue) !== strtolower($value->nama_produk)) {
        //         $prevValue = $value->nama_produk;
        //         $merge = $cell + $value->merge;
        //         $number++;
        //     }
        //     $cell++;

        //     $sheet->setCellValue('A' . $cell, $number)->mergeCells('A' . $cell . ':A' . $merge);
        //     $sheet->setCellValue('B' . $cell, strtoupper($value->nama_produk))->mergeCells('B' . $cell . ':B' . $merge);
        //     $sheet->setCellValue('C' . $cell, strtoupper($value->kemasan));
        //     $sheet->setCellValue('D' . $cell, $value->jumlah_perdos);
        //     $sheet->setCellValue('E' . $cell, $value->harga_jual);
        //     $sheet->setCellValue('F' . $cell, $value->harga_perdos);
        // }

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A5:F' . $cell)->applyFromArray($border);

        // Untuk download 
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Daftar Harga Produk CV. AYYUB TANI.xlsx"');


        $writer->save('php://output');
    }

    public function listEditPenjualan(Request $request)
    {
        $response = (new PenjualanController)->getListEditPenjualan($request->id);
        return $response;
    }

    public function getListEditPenjualan($id)
    {
        $data = DetailPenjualan::select('detail_penjualans.*', 'produks.nama_produk', 'produks.kemasan', 'produks.satuan', 'produks.harga_jual', 'harga_perdos', 'produks.jumlah_perdos')
            ->join('produks', 'detail_penjualans.produk_id', 'produks.id')
            ->where('detail_penjualans.penjualan_id', $id)
            ->get();

        $pajak = pajak::select('nama_pajak', 'satuan_pajak')
            ->where('active', '1')
            ->first();

        foreach ($data as $key => $value) {
            $value->satuan_pajak = $pajak->satuan_pajak;
        }

        if ($data) {
            return (new GeneralResponse)->default_json(true, 'success', $data, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    public function addEdit(Request $request)
    {
        $produk = Produk::where('id', $request->produk_id)->first();
        $qty = $produk->jumlah_perdos * $request->ket;
        $hargaSatuan = $produk->harga_jual / $produk->jumlah_perdos;
        $jumlah = $hargaSatuan * $qty;
        $jumlahDisc = $jumlah * $request->disc / 100;
        $jumlahAfterDisc = $jumlah - $jumlahDisc;

        $dataDetail = DetailPenjualan::where('produk_id', $request->produk_id)
            ->where('penjualan_id', $request->penjualan_id)->first();
        if ($dataDetail != null) {
            return (new GeneralResponse)->default_json(false, "Produk sudah ada!", null, 422);
        }

        $data = new DetailPenjualan();
        $data->penjualan_id = $request->penjualan_id;
        $data->produk_id = $request->produk_id;
        $data->qty = $qty;
        // $data->harga_satuan = $hargaSatuan;
        $data->ket = "$request->ket Dos";
        $data->disc = $request->disc;
        $data->jumlah = $jumlahAfterDisc;
        $data->save();

        $penjualan = Penjualan::where('id', $request->penjualan_id)->first();
        $penjualan->grand_total = $penjualan->grand_total + $jumlahAfterDisc;
        $penjualan->save();

        if ($penjualan) {
            return (new GeneralResponse)->default_json(true, "Success", $penjualan, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $penjualan, 403);
        }
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        $penjualan = Penjualan::where('id', $request->id)->first();
        $penjualan->kios_id = $request->kios;
        $penjualan->tanggal_jual = date('Y-m-d', strtotime($request->tanggal_jual));
        $penjualan['bulan'] = date('m', strtotime($request->tanggal_jual));
        $penjualan['tahun'] = date('Y', strtotime($request->tanggal_jual));
        $penjualan->invoice = $request->invoice;
        // return $penjualan;
        // $penjualan->grand_total = intval(preg_replace("/\D/", "", $request->grand_total));
        $penjualan->save();

        // foreach ($request->produk_id as $key => $value) {
        //     $detailPenjualan = DetailPenjualan::where('penjualan_id', $request->id)
        //         ->where('produk_id', $value)
        //         ->first();

        //     $detailPenjualan->qty = $request->qty[$key];
        //     $detailPenjualan->ket = $request->ket[$key];
        //     $detailPenjualan->disc = floatval(preg_replace('/[^\d\.]+/', '', $request->disc[$key]));
        //     $detailPenjualan->jumlah = intval(preg_replace("/\D/", "", $request->jumlah[$key]));
        //     $detailPenjualan->save();

        //     $produk = Produk::where('id', $value)->first();
        //     $marginStok = intval(preg_replace('/([^\-0-9\.,])/i', "", $request->margin_ket[$key]));
        //     $produk->stok = $produk->stok - $marginStok;
        //     $produk->save();
        // }

        $piutang = Piutang::where('penjualan_id', $request->id)->first();
        $piutang->tanggal_piutang = date('Y-m-d', strtotime($request->tanggal_jual));
        $piutang['bulan'] = date('m', strtotime($request->tanggal_jual));
        $piutang['tahun'] = date('Y', strtotime($request->tanggal_jual));
        // return $piutang;
        // $piutang->debet = $piutang->debet + intval(preg_replace('/([^\-0-9\.,])/i', "", $request->margin_grandtotal));
        // $piutang->sisa = $piutang->sisa + intval(preg_replace('/([^\-0-9\.,])/i', "", $request->margin_grandtotal));
        $piutang->save();

        if ($piutang) {
            return (new GeneralResponse)->default_json(true, "Success", null, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", null, 404);
        }
    }

    public function destroy(Penjualan $penjualan, $id)
    {
        $penjualan = Penjualan::where('id', $id)->first();
        $detailPenjualan = DetailPenjualan::where('penjualan_id', $penjualan->id)->get();
        $piutang = Piutang::where('penjualan_id', $penjualan->id)->first();

        foreach ($detailPenjualan as $key => $value) {
            $produk = Produk::where('id', $value->produk_id)->first();

            $stokJual = intval(preg_replace("/\D/", "", $value->ket));
            $stok = $produk->stok;
            $qtyJual = floatval(preg_replace('/[^\d\.]+/', '', $value->qty));
            $qty = $produk->qty;
            $stokBaru = $stokJual + $stok;
            $qtyBaru = $qtyJual + $qty;

            $produk->stok = $stokBaru;
            $produk->qty = $qtyBaru;

            $produk->save();

            if ($produk) {
                $detail = DetailPenjualan::where('id', $value->id)->first();
                $detail->delete();
            } else {
                return (new GeneralResponse)->default_json(false, "Error", $produk, 401);
            }
        }

        if ($piutang) {
            $piutang->delete();
        }
        $data = $penjualan->delete();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 200);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 401);
        }
    }

    public function export(Request $request)
    {
        $data = Penjualan::select('penjualans.id', 'tanggal_jual', 'invoice', 'kios.npwp', 'kios.nik', 'kios.pemilik', 'kios.kabupaten')
            ->join('kios', 'penjualans.kios_id', 'kios.id')
            // ->where('tahun', session('tahun'))
            // ->where('bulan', (int)session('bulan'))
            ->whereIn('penjualans.id', explode(',', $request->data))
            // ->where('penjualans.id', 5683)
            // ->whereBetween('penjualans.id', [5654, 5703])
            ->orderBy('tanggal_jual')
            ->orderBy('invoice')
            ->get();

        // $inputFileName = Storage::disk('partitionE')->files('at\pajak\template xml coretax\Converter Faktur 20250122\TemplateExcel\FPK 1.xlsx');
        // return $contents = Storage::disk('partitionE')->get($inputFileName);
        /** Load $inputFileName to a Spreadsheet Object  **/
        // return $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($contents);

        return $this->spreadsheet($data, $request->jenis);
    }


    public function spreadsheet($data, $jenis)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('CV AYYUB TANI')
            ->setLastModifiedBy('CV AYYUB TANI')
            ->setTitle('Export hasil penjualan')
            ->setSubject('Export hasil penjualan')
            ->setDescription('Export hasil penjualan')
            ->setKeywords('pdf php excel')
            ->setCategory('Export hasil penjualan');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);

        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);

        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $sheet->setCellValue('A1', 'No');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->setCellValue('B1', 'Tanggal Faktur');
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->setCellValue('C1', 'Jenis Faktur');
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->setCellValue('D1', 'Kode Transaksi');
        $sheet->getColumnDimension('D')->setWidth(5);
        $sheet->setCellValue('E1', 'Ket. Tambahan');
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->setCellValue('F1', 'Dok. Pendukung');
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->setCellValue('G1', 'Invoice');
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->setCellValue('H1', 'Cap Fasilitas');
        $sheet->getColumnDimension('H')->setWidth(5);
        $sheet->setCellValue('I1', 'IDTKU Penjual');
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->setCellValue('J1', 'NPWP Pembeli');
        $sheet->getColumnDimension('J')->setWidth(25);
        $sheet->setCellValue('K1', 'Jenis ID Pembeli');
        $sheet->getColumnDimension('K')->setWidth(8);
        $sheet->setCellValue('L1', 'Negara Pembeli');
        $sheet->getColumnDimension('L')->setWidth(8);
        $sheet->setCellValue('M1', 'NIK Pembeli');
        $sheet->getColumnDimension('M')->setWidth(25);
        $sheet->setCellValue('N1', 'Nama Pembeli');
        $sheet->getColumnDimension('N')->setWidth(25);
        $sheet->setCellValue('O1', 'Alamat Pembeli');
        $sheet->getColumnDimension('O')->setWidth(25);
        $sheet->setCellValue('P1', 'Email Pembeli');
        $sheet->getColumnDimension('P')->setWidth(5);
        $sheet->setCellValue('Q1', 'IDTKU Pembeli');
        $sheet->getColumnDimension('Q')->setWidth(5);

        // sheet 2
        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Detail');
        $spreadsheet->addSheet($myWorkSheet, 1);
        $sheet2 = $spreadsheet->setActiveSheetIndex(1);

        $sheet2->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet2->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);

        $sheet2->setCellValue('A1', 'No');
        $sheet2->getColumnDimension('A')->setWidth(6);
        $sheet2->setCellValue('B1', 'Barang/Jasa');
        $sheet2->getColumnDimension('B')->setWidth(6);
        $sheet2->setCellValue('C1', 'Kode');
        $sheet2->getColumnDimension('C')->setWidth(10);
        $sheet2->setCellValue('D1', 'Nama Barang');
        $sheet2->getColumnDimension('D')->setWidth(35);
        $sheet2->setCellValue('E1', 'Satuan');
        $sheet2->getColumnDimension('E')->setWidth(10);
        $sheet2->setCellValue('F1', 'Harga');
        $sheet2->getColumnDimension('F')->setWidth(20);
        $sheet2->setCellValue('G1', 'Jumlah');
        $sheet2->getColumnDimension('G')->setWidth(6);
        $sheet2->setCellValue('H1', 'Diskon');
        $sheet2->getColumnDimension('H')->setWidth(5);
        $sheet2->setCellValue('I1', 'DPP');
        $sheet2->getColumnDimension('I')->setWidth(20);
        $sheet2->setCellValue('J1', 'DPP Lain');
        $sheet2->getColumnDimension('J')->setWidth(20);
        $sheet2->setCellValue('K1', 'Tarif PPN');
        $sheet2->getColumnDimension('K')->setWidth(6);
        $sheet2->setCellValue('L1', 'PPN');
        $sheet2->getColumnDimension('L')->setWidth(20);
        $sheet2->setCellValue('M1', 'Tarif PPnBm');
        $sheet2->getColumnDimension('M')->setWidth(6);
        $sheet2->setCellValue('N1', 'PPnBm');
        $sheet2->getColumnDimension('N')->setWidth(20);

        $cell = 1;
        $cell2 = 1;
        foreach ($data as $index => $value) {
            $cell++;

            $sheet->setCellValue('A' . $cell, $cell - 1);
            $sheet->setCellValue('B' . $cell, date("m/d/Y", strtotime($value->tanggal_jual)));
            $sheet->setCellValue('C' . $cell, "Normal");
            $sheet->setCellValue('D' . $cell, "04");
            $sheet->setCellValue('E' . $cell, "");
            $sheet->setCellValue('F' . $cell, "");
            $sheet->setCellValue('G' . $cell, $value->invoice);
            $sheet->setCellValue('H' . $cell, "");
            $sheet->setCellValue('I' . $cell, "0801814260807000000000");
            $sheet->setCellValue('J' . $cell, "" . preg_replace("/\D/", "", $value->npwp) . "");
            $sheet->setCellValue('K' . $cell, "National ID");
            $sheet->setCellValue('L' . $cell, "IDN");
            $sheet->setCellValueExplicit('M' . $cell, $value->nik, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('N' . $cell, strtoupper($value->pemilik));
            $sheet->setCellValue('O' . $cell, strtoupper($value->kabupaten));
            $sheet->setCellValue('P' . $cell, "");
            $sheet->setCellValue('Q' . $cell, "000000");

            $detailPenjualan = DetailPenjualan::where('penjualan_id', $value->id)->get();
            foreach ($detailPenjualan as $key => $val) {
                $cell2++;
                $produk = Produk::select('nama_produk', 'kemasan', 'jumlah_perdos', 'qty_perdos')->where('id', $val->produk_id)->first();
                $val->nama_produk = strtoupper($produk->nama_produk) . " " . strtoupper($produk->kemasan);
                $val->jumlah_perdos = $produk->jumlah_perdos;
                $val->total = preg_replace("/\D/", "", $val->ket);
                $val->kode = ($val->qty % $produk->qty_perdos == 0) ? "UM.0022" : "UM.0021";
                $val->harga_satuan = (($val->jumlah / intval(preg_replace("/\D/", "", $val->ket))) / 1.11);
                $val->dpp = $val->total * $val->harga_satuan;
                $val->dppLain = 11 / 12 * ($val->jumlah / 1.11);
                $val->ppn = $val->dppLain * 12 / 100;

                $sheet2->setCellValue('A' . $cell2, $cell - 1);
                $sheet2->setCellValue('B' . $cell2, "A");
                $sheet2->setCellValueExplicit('C' . $cell2, "000000", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet2->setCellValue('D' . $cell2, $val->nama_produk);
                $sheet2->setCellValue('E' . $cell2, $val->kode);
                $sheet2->setCellValue('F' . $cell2, $val->harga_satuan);
                $sheet2->setCellValue('G' . $cell2, $val->total);
                $sheet2->setCellValue('H' . $cell2, 0.0);
                $sheet2->setCellValue('I' . $cell2, $val->dpp);
                $sheet2->setCellValue('J' . $cell2, $val->dppLain);
                $sheet2->setCellValue('K' . $cell2, 12);
                $sheet2->setCellValue('L' . $cell2, $val->ppn);
                $sheet2->setCellValue('M' . $cell2, 0);
                $sheet2->setCellValue('N' . $cell2, 0);
            }
        }
        // return $detailPenjualan;

        $sheet->getStyle('A1:Q' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A:Q')->getAlignment()->setWrapText(true);

        $sheet2->getStyle('A1:N' . $cell2)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet2->getStyle('A:N')->getAlignment()->setWrapText(true);

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:Q' . $cell)->applyFromArray($border);
        $sheet2->getStyle('A1:N' . $cell2)->applyFromArray($border);

        if ($jenis == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Rekapitulasi Laporan Penjualan CV. AYYUB TANI.xlsx"');
        } else {
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddHeader('&C&H' . url()->current());
            $spreadsheet->getActiveSheet()->getHeaderFooter()
                ->setOddFooter('&L&B &RPage &P of &N');
            $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
            \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
            header('Content-Type: application/pdf');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
        }

        $writer->save('php://output');
    }
}
