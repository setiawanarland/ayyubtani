<?php

namespace App\Http\Controllers;

use App\Http\Response\GeneralResponse;
use App\Models\DetailPembelian;
use App\Models\Pembelian;
use App\Models\DetailPembelianTemp;
use App\Models\Hutang;
use App\Models\pajak;
use App\Models\PembelianTemp;
use App\Models\Produk;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Validator;

class PembelianController extends Controller
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
        $breadcrumbs = ['Pembelian'];

        $supplier = DB::table('suppliers')
            ->select('id', 'nama_supplier',)
            ->get();

        $produk = DB::table('produks')
            ->select('id', 'nama_produk', 'kemasan')
            ->orderBy('nama_produk')
            ->orderBy('kemasan')
            ->get();

        $pajak = DB::table('pajaks')
            ->select('nama_pajak')
            ->where('active', '1')
            ->first();

        return view('pembelian.index', compact('page_title', 'page_description', 'breadcrumbs', 'supplier', 'produk', 'pajak'));
    }

    public function list()
    {
        $response = (new PembelianController)->getList();
        return $response;
    }

    public function getList()
    {
        $temp = DetailPembelianTemp::select('detail_pembelians_temp.*', 'produks.nama_produk', 'produks.kemasan', 'produks.satuan', 'produks.harga_beli')
            ->join('produks', 'detail_pembelians_temp.produk_id', 'produks.id')
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
        $produk = Produk::where('id', $request->produk_id)->first();
        $qty = $produk->qty_perdos * $request->ket;
        $hargaSatuan = $produk->harga_beli;
        $jumlah = $hargaSatuan * $qty;
        $jumlahDisc = $jumlah * $request->disc / 100;
        $jumlahAfterDisc = $jumlah - $jumlahDisc;

        // $dataDetail = DetailPembelianTemp::where('produk_id', $request->produk_id)->first();
        // if ($dataDetail != null) {
        //     return (new GeneralResponse)->default_json(false, "Barang sudah ada!", null, 422);
        // }

        $data = new DetailPembelianTemp();
        $data->produk_id = $request->produk_id;
        $data->qty = $qty;
        // $data->harga_satuan = $hargaSatuan;
        $data->ket = $request->ket;
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
        $data = DetailPembelianTemp::where('id', $id)->first();
        $data->delete();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function tempReset(Request $request)
    {
        $data = DetailPembelianTemp::truncate();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function preview(Request $request)
    {
        $data = [];

        // get supplier information
        $supplier = DB::table('suppliers')
            ->select('nama_supplier', 'alamat')
            ->where('id', $request->supplier)
            ->first();
        $data['supplier'] = $supplier;

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
        $jatuhTempo = date('d/m/Y', strtotime('+1 months', strtotime($request->tanggal_beli)));

        // set data
        $data['invoice'] = $request->invoice;
        $data['tanggal_beli'] = $request->tanggal_beli;
        $data['jatuh_tempo'] = $jatuhTempo;
        $data['dpp'] = $request->dpp;
        $data['ppn'] = $request->ppn;
        $data['total_disc'] = $request->total_disc;
        $data['grand_total'] = $request->grand_total;

        return $data;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function produkNew(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'nama_produk' => 'required',
            'kemasan' => 'required',
            'satuan' => 'required',
            'jumlah_perdos' => 'required|numeric|min:1',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['invalid' => $validator->errors()]);
        }

        $produk = new Produk();
        $produk->supplier_id = $request->supplier_id;
        $produk->nama_produk = $request->nama_produk;
        $produk->kemasan = $request->kemasan;
        $produk->satuan = $request->satuan;
        $produk->jumlah_perdos = floatval(preg_replace('/[^\d\.]+/', '', $request->jumlah_perdos));
        $produk->qty_kemasan = floatval(preg_replace('/[^\d\.]+/', '', $request->qty_kemasan));
        $produk->qty_perdos = floatval(preg_replace('/[^\d\.]+/', '', $request->qty_perdos));
        $produk->harga_beli = intval(preg_replace("/\D/", "", $request->harga_beli));
        $produk->harga_jual = intval(preg_replace("/\D/", "", $request->harga_jual));
        $produk->harga_perdos = intval(preg_replace("/\D/", "", $request->harga_perdos));
        $produk->save();
        // return $produk;

        $qty = $request->qty_perdos * $request->stok_masuk;
        $hargaSatuan = intval(preg_replace("/\D/", "", $request->harga_beli));
        $jumlah = $hargaSatuan * $qty;
        $jumlahDisc = $jumlah * intval(preg_replace("/\D/", "", $request->disc_harga)) / 100;
        $jumlahAfterDisc = $jumlah - $jumlahDisc;

        $data = new DetailPembelianTemp();
        $data->produk_id = $produk->id;
        $data->qty = $qty;
        // $data->harga_satuan = $hargaSatuan;
        $data->ket = $request->stok_masuk;
        $data->disc = $request->disc_harga;
        $data->jumlah = $jumlahAfterDisc;
        $data->save();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $dataPembelian = [];
        $dataPembelian['bulan'] = date('m', strtotime($request->tanggal_beli));
        $dataPembelian['tahun'] = date('Y', strtotime($request->tanggal_beli));

        $pembelian = new Pembelian();
        $pembelian->supplier_id = $request->supplier;
        $pembelian->invoice = $request->invoice;
        $pembelian->tanggal_beli = date('Y-m-d', strtotime($request->tanggal_beli));
        $pembelian->bulan = $dataPembelian['bulan'];
        $pembelian->tahun = $dataPembelian['tahun'];
        $pembelian->dpp = intval(preg_replace("/\D/", "", $request->dpp));
        $pembelian->ppn = intval(preg_replace("/\D/", "", $request->ppn));
        $pembelian->total_disc = intval(preg_replace("/\D/", "", $request->total_disc));
        $pembelian->grand_total = intval(preg_replace("/\D/", "", $request->grand_total));
        $pembelian->save();

        foreach ($request->produk_id as $key => $value) {
            $detailPembelian = new DetailPembelian();
            $detailPembelian->pembelian_id = $pembelian->id;
            $detailPembelian->produk_id = $value;
            $detailPembelian->qty = floatval(preg_replace('/[^\d\.]+/', '', $request->qty[$key]));
            // $detailPembelian->qty = 0;
            $detailPembelian->ket = $request->ket[$key];
            // $detailPembelian->disc = $request->disc[$key];
            $detailPembelian->disc = 0;
            // $detailPembelian->jumlah = intval(preg_replace("/\D/", "", $request->jumlah[$key]));
            $detailPembelian->jumlah = 0;
            $detailPembelian->save();

            $produk = Produk::where('id', $value)->first();
            $stok = $produk->stok;
            $qty = $produk->qty;
            $qtyMasuk = floatval(preg_replace('/[^\d\.]+/', '', $request->qty[$key]));
            $stokMasuk = intval(preg_replace("/\D/", "", $request->ket[$key]));
            $produk->qty = $qty + $qtyMasuk;
            $produk->stok = $stok + $stokMasuk;
            $produk->save();
        }

        $dataHutang = [];
        $dataHutang['bulan'] = date('m', strtotime($request->tanggal_beli));
        $dataHutang['tahun'] = date('Y', strtotime($request->tanggal_beli));

        $hutang = new Hutang();
        $hutang->pembelian_id = $pembelian->id;
        $hutang->supplier_id = $request->supplier;
        $hutang->tanggal_hutang = date('Y-m-d', strtotime($request->tanggal_beli));
        $hutang->bulan = $dataHutang['bulan'];
        $hutang->tahun = $dataHutang['tahun'];
        $hutang->ket = $request->invoice;
        $hutang->total = floatval(preg_replace("/\D/", "", $request->grand_total));
        $hutang->kredit = floatval(preg_replace("/\D/", "", 0));
        $hutang->sisa = floatval(preg_replace("/\D/", "", $request->grand_total));
        $hutang->save();

        $temp = DetailPembelianTemp::truncate();

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
        $breadcrumbs = ['Daftar Pembelian'];

        return view('pembelian.daftar', compact('page_title', 'page_description', 'breadcrumbs',));
    }

    public function listPembelian()
    {
        $response = (new PembelianController)->getListPembelian();
        return $response;
    }

    public function getListPembelian()
    {
        $data = Pembelian::select('pembelians.*', 'suppliers.nama_supplier',)
            ->join('suppliers', 'pembelians.supplier_id', 'suppliers.id')
            ->where('pembelians.tahun', session('tahun'))
            // ->orderBy('pembelians.bulan', 'ASC')
            // ->orderBy('pembelians.tahun', 'ASC')
            // ->orderBy('pembelians.id', 'DESC')
            ->orderBy('pembelians.invoice', 'DESC')
            ->orderBy('pembelians.tanggal_beli', 'DESC')
            ->get();


        if ($data) {
            return (new GeneralResponse)->default_json(true, 'success', $data, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $data = [];

        $pembelian = Pembelian::where('id', $id)->first();
        $supplier = Supplier::select('nama_supplier', 'alamat')->where('id', $pembelian->supplier_id)->first();
        $detailPembelian = DetailPembelian::where('pembelian_id', $pembelian->id)->get();

        foreach ($detailPembelian as $key => $value) {
            $produk = Produk::select('nama_produk', 'kemasan')->where('id', $value->produk_id)->first();
            $value->nama_produk = $produk->nama_produk;
            $value->kemasan_produk = $produk->kemasan;
        }

        $data['pembelian'] = $pembelian;
        $data['supplier'] = $supplier;
        $data['detailPembelian'] = $detailPembelian;

        return $data;
    }

    public function po()
    {
        $data = DB::table('detail_pembelians_temp')
            ->join('produks', 'detail_pembelians_temp.produk_id', 'produks.id')
            ->get();

        return $this->printPo($data);
    }

    public function printPo($data)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('CV AYYUB TANI')
            ->setLastModifiedBy('CV AYYUB TANI')
            ->setTitle('PO CV. AYYUB TANI')
            ->setSubject('PO CV. AYYUB TANI')
            ->setDescription('PO CV. AYYUB TANI')
            ->setKeywords('pdf php')
            ->setCategory('PO CV. AYYUB TANI');

        $sheet = $spreadsheet->getActiveSheet();
        // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
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

        $sheet->setCellValue('A1', 'CV. AYYUB TANI')->mergeCells('A1:C1');
        $sheet->setCellValue('A2', 'ALAMAT');
        $sheet->setCellValue('B2', ' : SALAMATARA KARELOE BONTORAMBA JENEPONTO')->mergeCells('B2:C2');
        $sheet->setCellValue('A3', 'NPWP');
        $sheet->setCellValue('B3', ' : 80.181.426.0-807.000')->mergeCells('B3:C3');
        $sheet->setCellValue('A4', 'NIK');
        $sheet->setCellValue('B4', ' : ')->mergeCells('B4:C4');
        $sheet->setCellValue('D1', 'JENEPONTO, ' . date('d-m-Y'))->mergeCells('D1:F1');
        $sheet->setCellValue('D2', 'Kepada Yth,')->mergeCells('D2:F2');
        $sheet->setCellValue('D3', 'PT. TIGA GENERASI MANDIRI')->mergeCells('D3:F3');
        $sheet->setCellValue('D4', 'KCP VETERAN MAKASSAR')->mergeCells('D4:F4');
        $sheet->setCellValue('A5', 'ORDERAN BARANG')->mergeCells('A5:F5');

        $sheet->setCellValue('A6', 'NO')->mergeCells('A6:A6');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->setCellValue('B6', 'NAMA PRODUK');
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->setCellValue('C6', 'KEMASAN');
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->setCellValue('D6', 'QTY');
        $sheet->getColumnDimension('D')->setWidth(13);
        $sheet->setCellValue('E6', 'ITEM');
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->setCellValue('F6', 'KET');
        $sheet->getColumnDimension('F')->setWidth(18);

        $cell = 6;

        $sheet->getStyle('A:F')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:F6')->getFont()->setBold(true);
        $sheet->getStyle('A5:F6')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A6:A' . (count($data) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B6:B' . (count($data) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('B6:B' . (count($data) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('C6:C' . (count($data) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('D6:D' . (count($data) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('E6:E' . (count($data) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('F6:F' . (count($data) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A1:A1')->getFont()->setBold(true);
        $sheet->getStyle('D3:D3')->getFont()->setBold(true);

        // return $data;
        foreach ($data as $index => $value) {

            $cell++;

            $sheet->setCellValue('A' . $cell, $index + 1);
            $sheet->setCellValue('B' . $cell, strtoupper($value->nama_produk));
            $sheet->setCellValue('C' . $cell, strtoupper($value->kemasan));
            $sheet->setCellValue('D' . $cell, $value->ket);
            $sheet->setCellValue('E' . $cell, 'DOS');
        }
        $sheet->getStyle('B' . ($cell + 3) . ':D' . ($cell + 11))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B' . ($cell + 10))->getFont()->setUnderline(true);
        $sheet->getStyle('E' . ($cell + 3) . ':F' . ($cell + 11))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('E' . ($cell + 10))->getFont()->setUnderline(true);

        $sheet->setCellValue('B' . ($cell + 3), 'PENANGGUNG JAWAB');
        $sheet->setCellValue('B' . ($cell + 10), 'H. ILYAS');
        $sheet->setCellValue('B' . ($cell + 11), 'DIREKTUR');
        $sheet->setCellValue('E' . ($cell + 3), 'DIBUAT OLEH,')->mergeCells('E' . ($cell + 3) . ':F' . ($cell + 3));
        $sheet->setCellValue('E' . ($cell + 10), '');
        $sheet->setCellValue('E' . ($cell + 11), 'ADMINISTRASI')->mergeCells('E' . ($cell + 11) . ':F' . ($cell + 11));

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A6:F' . $cell)->applyFromArray($border);


        // $spreadsheet->getActiveSheet()->getHeaderFooter()
        //     ->setOddHeader('&C&H' . url()->current());
        // $spreadsheet->getActiveSheet()->getHeaderFooter()
        //     ->setOddFooter('&L&B &RPage &P of &N');
        // $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
        // \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
        // $fileName = "PO_" . date('d-m-Y') . ".pdf";
        // header('Content-Type: application/pdf');
        // header("Content-Disposition: attachment; filename=" . urlencode($fileName));
        // header('Cache-Control: max-age=0');
        // $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');

        $fileName = "PO_" . date('d-m-Y');
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');


        $writer->save('php://output');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function edit(pembelian $pembelian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pembelian $pembelian)
    {
        //
    }

    public function destroy(Pembelian $pembelian, $id)
    {
        $pembelian = pembelian::where('id', $id)->first();
        $detailpembelian = DetailPembelian::where('pembelian_id', $pembelian->id)->get();
        $piutang = Hutang::where('pembelian_id', $pembelian->id)->first();

        foreach ($detailpembelian as $key => $value) {
            $produk = Produk::where('id', $value->produk_id)->first();

            $stokBeli = intval(preg_replace("/\D/", "", $value->ket));
            $stok = $produk->stok;
            $qtyBeli = floatval(preg_replace('/[^\d\.]+/', '', $value->qty));
            $qty = $produk->qty;
            $stokBaru = $stok - $stokBeli;
            $qtyBaru = $qty - $qtyBeli;

            $produk->stok = $stokBaru;
            $produk->qty = $qtyBaru;

            $produk->save();

            if ($produk) {
                $detail = DetailPembelian::where('id', $value->id)->first();
                $detail->delete();
            } else {
                return (new GeneralResponse)->default_json(false, "Error", $produk, 401);
            }
        }

        if ($piutang) {
            $piutang->delete();
        }
        $data = $pembelian->delete();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 200);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 401);
        }
    }
}
