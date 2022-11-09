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
            ->get();

        return view('pembelian.index', compact('page_title', 'page_description', 'breadcrumbs', 'supplier', 'produk'));
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

        $pajak = pajak::select('satuan_pajak')
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
        $qty = $produk->jumlah_perdos * $request->ket;
        $jumlah = ($produk->harga_beli * $qty) * $request->ket;
        $jumlahDisc = $jumlah * $request->disc / 100;
        $jumlahAfterDisc = $jumlah - $jumlahDisc;

        $dataDetail = DetailPembelianTemp::where('produk_id', $request->produk_id)->first();
        if ($dataDetail != null) {
            return (new GeneralResponse)->default_json(false, "Barang sudah ada!", null, 422);
        }

        $data = new DetailPembelianTemp();
        $data->produk_id = $request->produk_id;
        $data->qty = $qty;
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

    // public function print($data, $type)
    // {
    //     // return $data;
    //     $spreadsheet = new Spreadsheet;

    //     $spreadsheet->getProperties()->setCreator('AYYUB TANI')
    //         ->setLastModifiedBy('AYYUB TANI')
    //         ->setTitle('Preview Pembelian')
    //         ->setSubject('Preview Pembelian')
    //         ->setDescription('Preview Pembelian')
    //         ->setKeywords('pdf php')
    //         ->setCategory('Preview Pembelian');
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    //     $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);

    //     $sheet->getRowDimension(1)->setRowHeight(17);
    //     $sheet->getRowDimension(2)->setRowHeight(17);
    //     $sheet->getRowDimension(3)->setRowHeight(7);
    //     $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
    //     $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
    //     $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
    //     $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

    //     // //Margin PDF
    //     $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.3);
    //     $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.3);
    //     $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.5);
    //     $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.3);


    //     $sheet->setCellValue('A1', 'LAPORAN PEMBAYARAN TAMBAHAN PENGAHASILAN PEGAWAI')->mergeCells('A1:U1');
    //     // $sheet->setCellValue('A2', 'BULAN ' . strtoupper(date('F Y', mktime(0, 0, 0, $bulan + 1, 0))))->mergeCells('A2:U2');
    //     // $sheet->setCellValue('A3', 'OPD ' . strtoupper($data['satuan_kerja']))->mergeCells('A3:U3');

    //     // $sheet->setCellValue('A4', 'BULAN ' . strtoupper(date('F Y', mktime(0, 0, 0, $bulan + 1, 0))))->mergeCells('A4:U4');

    //     // $sheet->setCellValue('A5', 'NO.')->mergeCells('A5:A7');
    //     // $sheet->setCellValue('B5', 'NAMA & NIP')->mergeCells('B5:B7');
    //     // $sheet->setCellValue('C5', 'GOL.')->mergeCells('C5:C7');
    //     // $sheet->setCellValue('D5', 'JABATAN')->mergeCells('D5:D7');
    //     // $sheet->setCellValue('E5', 'JENIS JABATAN SESUAI PERBUB TPP')->mergeCells('E5:E7');
    //     // $sheet->setCellValue('F5', 'KELAS JABATAN')->mergeCells('F5:F7');
    //     // $sheet->setCellValue('G5', 'PAGU TPP')->mergeCells('G5:G7');
    //     // $sheet->setCellValue('H5', 'BESARAN TPP')->mergeCells('H5:N5');
    //     // $sheet->setCellValue('H6', 'KINERJA 60% DARI PAGU TPP')->mergeCells('H6:J6');
    //     // $sheet->setCellValue('K6', 'KEHADIRAN 40% DARI PAGU TPP')->mergeCells('K6:N6');
    //     // $sheet->setCellValue('H7', '% KINERJA');
    //     // $sheet->setCellValue('I7', 'NILAI SKP');
    //     // $sheet->setCellValue('J7', 'NILAI KINERJA');
    //     // $sheet->setCellValue('K7', '% KEHADIRAN');
    //     // $sheet->setCellValue('L7', '% PENGURANGAN KEHADIRAN');
    //     // $sheet->setCellValue('M7', 'NILAI PENGURANGAN KEHADIRAN');
    //     // $sheet->setCellValue('N7', 'JUMLAH KEHADIRAN');
    //     // $sheet->setCellValue('O5', 'BPJS 1%')->mergeCells('O5:O7');
    //     // $sheet->setCellValue('P5', 'TPP BRUTO')->mergeCells('P5:P7');
    //     // $sheet->setCellValue('Q5', 'PPH PSL 21')->mergeCells('Q5:Q7');
    //     // $sheet->setCellValue('R5', 'TPP NETTO')->mergeCells('R5:R7');
    //     // $sheet->setCellValue('S5', 'NILAI BRUTO SPM')->mergeCells('S5:S7');
    //     // $sheet->setCellValue('T5', 'NO. REK')->mergeCells('T5:T7');
    //     // $sheet->setCellValue('U5', 'IURAN 4% (DIBAYAR OLEH PEMDA)')->mergeCells('U5:U7');

    //     // $sheet->getColumnDimension('A')->setWidth(5);
    //     // $sheet->getColumnDimension('B')->setWidth(25);
    //     // $sheet->getColumnDimension('D')->setWidth(20);
    //     // $sheet->getColumnDimension('E')->setWidth(20);
    //     // $sheet->getColumnDimension('G')->setWidth(20);
    //     // $sheet->getColumnDimension('H')->setWidth(5);
    //     // $sheet->getColumnDimension('I')->setWidth(5);
    //     // $sheet->getColumnDimension('J')->setWidth(5);
    //     // $sheet->getColumnDimension('K')->setWidth(5);
    //     // $sheet->getColumnDimension('L')->setWidth(5);
    //     // $sheet->getColumnDimension('M')->setWidth(5);
    //     // $sheet->getColumnDimension('N')->setWidth(5);
    //     // $sheet->getColumnDimension('O')->setWidth(10);
    //     // $sheet->getColumnDimension('P')->setWidth(10);
    //     // $sheet->getColumnDimension('Q')->setWidth(10);
    //     // $sheet->getColumnDimension('R')->setWidth(10);
    //     // $sheet->getColumnDimension('S')->setWidth(15);

    //     // $sheet->getStyle('A1:A3')->getFont()->setSize(12);
    //     // $sheet->getStyle('A:U')->getAlignment()->setWrapText(true);
    //     // $sheet->getStyle('A1:A3')->getAlignment()->setVertical('center')->setHorizontal('center');
    //     // $sheet->getStyle('A5:U7')->getFont()->setBold(true);
    //     // $sheet->getStyle('A5:U7')->getAlignment()->setVertical('center')->setHorizontal('center');

    //     // $cell = 8;
    //     // $jmlPaguTpp = 0;
    //     // $jmlNilaiKinerja = 0;
    //     // $jmlNilaiKehadiran = 0;
    //     // $jmlBpjs = 0;
    //     // $jmlTppBruto = 0;
    //     // $jmlPphPsl = 0;
    //     // $jmlTppNetto = 0;
    //     // $jmlBrutoSpm = 0;
    //     // $jmlIuran = 0;

    //     // foreach ($data['list_pegawai'] as $key => $value) {
    //     //     // return $value;
    //     //     $sheet->setCellValue('A' . $cell, $key + 1);
    //     //     $sheet->setCellValue('B' . $cell, $value['nama'] . PHP_EOL . "'" . $value['nip']);
    //     //     $sheet->setCellValue('C' . $cell, $value['golongan']);
    //     //     $sheet->setCellValue('D' . $cell, $value['nama_jabatan']);
    //     //     $sheet->setCellValue('E' . $cell, $value['jenis_jabatan']);
    //     //     // kelas jabatan
    //     //     $sheet->setCellValue('F' . $cell, '-');
    //     //     $sheet->setCellValue('G' . $cell, number_format($value['nilai_jabatan']));
    //     //     $sheet->setCellValue('H' . $cell, '60%');
    //     //     $sheet->setCellValue('I' . $cell, $value['total_kinerja']);

    //     //     $nilaiKinerja = (60 * $value['nilai_jabatan'] / 100) * ($value['total_kinerja'] / 120);
    //     //     $sheet->setCellValue('J' . $cell, number_format($nilaiKinerja));

    //     //     $persentaseKehadiran = 40 * $value['nilai_jabatan'] / 100;
    //     //     $sheet->setCellValue('K' . $cell, number_format($persentaseKehadiran));
    //     //     $sheet->setCellValue('L' . $cell, $value['persentase_pemotongan']);

    //     //     $nilaiKehadiran = $persentaseKehadiran * $value['persentase_pemotongan'] / 100;
    //     //     $sheet->setCellValue('M' . $cell, number_format($nilaiKehadiran));

    //     //     $jumlahKehadiran = $persentaseKehadiran - $nilaiKehadiran;
    //     //     $sheet->setCellValue('N' . $cell, number_format($jumlahKehadiran));

    //     //     $bpjs = 1 * $value['nilai_jabatan'] / 100;
    //     //     $sheet->setCellValue('O' . $cell, number_format($bpjs));

    //     //     $tppBruto = $nilaiKinerja + $jumlahKehadiran - $bpjs;
    //     //     $sheet->setCellValue('P' . $cell, number_format($tppBruto));

    //     //     $pphPsl = 15 * $tppBruto / 100;
    //     //     $sheet->setCellValue('Q' . $cell, number_format($pphPsl));

    //     //     $tppNetto = $tppBruto - $pphPsl;
    //     //     $sheet->setCellValue('R' . $cell, number_format($tppNetto));

    //     //     $iuran = 4 * $value['nilai_jabatan'] / 100;
    //     //     $brutoSpm = $nilaiKehadiran + $jumlahKehadiran + $iuran;
    //     //     $sheet->setCellValue('S' . $cell, number_format($brutoSpm));

    //     //     // norek
    //     //     $sheet->setCellValue('T' . $cell, '-');

    //     //     $sheet->setCellValue('U' . $cell, number_format($iuran));

    //     //     // JUMLAH
    //     //     $jmlPaguTpp += $value['nilai_jabatan'];
    //     //     $jmlNilaiKinerja += $nilaiKinerja;
    //     //     $jmlNilaiKehadiran += $jumlahKehadiran;
    //     //     $jmlBpjs += $bpjs;
    //     //     $jmlTppBruto += $tppBruto;
    //     //     $jmlPphPsl += $pphPsl;
    //     //     $jmlTppNetto += $tppNetto;
    //     //     $jmlBrutoSpm += $brutoSpm;
    //     //     $jmlIuran += $iuran;

    //     //     $cell++;
    //     // }

    //     // $sheet->setCellValue('A' . $cell, "JUMLAH")->mergeCells('A' . $cell . ':F' . $cell);
    //     // $sheet->setCellValue('G' . $cell, number_format($jmlPaguTpp));
    //     // $sheet->setCellValue('J' . $cell, number_format($jmlNilaiKinerja));
    //     // $sheet->setCellValue('N' . $cell, number_format($jmlNilaiKehadiran));
    //     // $sheet->setCellValue('O' . $cell, number_format($jmlBpjs));
    //     // $sheet->setCellValue('P' . $cell, number_format($jmlTppBruto));
    //     // $sheet->setCellValue('Q' . $cell, number_format($jmlPphPsl));
    //     // $sheet->setCellValue('R' . $cell, number_format($jmlTppNetto));
    //     // $sheet->setCellValue('S' . $cell, number_format($jmlBrutoSpm));
    //     // $sheet->setCellValue('U' . $cell, number_format($jmlIuran));

    //     // $sheet->getStyle('A5:U' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
    //     // $sheet->getStyle('B5:B' . $cell)->getAlignment()->setVertical('center')->setHorizontal('left');
    //     // $sheet->getStyle('A' . $cell . ':U' . $cell)->getFont()->setBold(true);

    //     $border = [
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //                 'color' => ['argb' => '0000000'],
    //             ],
    //         ],
    //     ];

    //     // $sheet->getStyle('A5:U' . $cell)->applyFromArray($border);

    //     // $cell++;
    //     // $sheet->setCellValue('B' . $cell, '')->mergeCells('B' . $cell . ':U' . $cell);

    //     // $tgl_cetak = date("t", strtotime($tahun)) . ' ' . strftime('%B %Y', mktime(0, 0, 0, $bulan + 1, 0, (int)session('tahun_penganggaran')));

    //     // $sheet->setCellValue('S' . ++$cell, 'Bulukumba, ' . $tgl_cetak)->mergeCells('S' . $cell . ':U' . $cell);
    //     // $sheet->getStyle('E' . $cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    //     // $cell = $cell + 2;
    //     // $sheet->setCellValue('C' . $cell, 'KEPALA OPD')->mergeCells('C' . $cell . ':D' . $cell);
    //     // $sheet->setCellValue('I' . $cell, 'BENDAHARA PENGELUARAN')->mergeCells('I' . $cell . ':L' . $cell);
    //     // $sheet->setCellValue('R' . $cell, 'NAMA PEMBUAT DAFTAR')->mergeCells('R' . $cell . ':T' . $cell);
    //     // $sheet->getStyle('C' . $cell . ':S' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');


    //     // $cell = $cell + 3;
    //     // $sheet->setCellValue('C' . $cell, 'NAMA KEPALA OPD')->mergeCells('C' . $cell . ':D' . $cell);
    //     // $sheet->setCellValue('I' . $cell, 'NAMA BENDAHARA')->mergeCells('I' . $cell . ':L' . $cell);
    //     // $sheet->setCellValue('R' . $cell, 'NAMA PEMBUAT DAFTAR')->mergeCells('R' . $cell . ':T' . $cell);
    //     // $sheet->getStyle('C' . $cell . ':S' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');
    //     // $sheet->getStyle('C' . $cell . ':S' . $cell)->getFont()->setUnderline(true);;
    //     $cell = 0;
    //     $cell++;
    //     $sheet->setCellValue('C' . $cell, 'GOLONGAN JABATAN')->mergeCells('C' . $cell . ':D' . $cell);
    //     $sheet->setCellValue('C' . $cell, 'NIP')->mergeCells('C' . $cell . ':D' . $cell);
    //     $sheet->getStyle('C' . $cell . ':S' . $cell)->getAlignment()->setVertical('center')->setHorizontal('center');



    //     if ($type == 'excel') {
    //         // Untuk download 
    //         $writer = new Xlsx($spreadsheet);
    //         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //         header('Content-Disposition: attachment;filename="Daftar Laporan TPP"' . $data['satuan_kerja'] . ' Bulan ' . ucwords(date('F Y', mktime(0, 0, 0, $bulan + 1, 0))) . ' .xlsx"');
    //     } else {
    //         $spreadsheet->getActiveSheet()->getHeaderFooter()
    //             ->setOddHeader('&C&H' . url()->current());
    //         $spreadsheet->getActiveSheet()->getHeaderFooter()
    //             ->setOddFooter('&L&B &RPage &P of &N');
    //         $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
    //         \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);
    //         header('Content-Type: application/pdf');
    //         header('Cache-Control: max-age=0');
    //         $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');
    //     }

    //     $writer->save('php://output');
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $dataPembelian['bulan'] = date('m', strtotime(date('d/m/Y', strtotime($request->tanggal_beli))));
        $dataPembelian['tahun'] = date('Y', strtotime(date('d/m/Y', strtotime($request->tanggal_beli))));

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
            $detailPembelian->qty = $request->qty[$key];
            $detailPembelian->ket = $request->ket[$key];
            $detailPembelian->disc = $request->disc[$key];
            $detailPembelian->jumlah = intval(preg_replace("/\D/", "", $request->jumlah[$key]));
            $detailPembelian->save();

            $produk = Produk::where('id', $value)->first();
            $stok = $produk->stok;
            $jumlahPerdos = $produk->jumlah_perdos;
            $stokMasuk = $request->qty[$key] / $jumlahPerdos;
            $produk->stok = $stok + $stokMasuk;
            $produk->save();
        }

        $dataHutang = [];
        $dataHutang['bulan'] = date('m', strtotime(date('d/m/Y', strtotime($request->tanggal_beli))));
        $dataHutang['tahun'] = date('Y', strtotime(date('d/m/Y', strtotime($request->tanggal_beli))));

        $hutang = new Hutang();
        $hutang->pembelian_id = $pembelian->id;
        $hutang->bulan = $dataHutang['bulan'];
        $hutang->tahun = $dataHutang['tahun'];
        $hutang->ket = '';
        $hutang->debet = intval(preg_replace("/\D/", "", $request->grand_total));
        $hutang->kredit = 0;
        $hutang->sisa = intval(preg_replace("/\D/", "", $request->grand_total)) - $hutang->kredit;
        $hutang->save();

        $temp = DetailPembelianTemp::truncate();

        if ($temp) {
            return (new GeneralResponse)->default_json(true, "Success", null, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", null, 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function show(pembelian $pembelian)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function destroy(pembelian $pembelian)
    {
        //
    }
}
