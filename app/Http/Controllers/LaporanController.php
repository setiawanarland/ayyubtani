<?php

namespace App\Http\Controllers;

use App\Http\Response\GeneralResponse;
use DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

setlocale(LC_ALL, 'IND');

class LaporanController extends Controller
{
    public function stok()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Laporan Stok'];

        return view('laporan.stok', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function list(Request $request)
    {
        $data = [];
        $bulan = request('bulan');
        $stokBeli = 0;
        $stokJual = 0;

        $produks = DB::table('produks')->get();

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
            $value->stok_bulanan = $stokBeli - $stokJual;
            $value->harga = $value->stok_bulanan * $value->harga_perdos;

            if ($stokBeli !== 0 || $stokJual !== 0) {
                $data[] = $value;
            }
        }

        return (new GeneralResponse)->default_json(true, 'success', $data, 200);
    }

    public function rekap(Request $request)
    {
        $bulan = request('bulan');
        $jenis = request('jenis');
        $data = [];
        $temp = [];
        $stokBeli = 0;
        $stokJual = 0;

        $produks = DB::table('produks')->get();

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
            $value->stok_bulanan = $stokBeli - $stokJual;
            $value->harga = $value->stok_bulanan * $value->harga_perdos;

            if ($stokBeli !== 0 || $stokJual !== 0) {
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
            ->setTitle('Laporan Rekap Stok Bulanan')
            ->setSubject('Laporan Rekap Stok Bulanan')
            ->setDescription('Laporan Rekap Stok Bulanan')
            ->setKeywords('pdf php')
            ->setCategory('Laporan Rekap Stok Bulanan');

        $sheet = $spreadsheet->getActiveSheet();
        // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
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
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $tahun = ""  . session('tahun') . "-" . $bulan . "";
        $periode = ($bulan != 'all') ? strtoupper(strftime('%B %Y', mktime(0, 0, 0, $bulan + 1, 0, (int)session('tahun')))) : (int)session('tahun');

        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI STOK BARANG')->mergeCells('A1:F1');
        $sheet->setCellValue('A2', 'CV. AYYUB TANI')->mergeCells('A2:F2');
        $sheet->setCellValue('A3', "PERIODE $periode")->mergeCells('A3:F3');

        $sheet->setCellValue('A5', 'No')->mergeCells('A5:A5');
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->setCellValue('B5', 'Nama Produk');
        $sheet->getColumnDimension('B')->setWidth(60);
        $sheet->setCellValue('C5', 'Kemasan');
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->setCellValue('D5', 'Pembelian');
        $sheet->getColumnDimension('D')->setWidth(13);
        $sheet->setCellValue('E5', 'Penjualan');
        $sheet->getColumnDimension('E')->setWidth(13);
        $sheet->setCellValue('F5', 'Stok');
        $sheet->getColumnDimension('F')->setWidth(8);
        $sheet->setCellValue('G5', 'Harga');
        $sheet->getColumnDimension('G')->setWidth(20);

        $cell = 5;

        $sheet->getStyle('A1:A3')->getFont()->setSize(12);
        $sheet->getStyle('A:G')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:G5')->getFont()->setBold(true);
        $sheet->getStyle('A5:G5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A5:A' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('C5:C' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('D5:D' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('E5:E' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('F5:F' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('G5:G5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('G6:G' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('A1:A3')->getAlignment()->setVertical('center')->setHorizontal('center');


        foreach ($data['produks'] as $index => $value) {
            // return $value;

            $cell++;

            $sheet->setCellValue('A' . $cell, $index + 1);
            $sheet->setCellValue('B' . $cell, strtoupper($value->nama_produk));
            $sheet->setCellValue('C' . $cell, strtoupper($value->kemasan));
            $sheet->setCellValue('D' . $cell, $value->pembelian);
            $sheet->setCellValue('E' . $cell, $value->penjualan);
            $sheet->setCellValue('F' . $cell, $value->stok_bulanan);
            $sheet->setCellValue('G' . $cell, number_format($value->harga, 1));
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
            header('Content-Disposition: attachment;filename="Rekapitulasi Laporan Stok CV. AYYUB TANI ' . $periode . '.xlsx"');
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
