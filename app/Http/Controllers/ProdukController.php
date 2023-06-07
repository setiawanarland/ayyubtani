<?php

namespace App\Http\Controllers;

use App\Http\Response\GeneralResponse;
use App\Models\Hutang;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Piutang;
use App\Models\Produk;
use App\Models\StokBulanan as ModelsStokBulanan;
use App\Models\StokTahunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use StokBulanan;
use Validator;

setlocale(LC_ALL, 'IND');

class ProdukController extends Controller
{
    public function index()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Daftar Produk'];

        return view('produk.index', compact('page_title', 'page_description', 'breadcrumbs',));
    }

    public function list()
    {
        $response = (new ProdukController)->getList();
        return $response;
    }

    public function getList()
    {
        $produk = DB::table("produks")
            ->orderBy('nama_produk', 'ASC')
            ->get();

        if ($produk) {
            return (new GeneralResponse)->default_json(true, 'success', $produk, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required',
            'kemasan' => 'required',
            'satuan' => 'required',
            'qty_kemasan' => 'required',
            'qty_perdos' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['invalid' => $validator->errors()]);
        }

        $data = $request->all();

        $filtered = array_filter(
            $data,
            function ($key) {
                if (!in_array($key, ['_token', 'id'])) {
                    return $key;
                };
            },
            ARRAY_FILTER_USE_KEY
        );

        $request = Request::create("/api/produk/create", 'POST', $filtered);
        $response = Route::dispatch($request);

        return $response;
    }

    public function create(Request $request)
    {
        // return floatval($request->qty_kemasan);
        $data = new Produk();
        $data->nama_produk = $request->nama_produk;
        $data->kemasan = $request->kemasan;
        $data->satuan = $request->satuan;
        $data->jumlah_perdos = intval($request->jumlah_perdos);
        $data->qty_kemasan = floatval($request->qty_kemasan);
        $data->qty_perdos = floatval($request->qty_perdos);
        $data->harga_beli = intval(preg_replace("/\D/", "", $request->harga_beli));
        $data->harga_jual = intval(preg_replace("/\D/", "", $request->harga_jual));
        $data->harga_perdos = intval(preg_replace("/\D/", "", $request->harga_perdos));
        $data->save();
        // return $data;

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 403);
        }
    }

    public function show(Request $request, $id)
    {
        $data = Produk::where('id', $id)->first();
        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function update(Request $request)
    {
        $id = request('id');
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required',
            'kemasan' => 'required',
            'satuan' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['invalid' => $validator->errors()]);
        }

        $data = $request->all();

        $filtered = array_filter(
            $data,
            function ($key) {
                if (!in_array($key, ['_token', 'id'])) {
                    return $key;
                };
            },
            ARRAY_FILTER_USE_KEY
        );

        $request = Request::create("/api/produk/edit/$id", 'POST', $filtered);
        $response = Route::dispatch($request);

        return $response;
    }

    public function edit(Request $request, $id)
    {
        $data = Produk::where('id', $id)->first();
        $data->nama_produk = $request->nama_produk;
        $data->kemasan = $request->kemasan;
        $data->satuan = $request->satuan;
        $data->jumlah_perdos = intval($request->jumlah_perdos);
        $data->harga_beli = intval(preg_replace("/\D/", "", $request->harga_beli));
        $data->harga_jual = intval(preg_replace("/\D/", "", $request->harga_jual));
        $data->harga_perdos = intval(preg_replace("/\D/", "", $request->harga_perdos));
        $data->save();
        // return $data;

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 403);
        }
    }

    public function delete(Request $request, $id)
    {
        $data = Produk::where('id', $id)->first();
        $data->delete();

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 404);
        }
    }

    public function test()
    {
        // $hasil = [];
        // $data = [];
        // $produk = Produk::where('satuan', 'btl')->get();
        // foreach ($produk as $key => $value) {
        //     $data[] = explode(' ', $value->kemasan);
        // }

        // foreach ($data as $key => $value) {
        //     // return $value[0];
        //     $hasil[] = ($value[0] * $value[3]) / 1000;
        // }

        // foreach ($produk as $key => $value) {
        //     // return "ok";
        //     $baru = $hasil[$key];
        //     $dataProduk = Produk::where('id', $value->id)->first();
        //     $dataProduk->satuan = "ltr";
        //     $dataProduk->qty_perdos = $baru;
        //     $dataProduk->save();
        // }


        // return $produk;

        // $data = [
        //     49066200.0,
        //     54661200.0,
        //     41023350.0,
        //     88377600.0,
        //     54988850.0,
        //     84531800.0,
        //     67209400.0,
        //     35685600.0,
        //     36611600.0,
        //     103831000.0,
        //     48191000.0,
        //     14448850.0,
        //     20428850.0,
        //     24191850.0,
        //     21628200.0,
        //     83323600.0,
        //     39352500.0,
        //     83323600.0,
        //     38282500.0,
        //     86715600.0,
        //     83323600.0,
        //     46762500.0,
        //     34890500.0,
        //     250352000.0,
        //     197453200.0,
        //     201460000.0,
        //     131640000.0,
        //     5350000.0,
        //     19705400.0,
        //     38558500.0,
        //     3630000.0,
        //     2000000.0,
        //     1015200.0,
        //     5740000.0,
        //     6520000.0,
        //     27595200.0,
        //     9549200.0,
        //     11889200.0,
        //     18913200.0,
        //     3209200.0,
        //     3048000.0,
        //     1460000.0,
        //     19605000.0,
        //     1180000.0,
        //     3148000.0,
        //     3260000.0,
        //     2870000.0,
        //     32600000.0,
        //     6937200.0,
        //     4940000.0,
        //     14640000.0,
        //     10498000.0,
        //     5814000.0,
        //     3494000.0,
        //     3260000.0,
        //     1148000.0,
        //     1944000.0,
        //     1944000.0,
        //     15700000.0,
        //     1435000.0,
        //     3260000.0,
        //     13040000.0,
        //     1303800.0,
        //     5900000.0,
        //     12690600.0,
        //     11043200.0,
        //     4305000.0,
        //     7300400.0,
        //     3780000.0,
        //     17160000.0,
        //     7668000.0,
        //     16518000.0,
        //     9914600.0,
        //     17657000.0,
        //     6328800.0,
        //     9934400.0,
        //     59952200.0,
        //     10280000.0,
        // ];

        // $result = [];
        // $dpp = 0;

        // $piutangs = Piutang::where('sisa', 0)->get();

        // foreach ($piutangs as $key => $value) {
        //     // return $value;
        //     $penjualan = Penjualan::where('id', $value->penjualan_id)->first();

        //     if ($penjualan) {
        //         $piutang = Piutang::where('id', $value->id)->first();
        //         $piutang->kios_id = $penjualan->kios_id;
        //         $piutang->tanggal_piutang = $penjualan->tanggal_jual;
        //         $piutang->ket = $penjualan->invoice;
        //         $piutang->sisa = floatval(preg_replace('/[^\d\.]+/', '', $penjualan->grand_total));
        //         $piutang->save();
        //     }
        // }

        $hutangs = Hutang::where('sisa', 0)->get();

        foreach ($hutangs as $key => $value) {
            $pembelian = Pembelian::where('id', $value->pembelian_id)->first();
            // return $pembelian;

            if ($pembelian) {
                $hutangs = Hutang::where('id', $value->id)->first();
                $hutangs->supplier_id = $pembelian->supplier_id;
                $hutangs->tanggal_hutang = $pembelian->tanggal_beli;
                $hutangs->ket = $pembelian->invoice;
                $hutangs->sisa = floatval(preg_replace('/[^\d\.]+/', '', $pembelian->grand_total));
                $hutangs->save();
            }
        }

        return "ok";
    }

    public function cetakk(Request $request)
    {
        $data = [];
        $bulan = request('bulan');
        $stokBeli = 0;
        $stokJual = 0;

        $produks = DB::table('produks')->orderBy('nama_produk')->get();

        foreach ($produks as $key => $value) {
            $stokBeli = 0;
            $stokJual = 0;

            $pembelian = DB::table('detail_pembelians')
                ->join('pembelians', 'detail_pembelians.pembelian_id', 'pembelians.id')
                ->where('produk_id', $value->id)
                ->where('tahun', session('tahun'))
                // ->whereRaw("$whereBeli")
                ->when($bulan, function ($query, $bulan) {
                    if ($bulan !== 'all') {
                        return $query->whereMonth('tanggal_beli', "$bulan");
                    }
                })
                ->get();

            if (count($pembelian) > 0) {
                foreach ($pembelian as $index => $val) {
                    $stokBeli += intval(preg_replace("/\D/", "", $val->ket));
                }
            }

            $penjualan = DB::table('detail_penjualans')
                ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                ->where('produk_id', $value->id)
                ->where('tahun', session('tahun'))
                ->when($bulan, function ($query, $bulan) {
                    if ($bulan !== 'all') {
                        return $query->whereMonth('tanggal_jual', "$bulan");
                    }
                })
                ->get();

            if (count($penjualan) > 0) {
                foreach ($penjualan as $index => $val) {
                    $stokJual += intval(preg_replace("/\D/", "", $val->ket));
                }
            }

            $value->pembelian = $stokBeli;
            $value->penjualan = $stokJual;
            // $value->stok_bulanan = $stokBeli - $stokJual;
            $value->stok_bulanan = $value->stok;
            $value->harga = $value->stok_bulanan * $value->harga_perdos;
            $value->dpp = $value->harga / 1.1;
            $value->ppn = $value->harga - $value->dpp;

            $data[] = $value;

            // if ($stokBeli !== 0 || $stokJual !== 0) {
            //     $data[] = $value;
            // }
        }

        return (new GeneralResponse)->default_json(true, 'success', $data, 200);
    }

    public function cetak(Request $request)
    {

        $bulan = request('bulan');
        $jenis = request('jenis');
        $data = [];
        $temp = [];

        $produks = DB::table('produks')
            ->orderBy('nama_produk')
            ->orderBy('kemasan')
            ->get();

        foreach ($produks as $key => $value) {

            $dataProduk = DB::table('produks')->where('nama_produk', $value->nama_produk)->get();

            // $value->merge = count($dataProduk);

            $satuanKemasan = ($value->satuan == "ltr") ? "Btl" : "Bks";
            $ketKemasan = $value->jumlah_perdos;
            $qtyKemasan = $value->qty_kemasan;
            $qtyTotal = $value->qty;
            $ketTotal = round($qtyTotal / $qtyKemasan);
            $ketLeft = $ketTotal % $ketKemasan;
            $stok = ($ketLeft > 0) ? "" . ($ketTotal - $ketLeft) / $ketKemasan .
                " Dos " . $ketLeft . " " . $satuanKemasan . "" : "" . ($ketTotal -
                    $ketLeft) /
                $ketKemasan .
                " Dos";

            $value->stok = $stok;

            if ($value->stok != 0) {
                $temp[] = $value;
            }
        }

        $data['bulan'] = $bulan;
        $data['produks'] = $temp;
        // return $data;

        return $this->laporanRekapStok($data, $bulan, $jenis);
    }

    public function laporanRekapStok($data, $bulan, $jenis)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('CV AYYUB TANI')
            ->setLastModifiedBy('CV AYYUB TANI')
            ->setTitle('daftar harga produk')
            ->setSubject('daftar harga produk')
            ->setDescription('daftar harga produk')
            ->setKeywords('pdf php')
            ->setCategory('daftar harga produk');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);

        $sheet->getRowDimension(1)->setRowHeight(17);
        $sheet->getRowDimension(2)->setRowHeight(17);
        $sheet->getRowDimension(3)->setRowHeight(7);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        // //Margin PDF
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $tahun = ""  . session('tahun') . "-" . $bulan . "";
        $periode = ($bulan != 'all') ? strtoupper(strftime('%B %Y', mktime(0, 0, 0, $bulan + 1, 0, (int)session('tahun')))) : (int)session('tahun');

        $sheet->setCellValue('A1', 'DAFTAR HARGA PRODUK')->mergeCells('A1:G1');
        $sheet->setCellValue('A2', 'CV. AYYUB TANI')->mergeCells('A2:G2');
        $sheet->setCellValue('A3', "PERIODE $periode")->mergeCells('A3:G3');

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
        $sheet->setCellValue('G5', 'Stok');
        $sheet->getColumnDimension('G')->setWidth(15);

        $cell = 5;

        $sheet->getStyle('A1:A3')->getFont()->setSize(12);
        $sheet->getStyle('A:G')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:G5')->getFont()->setBold(true);
        $sheet->getStyle('A5:G5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A5:A' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('C5:C' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('D5:D' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('E5:E' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('F5:F' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('G5:G5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('G5:G' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('E6:F' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('E6:F' . (count($data['produks']) + $cell))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('H6:F' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('H6:F' . (count($data['produks']) + $cell))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A1:A3')->getAlignment()->setVertical('center')->setHorizontal('center');

        $merge = 0;
        $prevValue = '';
        $number = 0;

        foreach ($data['produks'] as $index => $value) {
            // if (strtolower($prevValue) !== strtolower($value->nama_produk)) {
            //     $prevValue = $value->nama_produk;
            //     $merge = $cell + $value->merge;
            //     $number++;
            // }
            $cell++;

            $sheet->setCellValue('A' . $cell, ++$index);
            $sheet->setCellValue('B' . $cell, strtoupper($value->nama_produk));
            $sheet->setCellValue('C' . $cell, strtoupper($value->kemasan));
            $sheet->setCellValue('D' . $cell, $value->jumlah_perdos);
            $sheet->setCellValue('E' . $cell, $value->harga_jual);
            $sheet->setCellValue('F' . $cell, $value->harga_perdos);
            $sheet->setCellValue('G' . $cell, $value->stok);
        }

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A5:G' . $cell)->applyFromArray($border);

        if ($jenis == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Daftar Harga dan Rekap Stok Produk CV. AYYUB TANI ' . $periode . '.xlsx"');
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

    public function rekapTahunan(Request $request)
    {
        $getPenjualan = DB::table('penjualans')
            ->where('tahun', $request->tahun)
            ->where('bulan', 12)
            ->get();

        if (count($getPenjualan) <= 0) {
            return (new GeneralResponse)->default_json(false, "Input semua transaksi terlebih dahulu untuk melakukan rekap tahunan!", null, 400);
        }

        $produks = DB::table("produks")
            ->orderBy('nama_produk', 'ASC')
            ->get();

        $res = [];

        foreach ($produks as $key => $value) {
            if ($value->stok != 0) {

                $getStokBulanan = DB::table('stok_tahunans')
                    ->where('produk_id', $value->id)
                    ->where('tahun', $request->tahun)
                    ->first();

                if ($getStokBulanan) {
                    return (new GeneralResponse)->default_json(false, "Stok tahunan sudah ada!", $res, 400);
                }

                $data = new StokTahunan();
                $data->produk_id = $value->id;
                $data->tahun = $request->tahun;
                $data->jumlah = $value->stok;

                if ($value->stok > 0) {
                    $data->save();
                }

                if ($value->stok < 0) {
                    $produk = Produk::where('id', $value->id)->first();
                    $produk->stok = 0;
                    $produk->qty = 0;
                    $produk->save();
                }

                $res[] = $data;
            }
        }

        if ($res) {
            return (new GeneralResponse)->default_json(true, "Success", $res, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $res, 400);
        }
    }
}
