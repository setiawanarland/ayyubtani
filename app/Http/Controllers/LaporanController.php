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
        $bulan = request('bulan');
        $stokBeli = 0;
        $stokJual = 0;

        $produks = DB::table('produks')
            ->where('stok', '!=', 0)
            ->get();

        $pembelian = DB::table("pembelians")
            ->join('detail_pembelians', 'pembelians.id', 'detail_pembelians.pembelian_id')
            ->join('produks', 'detail_pembelians.produk_id', 'produks.id')
            ->where('tahun', session('tahun'))
            ->whereMonth('tanggal_beli', "$bulan")
            ->orderBy('nama_produk', 'ASC')
            ->get();

        foreach ($produks as $key => $value) {
            $stokBeli = 0;
            $stokJual = 0;

            $pembelian = DB::table('detail_pembelians')
                ->join('pembelians', 'detail_pembelians.pembelian_id', 'pembelians.id')
                ->where('tahun', session('tahun'))
                ->whereMonth('tanggal_beli', "$bulan")
                ->where('produk_id', $value->id)
                // ->where('produk_id', 168)
                ->get();

            $penjualan = DB::table('detail_penjualans')
                ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                ->where('tahun', session('tahun'))
                ->whereMonth('tanggal_jual', "$bulan")
                ->where('produk_id', $value->id)
                // ->where('produk_id', 168)
                ->get();
            // return $pembelian;

            foreach ($pembelian as $index => $val) {
                $stokBeli += intval(preg_replace("/\D/", "", $val->ket));
            }

            foreach ($penjualan as $index => $val) {
                $stokJual += intval(preg_replace("/\D/", "", $val->ket));
            }

            $value->pembelian = $stokBeli;
            $value->penjualan = $stokJual;
        }

        // $data = DB::table("pembelians")
        //     ->join('detail_pembelians', 'pembelians.id', 'detail_pembelians.pembelian_id')
        //     ->join('produks', 'detail_pembelians.produk_id', 'produks.id')
        //     ->where('tahun', session('tahun'))
        //     ->whereMonth('tanggal_beli', "$bulan")
        //     ->orderBy('nama_produk', 'ASC')
        //     ->get();

        // $produk = DB::table("pembelians")
        //     ->join('detail_pembelians', 'pembelians.id', 'detail_pembelians.pembelian_id')
        //     ->join('produks', 'detail_pembelians.produk_id', 'produks.id')
        //     ->orderBy('nama_produk', 'ASC')
        //     ->get();

        if ($produks) {
            return (new GeneralResponse)->default_json(true, 'success', $produks, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

    public function rekap(Request $request)
    {
        $bulan = request('bulan');
        $jenis = request('jenis');
        $data = [];
        $stokBeli = 0;
        $stokJual = 0;

        $produks = DB::table('produks')
            ->where('stok', '!=', 0)
            ->get();
        if ($bulan !== 'all') {
            $pembelian = DB::table("pembelians")
                ->join('detail_pembelians', 'pembelians.id', 'detail_pembelians.pembelian_id')
                ->join('produks', 'detail_pembelians.produk_id', 'produks.id')
                ->where('tahun', session('tahun'))
                ->whereMonth('tanggal_beli', "$bulan")
                ->orderBy('nama_produk', 'ASC')
                ->get();

            foreach ($produks as $key => $value) {
                $stokBeli = 0;
                $stokJual = 0;

                $pembelian = DB::table('detail_pembelians')
                    ->join('pembelians', 'detail_pembelians.pembelian_id', 'pembelians.id')
                    ->where('tahun', session('tahun'))
                    ->whereMonth('tanggal_beli', "$bulan")
                    ->where('produk_id', $value->id)
                    // ->where('produk_id', 168)
                    ->get();

                $penjualan = DB::table('detail_penjualans')
                    ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                    ->where('tahun', session('tahun'))
                    ->whereMonth('tanggal_jual', "$bulan")
                    ->where('produk_id', $value->id)
                    // ->where('produk_id', 168)
                    ->get();
                // return $pembelian;

                foreach ($pembelian as $index => $val) {
                    $stokBeli += intval(preg_replace("/\D/", "", $val->ket));
                }

                foreach ($penjualan as $index => $val) {
                    $stokJual += intval(preg_replace("/\D/", "", $val->ket));
                }

                $value->pembelian = $stokBeli;
                $value->penjualan = $stokJual;
            }
        } else {
            $pembelian = DB::table("pembelians")
                ->join('detail_pembelians', 'pembelians.id', 'detail_pembelians.pembelian_id')
                ->join('produks', 'detail_pembelians.produk_id', 'produks.id')
                ->where('tahun', session('tahun'))
                ->whereMonth('tanggal_beli', "$bulan")
                ->orderBy('nama_produk', 'ASC')
                ->get();

            foreach ($produks as $key => $value) {
                $stokBeli = 0;
                $stokJual = 0;

                $pembelian = DB::table('detail_pembelians')
                    ->join('pembelians', 'detail_pembelians.pembelian_id', 'pembelians.id')
                    ->where('tahun', session('tahun'))
                    ->whereMonth('tanggal_beli', "$bulan")
                    ->where('produk_id', $value->id)
                    // ->where('produk_id', 168)
                    ->get();

                $penjualan = DB::table('detail_penjualans')
                    ->join('penjualans', 'detail_penjualans.penjualan_id', 'penjualans.id')
                    ->where('tahun', session('tahun'))
                    ->whereMonth('tanggal_jual', "$bulan")
                    ->where('produk_id', $value->id)
                    // ->where('produk_id', 168)
                    ->get();
                // return $pembelian;

                foreach ($pembelian as $index => $val) {
                    $stokBeli += intval(preg_replace("/\D/", "", $val->ket));
                }

                foreach ($penjualan as $index => $val) {
                    $stokJual += intval(preg_replace("/\D/", "", $val->ket));
                }

                $value->pembelian = $stokBeli;
                $value->penjualan = $stokJual;
            }
        }

        $data['bulan'] = $bulan;
        $data['produks'] = $produks;
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

        $cell = 5;

        $sheet->getStyle('A1:A3')->getFont()->setSize(12);
        $sheet->getStyle('A:F')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:F5')->getFont()->setBold(true);
        $sheet->getStyle('A5:F5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A5:A' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('B5:B' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center');
        $sheet->getStyle('C5:C' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('D5:D' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('E5:E' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('F5:F' . (count($data['produks']) + $cell))->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A1:A3')->getAlignment()->setVertical('center')->setHorizontal('center');


        foreach ($data['produks'] as $index => $value) {
            // return $value;

            $cell++;

            $sheet->setCellValue('A' . $cell, $index + 1);
            $sheet->setCellValue('B' . $cell, strtoupper($value->nama_produk));
            $sheet->setCellValue('C' . $cell, strtoupper($value->kemasan));
            $sheet->setCellValue('D' . $cell, $value->pembelian);
            $sheet->setCellValue('E' . $cell, $value->penjualan);
            $sheet->setCellValue('F' . $cell, $value->stok);

            // level if kepala 


            // $total_tambahan = 0;

            // // cek if isset skp_utama
            // if (isset($value['skp_utama'])) {

            //     $jumlah_data = 0;
            //     $sum_nilai_iki = 0;
            //     foreach ($value['skp_utama'] as $key => $val) {

            //         foreach ($val['aspek_skp'] as $k => $v) {

            //             foreach ($v['target_skp'] as $mk => $rr) {
            //                 $kategori_ = '';
            //                 if ($rr['bulan'] ==  $bulan) {

            //                     $single_rate = ($v['realisasi_skp'][$mk]['realisasi_bulanan'] / $rr['target']) * 100;

            //                     if ($single_rate > 110) {
            //                         $nilai_iki = 110 + ((120 - 110) / (110 - 101)) * (110 - 101);
            //                     } elseif ($single_rate >= 101 && $single_rate <= 110) {
            //                         $nilai_iki = 110 + ((120 - 110) / (110 - 101)) * ($single_rate - 101);
            //                     } elseif ($single_rate == 100) {
            //                         $nilai_iki = 109;
            //                     } elseif ($single_rate >= 80 && $single_rate <= 99) {
            //                         $nilai_iki = 70 + ((89 - 70) / (99 - 80)) * ($single_rate - 80);
            //                     } elseif ($single_rate >= 60 && $single_rate <= 79) {
            //                         $nilai_iki = 50 + ((69 - 50) / (79 - 60)) * ($single_rate - 60);
            //                     } elseif ($single_rate >= 0 && $single_rate <= 79) {
            //                         $nilai_iki = (49 / 59) * $single_rate;
            //                     }
            //                     //$sheet->setCellValue('J13', round($nilai_iki,1).' %' )->mergeCells('J13:J13');
            //                     $sum_nilai_iki += $nilai_iki;
            //                     $jumlah_data++;
            //                 }
            //             }
            //         }
            //     }

            //     if ($sum_nilai_iki != 0 && $jumlah_data != 0) {
            //         $nilai_utama = round($sum_nilai_iki / $jumlah_data, 1);
            //     } else {
            //         $nilai_utama = 0;
            //     }
            // } else {
            //     $nilai_utama = 0;
            // }

            // // cek if isset skp_tambahan
            // if (isset($value['skp_tambahan'])) {

            //     $total_tambahan = 0;

            //     foreach ($value['skp_tambahan'] as $key => $val) {

            //         foreach ($val['aspek_skp'] as $k => $v) {

            //             foreach ($v['target_skp'] as $mk => $rr) {
            //                 $kategori_ = '';
            //                 if ($rr['bulan'] ==  $bulan) {

            //                     $single_rate = ($v['realisasi_skp'][$mk]['realisasi_bulanan'] / $rr['target']) * 100;

            //                     if ($single_rate > 110) {
            //                         $nilai_iki = 110 + ((120 - 110) / (110 - 101)) * (110 - 101);
            //                     } elseif ($single_rate >= 101 && $single_rate <= 110) {
            //                         $nilai_iki = 110 + ((120 - 110) / (110 - 101)) * ($single_rate - 101);
            //                     } elseif ($single_rate == 100) {
            //                         $nilai_iki = 109;
            //                     } elseif ($single_rate >= 80 && $single_rate <= 99) {
            //                         $nilai_iki = 70 + ((89 - 70) / (99 - 80)) * ($single_rate - 80);
            //                     } elseif ($single_rate >= 60 && $single_rate <= 79) {
            //                         $nilai_iki = 50 + ((69 - 50) / (79 - 60)) * ($single_rate - 60);
            //                     } elseif ($single_rate >= 0 && $single_rate <= 79) {
            //                         $nilai_iki = (49 / 59) * $single_rate;
            //                     }

            //                     if ($nilai_iki > 110) {
            //                         $total_tambahan += 2.4;
            //                     } elseif ($nilai_iki >= 101 && $nilai_iki <= 110) {
            //                         $total_tambahan += 1.6;
            //                     } elseif ($nilai_iki == 100) {
            //                         $total_tambahan += 1.0;
            //                     } elseif ($nilai_iki >= 80 && $nilai_iki <= 99) {
            //                         $total_tambahan += 0.5;
            //                     } elseif ($nilai_iki >= 60 && $nilai_iki <= 79) {
            //                         $total_tambahan += 0.3;
            //                     } elseif ($nilai_iki >= 0 && $nilai_iki <= 79) {
            //                         $total_tambahan += 0.1;
            //                     }
            //                 }
            //             }
            //         }
            //     }

            //     $nilai_tambahan = $total_tambahan;
            // } else {
            //     $nilai_tambahan = 0;
            // }


            // $total_nilai = round($nilai_utama + $nilai_tambahan, 1);
            // // $sheet->setCellValue('J' . $cell, $total_nilai);
            // $sheet->setCellValue('E' . $cell, $total_nilai);
        }

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '0000000'],
                ],
            ],
        ];

        // $sheet->getStyle('A4:F' . $cell)->applyFromArray($border);
        $sheet->getStyle('A5:F' . $cell)->applyFromArray($border);

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
