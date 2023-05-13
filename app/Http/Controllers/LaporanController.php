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

        $produks = DB::table('produks')->orderBy('nama_produk')->orderBy('kemasan')->get();

        foreach ($produks as $key => $value) {
            $stokBeli = 0;
            $stokJual = 0;
            $stokBeliLalu = 0;
            $stokJualLalu = 0;

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

            $firstBeli = DB::table('pembelians')
                ->min('tanggal_beli');
            $firstJual = DB::table('penjualans')
                ->min('tanggal_jual');
            $currDate = ($bulan == 'all') ? date(session('tahun') + 1 . "-m-d", strtotime('01/01')) : date(session('tahun') . "-$bulan-01");
            // $lastDate = date($currDate, strtotime('last day of -1 month'));
            $lastDate = date("Y-m-d", strtotime($currDate . ' -1 day'));
            // return "$firstBeli - $lastDate";
            // return $lastDate;

            if ($bulan == 'all') {
                $pembelianLalu = DB::table('detail_pembelians')
                    ->join('pembelians', 'detail_pembelians.pembelian_id', 'pembelians.id')
                    ->where('produk_id', $value->id)
                    ->where('tahun', session('tahun') - 1)
                    ->get();

                if (count($pembelianLalu) > 0) {
                    foreach ($pembelianLalu as $index => $val) {
                        $stokBeliLalu += intval(preg_replace("/\D/", "", $val->ket));
                    }
                }

                $penjualanLalu = DB::table('detail_penjualans')
                    ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                    ->where('produk_id', $value->id)
                    ->where('tahun', session('tahun') - 1)
                    ->get();


                if (count($penjualanLalu) > 0) {
                    foreach ($penjualanLalu as $index => $val) {
                        $stokJualLalu += intval(preg_replace("/\D/", "", $val->ket));
                    }
                }
            } else {

                if (session('tahun') >= 2018) {
                    $firstBeli = date('Y-m-d', strtotime('Jan 01'));
                    $firstJual = date('Y-m-d', strtotime('Jan 01'));
                }

                $pembelianLalu = DB::table('detail_pembelians')
                    ->join('pembelians', 'detail_pembelians.pembelian_id', 'pembelians.id')
                    ->where('produk_id', $value->id)
                    ->whereBetween('tanggal_beli', [$firstBeli, $lastDate])
                    ->get();

                if (count($pembelianLalu) > 0) {
                    foreach ($pembelianLalu as $index => $val) {
                        $stokBeliLalu += intval(preg_replace("/\D/", "", $val->ket));
                    }
                }

                $penjualanLalu = DB::table('detail_penjualans')
                    ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                    ->where('produk_id', $value->id)
                    ->whereBetween('tanggal_jual', [$firstJual, $lastDate])
                    ->get();


                if (count($penjualanLalu) > 0) {
                    foreach ($penjualanLalu as $index => $val) {
                        $stokJualLalu += intval(preg_replace("/\D/", "", $val->ket));
                    }
                }
            }

            // if (session('tahun') == 2018 && $bulan == 1 && ) {
            //     $value->stokAwal = $value->stok;
            // } else {
            // }

            $value->stokAwal = $stokBeliLalu - $stokJualLalu;
            $value->stok_bulanan = $value->stokAwal + ($stokBeli - $stokJual);
            $value->beli = $stokBeli;
            $value->jual = $stokJual;
            $value->harga = $value->stok_bulanan * $value->harga_perdos;
            $value->dpp = $value->harga / 1.1;
            $value->ppn = $value->harga - $value->dpp;

            // $data[] = $value;
            if ($value->stokAwal !== 0 || $value->beli !== 0 || $value->jual !== 0 || $value->stok_bulanan !== 0) {
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
        $stokBeliLalu = 0;
        $stokJualLalu = 0;

        $produks = DB::table('produks')->orderBy('nama_produk')->get();

        foreach ($produks as $key => $value) {
            $stokBeli = 0;
            $stokJual = 0;
            $stokBeliLalu = 0;
            $stokJualLalu = 0;

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


            $firstBeli = DB::table('pembelians')
                ->min('tanggal_beli');
            $firstJual = DB::table('penjualans')
                ->min('tanggal_jual');

            $currDate = date(session('tahun') . "-$bulan-01");
            $lastDate = date($currDate, strtotime('last day of -1 month'));

            if ($bulan == 'all') {
                $pembelianLalu = DB::table('detail_pembelians')
                    ->join('pembelians', 'detail_pembelians.pembelian_id', 'pembelians.id')
                    ->where('produk_id', $value->id)
                    ->where('tahun', session('tahun') - 1)
                    ->get();

                if (count($pembelianLalu) > 0) {
                    foreach ($pembelianLalu as $index => $val) {
                        $stokBeliLalu += intval(preg_replace("/\D/", "", $val->ket));
                    }
                }

                $penjualanLalu = DB::table('detail_penjualans')
                    ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                    ->where('produk_id', $value->id)
                    ->where('tahun', session('tahun') - 1)
                    ->get();


                if (count($penjualanLalu) > 0) {
                    foreach ($penjualanLalu as $index => $val) {
                        $stokJualLalu += intval(preg_replace("/\D/", "", $val->ket));
                    }
                }
            } else {

                $pembelianLalu = DB::table('detail_pembelians')
                    ->join('pembelians', 'detail_pembelians.pembelian_id', 'pembelians.id')
                    ->where('produk_id', $value->id)
                    ->whereBetween('tanggal_beli', [$firstBeli, $lastDate])
                    ->get();

                if (count($pembelianLalu) > 0) {
                    foreach ($pembelianLalu as $index => $val) {
                        $stokBeliLalu += intval(preg_replace("/\D/", "", $val->ket));
                    }
                }

                $penjualanLalu = DB::table('detail_penjualans')
                    ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                    ->where('produk_id', $value->id)
                    ->whereBetween('tanggal_jual', [$firstJual, $lastDate])
                    ->get();


                if (count($penjualanLalu) > 0) {
                    foreach ($penjualanLalu as $index => $val) {
                        $stokJualLalu += intval(preg_replace("/\D/", "", $val->ket));
                    }
                }
            }

            $value->stokAwal = $stokBeliLalu - $stokJualLalu;
            $value->stok_bulanan = $value->stokAwal + ($stokBeli - $stokJual);

            // $value->stok_bulanan = $stokBeli - $stokJual;
            // $value->stok_bulanan = $value->stok;
            $value->harga = $value->stok_bulanan * $value->harga_perdos;
            $value->dpp = $value->harga / 1.1;
            $value->ppn = $value->harga - $value->dpp;

            // $temp[] = $value;
            if ($value->stok_bulanan !== 0) {
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
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $tahun = ""  . session('tahun') . "-" . $bulan . "";
        $periode = ($bulan != 'all') ? strtoupper(strftime('%B %Y', mktime(0, 0, 0, $bulan + 1, 0, (int)session('tahun')))) : (int)session('tahun');

        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI STOK BARANG')->mergeCells('A1:J1');
        $sheet->setCellValue('A2', 'CV. AYYUB TANI')->mergeCells('A2:I2');
        $sheet->setCellValue('A3', "PERIODE $periode")->mergeCells('A3:I3');

        $sheet->setCellValue('A5', 'No')->mergeCells('A5:A5');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->setCellValue('B5', 'Nama Produk');
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->setCellValue('C5', 'Kemasan');
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->setCellValue('D5', 'Stok Awal');
        $sheet->getColumnDimension('D')->setWidth(8);
        $sheet->setCellValue('E5', 'Pembelian');
        $sheet->getColumnDimension('E')->setWidth(13);
        $sheet->setCellValue('F5', 'Penjualan');
        $sheet->getColumnDimension('F')->setWidth(13);
        $sheet->setCellValue('G5', 'Stok Akhir');
        $sheet->getColumnDimension('F')->setWidth(8);
        $sheet->setCellValue('H5', 'Jml Kemasan');
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->setCellValue('I5', 'Harga Stn');
        $sheet->getColumnDimension('I')->setWidth(16);
        $sheet->setCellValue('J5', 'Disc Stn');
        $sheet->getColumnDimension('J')->setWidth(16);
        // $sheet->setCellValue('H5', 'Harga');
        // $sheet->getColumnDimension('H')->setWidth(16);
        // $sheet->setCellValue('I5', 'DPP');
        // $sheet->getColumnDimension('I')->setWidth(16);
        // $sheet->setCellValue('J5', 'PPN');
        // $sheet->getColumnDimension('J')->setWidth(16);

        $cell = 5;

        $sheet->getStyle('A1:A3')->getFont()->setSize(12);
        $sheet->getStyle('A:J')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:J5')->getFont()->setBold(true);
        $sheet->getStyle('A5:J5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A5:A' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('C5:C' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('D5:D' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('E5:E' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('F5:F' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('G5:G' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('H5:H5')->getAlignment()->setVertical('center')->setHorizontal('center');
        // $sheet->getStyle('H6:H' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('right');
        // $sheet->getStyle('H6:H' . (count($data['produks']) + $cell))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I5:I5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('I6:I' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('I6:I' . (count($data['produks']) + $cell))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I5:J5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('I6:J' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('I6:J' . (count($data['produks']) + $cell))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A1:A3')->getAlignment()->setVertical('center')->setHorizontal('center');


        foreach ($data['produks'] as $index => $value) {
            // return $value;

            $cell++;

            $sheet->setCellValue('A' . $cell, $index + 1);
            $sheet->setCellValue('B' . $cell, strtoupper($value->nama_produk));
            $sheet->setCellValue('C' . $cell, strtoupper($value->kemasan));
            $sheet->setCellValue('D' . $cell, $value->stokAwal);
            $sheet->setCellValue('E' . $cell, $value->pembelian);
            $sheet->setCellValue('F' . $cell, $value->penjualan);
            $sheet->setCellValue('G' . $cell, $value->stok_bulanan);
            // $sheet->setCellValue('G' . $cell, number_format($value->harga));
            $sheet->setCellValue('H' . $cell, $value->jumlah_perdos);
            $sheet->setCellValue('I' . $cell, $value->harga_jual);
            $sheet->setCellValue('J' . $cell, $value->disc);
        }

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A5:J' . $cell)->applyFromArray($border);

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

    public function produkJual()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Laporan Produk Jual'];

        return view('laporan.produkJual', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function listProdukJual(Request $request)
    {
        $data = [];
        $bulan = request('bulan');
        // return $bulan;
        $stokBeli = 0;
        $stokJual = 0;

        $produks = DB::table('produks')->orderBy('nama_produk')->get();

        foreach ($produks as $key => $value) {
            $stokJual = 0;

            $detailPenjualan = DB::table('detail_penjualans')
                ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                ->where('produk_id', $value->id)
                ->where('tahun', session('tahun'))
                // ->whereRaw("$whereBeli")
                ->when($bulan, function ($query, $bulan) {
                    if ($bulan !== 'all') {
                        return $query->whereMonth('tanggal_jual', "$bulan");
                    }
                })
                ->get();

            if (count($detailPenjualan) > 0) {
                foreach ($detailPenjualan as $index => $val) {
                    $stokJual += intval(preg_replace("/\D/", "", $val->ket));
                    $penjualan = DB::table('penjualans')->where('id', $val->penjualan_id)->first();
                    $val->invoice = $penjualan->invoice;

                    $kios = DB::table('kios')->where('id', $penjualan->kios_id)->first();
                    $val->kios = "$kios->pemilik, $kios->nama_kios, $kios->kabupaten";
                }
            }

            $value->penjualan = $stokJual;
            $value->detail_penjualan = $detailPenjualan;

            if (count($detailPenjualan) > 0) {
                $data[] = $value;
            }
        }

        return (new GeneralResponse)->default_json(true, 'success', $data, 200);
    }

    public function rekapProdukJual(Request $request)
    {
        $bulan = request('bulan');
        $jenis = request('jenis');
        $data = [];
        $temp = [];
        $stokBeli = 0;
        $stokJual = 0;

        $produks = DB::table('produks')->orderBy('nama_produk')->get();

        foreach ($produks as $key => $value) {
            $stokBeli = 0;
            $stokJual = 0;

            $detailPenjualan = DB::table('detail_penjualans')
                ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                ->where('produk_id', $value->id)
                ->where('tahun', session('tahun'))
                // ->whereRaw("$whereBeli")
                ->when($bulan, function ($query, $bulan) {
                    if ($bulan !== 'all') {
                        return $query->whereMonth('tanggal_jual', "$bulan");
                    }
                })
                ->get();

            if (count($detailPenjualan) > 0) {
                foreach ($detailPenjualan as $index => $val) {
                    $stokJual += intval(preg_replace("/\D/", "", $val->ket));
                    $penjualan = DB::table('penjualans')->where('id', $val->penjualan_id)->first();
                    $val->invoice = $penjualan->invoice;

                    $kios = DB::table('kios')->where('id', $penjualan->kios_id)->first();
                    $val->kios = "$kios->pemilik, $kios->nama_kios, $kios->kabupaten";
                }
            }

            $value->penjualan = $stokJual;
            $value->detail_penjualan = $detailPenjualan;

            if (count($detailPenjualan) > 0) {
                $temp[] = $value;
            }
        }

        $data['bulan'] = $bulan;
        $data['produks'] = $temp;
        // return $data;

        return $this->laporanRekapProdukJual($data, $bulan, $jenis);
    }

    public function laporanRekapProdukJual($data, $bulan, $jenis)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('CV AYYUB TANI')
            ->setLastModifiedBy('CV AYYUB TANI')
            ->setTitle('Laporan Rekap Produk Jual Bulanan')
            ->setSubject('Laporan Rekap Produk Jual Bulanan')
            ->setDescription('Laporan Rekap Produk Jual Bulanan')
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
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $tahun = ""  . session('tahun') . "-" . $bulan . "";
        $periode = ($bulan != 'all') ? strtoupper(strftime('%B %Y', mktime(0, 0, 0, $bulan + 1, 0, (int)session('tahun')))) : (int)session('tahun');

        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PRODUK JUAL')->mergeCells('A1:G1');
        $sheet->setCellValue('A2', 'CV. AYYUB TANI')->mergeCells('A2:G2');
        $sheet->setCellValue('A3', "PERIODE $periode")->mergeCells('A3:G3');

        $sheet->setCellValue('A5', 'No')->mergeCells('A5:A5');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->setCellValue('B5', 'Nama Produk');
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->setCellValue('C5', 'Kemasan');
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->setCellValue('D5', 'Invoice');
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->setCellValue('E5', 'Penjualan');
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->setCellValue('F5', 'Kios');
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->setCellValue('G5', 'Total');
        $sheet->getColumnDimension('G')->setWidth(15);

        $cell = 5;
        $merge = 0;

        // return $data;
        foreach ($data['produks'] as $index => $value) {
            // return $merge;
            $cell++;
            $merge = $cell + (count($value->detail_penjualan) - 1);

            $sheet->setCellValue('A' . $cell, $index + 1)->mergeCells('A' . $cell . ':A' . $merge);
            $sheet->setCellValue('B' . $cell, strtoupper($value->nama_produk))->mergeCells('B' . $cell . ':B' . $merge);
            $sheet->setCellValue('C' . $cell, strtoupper($value->kemasan))->mergeCells('C' . $cell . ':C' . $merge);

            $index = $cell;
            foreach ($value->detail_penjualan as $k => $v) {
                $sheet->setCellValue('D' . $index, $v->invoice);
                $sheet->setCellValue('E' . $index, $v->ket);
                $sheet->setCellValue('F' . $index, strtoupper($v->kios));
                $index++;
            }

            $sheet->setCellValue('G' . $cell, strtoupper($value->penjualan) . " Dos")->mergeCells('G' . $cell . ':G' . $merge);

            $cell = $merge;
        }

        $sheet->getStyle('A1:A3')->getFont()->setSize(12);
        $sheet->getStyle('A1:A3')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A:G')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:G5')->getFont()->setBold(true);
        $sheet->getStyle('A5:G5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A5:A' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('C5:C' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('D5:D' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('E5:E' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('F5:F' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('G5:G' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');

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
            header('Content-Disposition: attachment;filename="Rekapitulasi Laporan Produk Jual CV. AYYUB TANI ' . $periode . '.xlsx"');
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
    public function produkBeli()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Laporan Produk Beli'];

        return view('laporan.produkBeli', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function listProdukBeli(Request $request)
    {
        $data = [];
        $bulan = request('bulan');
        // return $bulan;
        $stokBeli = 0;

        $produks = DB::table('produks')->orderBy('nama_produk')->get();

        foreach ($produks as $key => $value) {
            $stokBeli = 0;

            $detailPembelian = DB::table('detail_pembelians')
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

            if (count($detailPembelian) > 0) {
                foreach ($detailPembelian as $index => $val) {
                    $stokBeli += intval(preg_replace("/\D/", "", $val->ket));
                    $pembelian = DB::table('pembelians')->where('id', $val->pembelian_id)->first();
                    $val->invoice = $pembelian->invoice;

                    $supplier = DB::table('suppliers')->where('id', $pembelian->supplier_id)->first();
                    $val->supplier = "$supplier->nama_supplier";
                }
            }

            $value->pembelian = $stokBeli;
            $value->detail_pembelian = $detailPembelian;

            if (count($detailPembelian) > 0) {
                $data[] = $value;
            }
        }

        return (new GeneralResponse)->default_json(true, 'success', $data, 200);
    }

    public function rekapProdukBeli(Request $request)
    {
        $bulan = request('bulan');
        $jenis = request('jenis');
        $data = [];
        $temp = [];
        $stokBeli = 0;
        $stokJual = 0;

        $produks = DB::table('produks')->orderBy('nama_produk')->get();

        foreach ($produks as $key => $value) {
            $stokBeli = 0;

            $detailPembelian = DB::table('detail_pembelians')
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

            if (count($detailPembelian) > 0) {
                foreach ($detailPembelian as $index => $val) {
                    $stokBeli += intval(preg_replace("/\D/", "", $val->ket));
                    $pembelian = DB::table('pembelians')->where('id', $val->pembelian_id)->first();
                    $val->invoice = $pembelian->invoice;

                    $supplier = DB::table('suppliers')->where('id', $pembelian->supplier_id)->first();
                    $val->supplier = "$supplier->nama_supplier";
                }
            }

            $value->pembelian = $stokBeli;
            $value->detail_pembelian = $detailPembelian;

            if (count($detailPembelian) > 0) {
                $temp[] = $value;
            }
        }

        $data['bulan'] = $bulan;
        $data['produks'] = $temp;
        // return $data;

        return $this->laporanRekapProdukBeli($data, $bulan, $jenis);
    }

    public function laporanRekapProdukBeli($data, $bulan, $jenis)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('CV AYYUB TANI')
            ->setLastModifiedBy('CV AYYUB TANI')
            ->setTitle('Laporan Rekap Produk Beli Bulanan')
            ->setSubject('Laporan Rekap Produk Beli Bulanan')
            ->setDescription('Laporan Rekap Produk Beli Bulanan')
            ->setKeywords('pdf php')
            ->setCategory('Laporan Rekap Produk Beli Bulanan');

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
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $tahun = ""  . session('tahun') . "-" . $bulan . "";
        $periode = ($bulan != 'all') ? strtoupper(strftime('%B %Y', mktime(0, 0, 0, $bulan + 1, 0, (int)session('tahun')))) : (int)session('tahun');

        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PRODUK BELI')->mergeCells('A1:G1');
        $sheet->setCellValue('A2', 'CV. AYYUB TANI')->mergeCells('A2:G2');
        $sheet->setCellValue('A3', "PERIODE $periode")->mergeCells('A3:G3');

        $sheet->setCellValue('A5', 'No')->mergeCells('A5:A5');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->setCellValue('B5', 'Nama Produk');
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->setCellValue('C5', 'Kemasan');
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->setCellValue('D5', 'Invoice');
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->setCellValue('E5', 'Pembelian');
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->setCellValue('F5', 'Supplier');
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->setCellValue('G5', 'Total');
        $sheet->getColumnDimension('G')->setWidth(15);

        $cell = 5;
        $merge = 0;

        // return $data;
        foreach ($data['produks'] as $index => $value) {
            // return $merge;
            $cell++;
            $merge = $cell + (count($value->detail_pembelian) - 1);

            $sheet->setCellValue('A' . $cell, $index + 1)->mergeCells('A' . $cell . ':A' . $merge);
            $sheet->setCellValue('B' . $cell, strtoupper($value->nama_produk))->mergeCells('B' . $cell . ':B' . $merge);
            $sheet->setCellValue('C' . $cell, strtoupper($value->kemasan))->mergeCells('C' . $cell . ':C' . $merge);

            $index = $cell;
            foreach ($value->detail_pembelian as $k => $v) {
                $sheet->setCellValue('D' . $index, $v->invoice);
                $sheet->setCellValue('E' . $index, $v->ket);
                $sheet->setCellValue('F' . $index, strtoupper($v->supplier));
                $index++;
            }

            $sheet->setCellValue('G' . $cell, strtoupper($value->pembelian) . " Dos")->mergeCells('G' . $cell . ':G' . $merge);

            $cell = $merge;
        }

        $sheet->getStyle('A1:A3')->getFont()->setSize(12);
        $sheet->getStyle('A1:A3')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A:G')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:G5')->getFont()->setBold(true);
        $sheet->getStyle('A5:G5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A5:A' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('C5:C' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('D5:D' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('E5:E' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('F5:F' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('G5:G' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');

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
            header('Content-Disposition: attachment;filename="Rekapitulasi Laporan Produk Beli CV. AYYUB TANI ' . $periode . '.xlsx"');
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

    public function penjualan()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Laporan Penjualan'];

        return view('laporan.penjualan', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function listPenjualan(Request $request)
    {
        $data = [];
        $bulan = request('bulan');

        $penjualan = DB::table('penjualans')
            ->where('tahun', session('tahun'))
            ->when($bulan, function ($query, $bulan) {
                if ($bulan !== 'all') {
                    return $query->whereMonth('tanggal_jual', "$bulan");
                }
            })
            ->orderBy('invoice')
            ->get();

        foreach ($penjualan as $key => $value) {

            $detailPenjualan = DB::table('detail_penjualans')
                ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                ->where('penjualan_id', $value->id)
                ->where('tahun', session('tahun'))
                ->when($bulan, function ($query, $bulan) {
                    if ($bulan !== 'all') {
                        return $query->whereMonth('tanggal_jual', "$bulan");
                    }
                })
                ->get();


            if (count($detailPenjualan) > 0) {
                foreach ($detailPenjualan as $index => $val) {

                    $produk = DB::table('produks')->select('nama_produk', 'kemasan')->where('id', $val->produk_id)->first();

                    if ($produk) {
                        $val->produk = $produk->nama_produk;
                        $val->kemasan = $produk->kemasan;
                    }
                }
            }

            $kios = DB::table('kios')->select('nama_kios', 'pemilik', 'kabupaten')->where('id', $value->kios_id)->first();

            $value->detailPenjualan = $detailPenjualan;
            $value->kios = $kios;

            if (count($detailPenjualan) > 0) {
                $data[] = $value;
            }
        }

        return (new GeneralResponse)->default_json(true, 'success', $data, 200);
    }

    public function rekapPenjualan(Request $request)
    {
        $bulan = request('bulan');
        $jenis = request('jenis');
        $data = [];
        $temp = [];
        $penjualan = DB::table('penjualans')
            ->where('tahun', session('tahun'))
            ->when($bulan, function ($query, $bulan) {
                if ($bulan !== 'all') {
                    return $query->whereMonth('tanggal_jual', "$bulan");
                }
            })
            ->orderBy('invoice')
            ->get();

        foreach ($penjualan as $key => $value) {

            $detailPenjualan = DB::table('detail_penjualans')
                ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                ->where('penjualan_id', $value->id)
                ->where('tahun', session('tahun'))
                ->when($bulan, function ($query, $bulan) {
                    if ($bulan !== 'all') {
                        return $query->whereMonth('tanggal_jual', "$bulan");
                    }
                })
                ->get();


            if (count($detailPenjualan) > 0) {
                foreach ($detailPenjualan as $index => $val) {

                    $produk = DB::table('produks')->select('nama_produk', 'kemasan')->where('id', $val->produk_id)->first();

                    if ($produk) {
                        $val->produk = $produk->nama_produk;
                        $val->kemasan = $produk->kemasan;
                    }
                }
            }

            $kios = DB::table('kios')->select('nama_kios', 'pemilik', 'kabupaten')->where('id', $value->kios_id)->first();

            $value->detail_penjualan = $detailPenjualan;
            $value->kios = $kios;

            if (count($detailPenjualan) > 0) {
                $temp[] = $value;
            }
        }

        $data['bulan'] = $bulan;
        $data['penjualan'] = $temp;
        // return $data;

        return $this->laporanRekapPenjualan($data, $bulan, $jenis);
    }

    public function laporanRekapPenjualan($data, $bulan, $jenis)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('CV AYYUB TANI')
            ->setLastModifiedBy('CV AYYUB TANI')
            ->setTitle('Laporan Rekap Penjualan Bulanan')
            ->setSubject('Laporan Rekap Penjualan Bulanan')
            ->setDescription('Laporan Rekap Penjualan Bulanan')
            ->setKeywords('pdf php')
            ->setCategory('Laporan Rekap Penjualan Bulanan');

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
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $tahun = ""  . session('tahun') . "-" . $bulan . "";
        $periode = ($bulan != 'all') ? strtoupper(strftime('%B %Y', mktime(0, 0, 0, $bulan + 1, 0, (int)session('tahun')))) : (int)session('tahun');

        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PENJUALAN')->mergeCells('A1:I1');
        $sheet->setCellValue('A2', 'CV. AYYUB TANI')->mergeCells('A2:I2');
        $sheet->setCellValue('A3', "PERIODE $periode")->mergeCells('A3:I3');

        $sheet->setCellValue('A5', 'No')->mergeCells('A5:A5');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->setCellValue('B5', 'Invoice');
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->setCellValue('C5', 'Tanggal');
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->setCellValue('D5', 'Kios');
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->setCellValue('E5', 'Penjualan');
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->setCellValue('F5', 'Ket');
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->setCellValue('G5', 'Total');
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->setCellValue('H5', 'DPP');
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->setCellValue('I5', 'PPN');
        $sheet->getColumnDimension('I')->setWidth(15);

        $cell = 5;
        $merge = 0;

        // return $data;
        foreach ($data['penjualan'] as $index => $value) {
            // return $value;
            $cell++;
            $merge = $cell + (count($value->detail_penjualan) - 1);

            $sheet->setCellValue('A' . $cell, $index + 1)->mergeCells('A' . $cell . ':A' . $merge);
            $sheet->setCellValue('B' . $cell, strtoupper($value->invoice))->mergeCells('B' . $cell . ':B' . $merge);
            $sheet->setCellValue('C' . $cell, date("d/m/Y", strtotime($value->tanggal_jual)))->mergeCells('C' . $cell . ':C' . $merge);
            $sheet->setCellValue('D' . $cell, "" . strtoupper($value->kios->pemilik) . ", " . "" . strtoupper($value->kios->nama_kios) . ", " . "" . strtoupper($value->kios->kabupaten))->mergeCells('D' . $cell . ':D' . $merge);

            $index = $cell;
            foreach ($value->detail_penjualan as $k => $v) {
                $sheet->setCellValue('E' . $index, "" . strtoupper($v->produk) . ", " . strtoupper($v->kemasan));
                $sheet->setCellValue('F' . $index, $v->ket);
                $index++;
            }

            $sheet->setCellValue('G' . $cell, $value->grand_total)->mergeCells('G' . $cell . ':G' . $merge);
            $sheet->setCellValue('H' . $cell, $value->dpp)->mergeCells('H' . $cell . ':H' . $merge);
            $sheet->setCellValue('I' . $cell, $value->ppn)->mergeCells('I' . $cell . ':I' . $merge);

            $cell = $merge;
        }

        $sheet->getStyle('A1:A3')->getFont()->setSize(12);
        $sheet->getStyle('A1:A3')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A:I')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:I5')->getFont()->setBold(true);
        $sheet->getStyle('A5:I5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A5:A' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B5:B' . (count($data['penjualan']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B5:B' . (count($data['penjualan']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('C5:C' . (count($data['penjualan']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('D5:D' . (count($data['penjualan']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('E5:E' . (count($data['penjualan']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('F5:F' . (count($data['penjualan']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->getStyle('G5:G' . (count($data['penjualan']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('G5:G' . (count($data['penjualan']) + $cell))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('H5:H' . (count($data['penjualan']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('H5:H' . (count($data['penjualan']) + $cell))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I5:I' . (count($data['penjualan']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('I5:I' . (count($data['penjualan']) + $cell))->getNumberFormat()->setFormatCode('#,##0');

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        $sheet->getStyle('A5:I' . $cell)->applyFromArray($border);

        if ($jenis == 'excel') {
            // Untuk download 
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Rekapitulasi Laporan Penjualan CV. AYYUB TANI ' . $periode . '.xlsx"');
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

    public function pembelian()
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Laporan Pembelian'];

        return view('laporan.pembelian', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function listPembelian(Request $request)
    {
        $data = [];
        $bulan = request('bulan');

        $pembelian = DB::table('pembelians')
            ->where('tahun', session('tahun'))
            ->when($bulan, function ($query, $bulan) {
                if ($bulan !== 'all') {
                    return $query->whereMonth('tanggal_beli', "$bulan");
                }
            })
            ->orderBy('tanggal_beli')
            ->get();

        foreach ($pembelian as $key => $value) {

            $detailPembelian = DB::table('detail_pembelians')
                ->join('pembelians', 'detail_pembelians.pembelian_id', 'pembelians.id')
                ->where('pembelian_id', $value->id)
                ->where('tahun', session('tahun'))
                ->when($bulan, function ($query, $bulan) {
                    if ($bulan !== 'all') {
                        return $query->whereMonth('tanggal_beli', "$bulan");
                    }
                })
                ->get();


            if (count($detailPembelian) > 0) {
                foreach ($detailPembelian as $index => $val) {

                    $produk = DB::table('produks')->select('nama_produk', 'kemasan')->where('id', $val->produk_id)->first();

                    if ($produk) {
                        $val->produk = $produk->nama_produk;
                        $val->kemasan = $produk->kemasan;
                    }
                }
            }

            $supplier = DB::table('suppliers')->select('nama_supplier')->where('id', $value->supplier_id)->first();

            $value->detailPembelian = $detailPembelian;
            $value->supplier = $supplier;

            if (count($detailPembelian) > 0) {
                $data[] = $value;
            }
        }

        return (new GeneralResponse)->default_json(true, 'success', $data, 200);
    }

    public function rekapPembelian(Request $request)
    {
        $bulan = request('bulan');
        $jenis = request('jenis');
        $data = [];
        $temp = [];
        $penjualan = DB::table('pembelians')
            ->where('tahun', session('tahun'))
            ->when($bulan, function ($query, $bulan) {
                if ($bulan !== 'all') {
                    return $query->whereMonth('tanggal_beli', "$bulan");
                }
            })
            ->orderBy('tanggal_beli')
            ->get();

        foreach ($penjualan as $key => $value) {

            $detailPembelian = DB::table('detail_pembelians')
                ->join('pembelians', 'detail_pembelians.pembelian_id', 'pembelians.id')
                ->where('pembelian_id', $value->id)
                ->where('tahun', session('tahun'))
                ->when($bulan, function ($query, $bulan) {
                    if ($bulan !== 'all') {
                        return $query->whereMonth('tanggal_beli', "$bulan");
                    }
                })
                ->get();


            if (count($detailPembelian) > 0) {
                foreach ($detailPembelian as $index => $val) {

                    $produk = DB::table('produks')->select('nama_produk', 'kemasan')->where('id', $val->produk_id)->first();

                    if ($produk) {
                        $val->produk = $produk->nama_produk;
                        $val->kemasan = $produk->kemasan;
                    }
                }
            }

            $supplier = DB::table('suppliers')->select('nama_supplier')->where('id', $value->supplier_id)->first();

            $value->detail_pembelian = $detailPembelian;
            $value->supplier = $supplier;

            if (count($detailPembelian) > 0) {
                $temp[] = $value;
            }
        }

        $data['bulan'] = $bulan;
        $data['pembelian'] = $temp;
        // return $data;

        return $this->laporanRekapPembelian($data, $bulan, $jenis);
    }

    public function laporanRekapPembelian($data, $bulan, $jenis)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('CV AYYUB TANI')
            ->setLastModifiedBy('CV AYYUB TANI')
            ->setTitle('Laporan Rekap Pembelian Bulanan')
            ->setSubject('Laporan Rekap Pembelian Bulanan')
            ->setDescription('Laporan Rekap Pembelian Bulanan')
            ->setKeywords('pdf php')
            ->setCategory('Laporan Rekap Pembelian Bulanan');

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
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $tahun = ""  . session('tahun') . "-" . $bulan . "";
        $periode = ($bulan != 'all') ? strtoupper(strftime('%B %Y', mktime(0, 0, 0, $bulan + 1, 0, (int)session('tahun')))) : (int)session('tahun');

        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PENJUALAN')->mergeCells('A1:G1');
        $sheet->setCellValue('A2', 'CV. AYYUB TANI')->mergeCells('A2:G2');
        $sheet->setCellValue('A3', "PERIODE $periode")->mergeCells('A3:G3');

        $sheet->setCellValue('A5', 'No')->mergeCells('A5:A5');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->setCellValue('B5', 'Invoice');
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->setCellValue('C5', 'Tanggal');
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->setCellValue('D5', 'Supplier');
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->setCellValue('E5', 'Pembelian');
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->setCellValue('F5', 'Ket');
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->setCellValue('G5', 'Total');
        $sheet->getColumnDimension('G')->setWidth(10);

        $cell = 5;
        $merge = 0;

        // return $data;
        foreach ($data['pembelian'] as $index => $value) {
            // return $value;
            $cell++;
            $merge = $cell + (count($value->detail_pembelian) - 1);

            $sheet->setCellValue('A' . $cell, $index + 1)->mergeCells('A' . $cell . ':A' . $merge);
            $sheet->setCellValue('B' . $cell, strtoupper($value->invoice))->mergeCells('B' . $cell . ':B' . $merge);
            $sheet->setCellValue('C' . $cell, date("d/m/Y", strtotime($value->tanggal_beli)))->mergeCells('C' . $cell . ':C' . $merge);
            $sheet->setCellValue('D' . $cell, strtoupper($value->supplier->nama_supplier))->mergeCells('D' . $cell . ':D' . $merge);

            $index = $cell;
            foreach ($value->detail_pembelian as $k => $v) {
                $sheet->setCellValue('E' . $index, "" . strtoupper($v->produk) . ", " . strtoupper($v->kemasan));
                $sheet->setCellValue('F' . $index, $v->ket);
                $index++;
            }
            $sheet->setCellValue('G' . $cell, $value->grand_total)->mergeCells('G' . $cell . ':G' . $merge);

            $cell = $merge;
        }

        $sheet->getStyle('A1:A3')->getFont()->setSize(12);
        $sheet->getStyle('A1:A3')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A:G')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:G5')->getFont()->setBold(true);
        $sheet->getStyle('A5:G5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A5:A' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B5:B' . (count($data['pembelian']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B5:B' . (count($data['pembelian']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('C5:C' . (count($data['pembelian']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('D5:D' . (count($data['pembelian']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('E5:E' . (count($data['pembelian']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('F5:F' . (count($data['pembelian']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('G5:G' . (count($data['pembelian']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('G6:G' . (count($data['pembelian']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('G6:G' . (count($data['pembelian']) + $cell))->getNumberFormat()->setFormatCode('#,##0');

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
            header('Content-Disposition: attachment;filename="Rekapitulasi Laporan Pembelian CV. AYYUB TANI ' . $periode . '.xlsx"');
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
