<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use App\Http\Requests\StorePiutangRequest;
use App\Http\Requests\UpdatePiutangRequest;
use App\Http\Response\GeneralResponse;
use App\Models\BayarPiutang;
use App\Models\Kios;
use DB;
use Illuminate\Http\Request;

class PiutangController extends Controller
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
        $breadcrumbs = ['Daftar Piutang'];

        $data = [];
        $temp = [];

        $debet = 0;
        $kredit = 0;
        $sisa = 0;

        $totalPiutang = 0;
        $totalSisa = 0;

        $kios = DB::table('kios')->orderBy('kabupaten')->orderBy('nama_kios')->get();
        foreach ($kios as $key => $value) {
            $totalPiutangKios = 0;
            $kredit = 0;
            $sisa = 0;

            $piutangs = Piutang::join('kios', 'piutangs.kios_id', 'kios.id')
                ->where('kios_id', $value->id)
                ->where('status_lunas', '0')
                ->sum('piutangs.sisa');

            // $bayarPiutangs = BayarPiutang::join('kios', 'bayar_piutangs.kios_id', 'kios.id')
            //     ->where('kios_id', $value->id)
            //     ->sum('bayar_piutangs.total_bayar');
            // $totalPiutangKios = $piutangs - $bayarPiutangs;
            $totalPiutangKios = $piutangs;
            // return $piutangs;

            $value->totalPiutangKios = $totalPiutangKios;

            if ($value->totalPiutangKios > 0) {
                $temp[] = $value;
            }

            $totalPiutang += $value->totalPiutangKios;
        }

        $data['kios'] = $temp;
        $data['totalPiutang'] = $totalPiutang;

        return view('piutang.index', compact('page_title', 'page_description', 'breadcrumbs', 'data'));
    }

    public function list()
    {
        $response = (new PiutangController)->getList();
        return $response;
    }

    public function getList()
    {
        $piutang = DB::table("piutangs")
            ->join('penjualans', 'piutangs.penjualan_id', 'penjualans.id')
            ->where('piutangs.tahun', session('tahun'))
            ->orderBy('piutangs.bulan', 'ASC')
            ->orderBy('piutangs.tahun', 'ASC')
            ->get();

        if ($piutang) {
            return (new GeneralResponse)->default_json(true, 'success', $piutang, 200);
        } else {
            return (new GeneralResponse)->default_json(false, 'error', null, 401);
        }
    }

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
     * @param  \App\Http\Requests\StorePiutangRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePiutangRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Piutang  $piutang
     * @return \Illuminate\Http\Response
     */
    public function show(Piutang $piutang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Piutang  $piutang
     * @return \Illuminate\Http\Response
     */
    public function edit(Piutang $piutang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePiutangRequest  $request
     * @param  \App\Models\Piutang  $piutang
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePiutangRequest $request, Piutang $piutang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Piutang  $piutang
     * @return \Illuminate\Http\Response
     */
    public function destroy(Piutang $piutang)
    {
        //
    }

    public function detailPiutang($kiosId)
    {
        $page_title = 'Ayyub Tani';
        $page_description = 'Dashboard Admin Ayyub Tani';
        $breadcrumbs = ['Detail Piutang'];

        $data = [];
        $temp = [];
        $dateTransaction = [];
        $totalPiutang = 0;

        $transPiutang = Piutang::select('tanggal_piutang')
            ->where('kios_id', $kiosId)
            ->get();
        foreach ($transPiutang as $key => $value) {
            $dateTransaction[$value->tanggal_piutang] = $value->tanggal_piutang;
        }

        $transBayarPiutang = BayarPiutang::select('tanggal_bayar')
            ->where('kios_id', $kiosId)
            ->get();
        foreach ($transBayarPiutang as $key => $value) {
            $dateTransaction[$value->tanggal_bayar] = $value->tanggal_bayar;
        }

        $dateSort = collect($dateTransaction)->sortKeys()->toArray();

        $kios = Kios::where('kios.id', $kiosId)
            ->first();

        foreach ($dateSort as $key => $value) {
            $piutang = Piutang::select('piutangs.*', 'kios.*', 'piutangs.id as piutang_id')
                ->join('kios', 'piutangs.kios_id', 'kios.id')
                ->where('tanggal_piutang', $value)
                ->where('piutangs.kios_id', $kios->id)
                ->get();
            // return $piutang;

            if (count($piutang) > 0) {
                foreach ($piutang as $key => $val) {

                    $temp['tanggal_transaksi'] = $val->tanggal_piutang;
                    $temp['invoice'] = $val->invoice;
                    $temp['debet'] = $val->total;
                    $temp['tanggal_bayar'] = $val->tanggal_bayar;
                    $temp['kredit'] = $val->kredit;
                    $temp['status_lunas'] = $val->status_lunas;
                    $totalPiutang += ($temp['debet'] - $temp['kredit']);
                    $temp['total'] = $totalPiutang;

                    $bayarPiutang = BayarPiutang::join('piutangs', 'bayar_piutangs.piutang_id', 'piutangs.id')
                        ->where('piutangs.tanggal_bayar', $val->tanggal_bayar)
                        ->where('piutang_id', $val->piutang_id)
                        ->get();

                    $temp['bayar_piutang'] = $bayarPiutang;
                    $temp['count'] = count($bayarPiutang);
                    // return $bayarPiutang;

                    // if (count($bayarPiutang) > 0) {
                    //     foreach ($bayarPiutang as $key => $val) {
                    //         $temp['tanggal_transaksi'] = $val->tanggal_bayar;
                    //         $temp['keterangan'] = $val->ket;
                    //         $temp['debet'] = 0;
                    //         $temp['kredit'] = $val->total;
                    //         $totalPiutang += ($temp['debet'] - $temp['kredit']);
                    //         $temp['total'] = $totalPiutang;
                    //         $data[] = $temp;
                    //         $kios->transaksi = $data;
                    //     }
                    // }
                    $data[] = $temp;
                    $kios->transaksi = $data;
                    // return $kios;
                }
            }

            // $bayarPiutang = BayarPiutang::join('kios', 'bayar_piutangs.kios_id', 'kios.id')
            //     ->where('tanggal_bayar', $value)
            //     ->where('kios_id', $kios->id)
            //     ->get();

            // if (count($bayarPiutang) > 0) {
            //     foreach ($bayarPiutang as $key => $val) {
            //         $temp['tanggal_transaksi'] = $val->tanggal_bayar;
            //         $temp['keterangan'] = $val->ket;
            //         $temp['debet'] = 0;
            //         $temp['kredit'] = $val->total;
            //         $totalPiutang += ($temp['debet'] - $temp['kredit']);
            //         $temp['total'] = $totalPiutang;
            //         $data[] = $temp;
            //         $kios->transaksi = $data;
            //     }
            // }
        }
        // return $totalPiutang;

        return view('piutang.detail', compact('page_title', 'page_description', 'breadcrumbs', 'kios', 'totalPiutang'));
    }

    public function bayarPiutang(Request $request)
    {
        $data = '';
        $dataPembayaran = [];
        $dataPembayaran['bulan'] = date('m', strtotime($request->tanggal_bayar));
        $dataPembayaran['tahun'] = date('Y', strtotime($request->tanggal_bayar));

        $kiosId = $request->kios_id;
        $jumlahPembayaran = floatval(preg_replace("/\D/", "", $request->total));

        $transPiutang = Piutang::where('kios_id', $kiosId)
            ->where('status_lunas', '0')
            ->get();
        foreach ($transPiutang as $key => $value) {
            if ($jumlahPembayaran > 0) {
                $sisaPembayaran = $value->total - ($jumlahPembayaran + $value->kredit);

                $data = new BayarPiutang();
                $data->kios_id = $request->kios_id;
                $data->piutang_id = $value->id;
                $data->tanggal_bayar = date('Y-m-d', strtotime($request->tanggal_bayar));
                $data->bulan = $dataPembayaran['bulan'];
                $data->tahun = $dataPembayaran['tahun'];
                $data->ket = $request->keterangan;
                $data->total_bayar = ($jumlahPembayaran >= $value->sisa) ? $value->sisa : floatval(preg_replace("/\D/", "", $jumlahPembayaran));
                $data->save();

                $piutang = Piutang::where('id', $value->id)
                    ->first();
                $piutang->tanggal_bayar = date('Y-m-d', strtotime($request->tanggal_bayar));
                $piutang->kredit = ($sisaPembayaran <= 0) ? $value->total : floatval(preg_replace("/\D/", "", $jumlahPembayaran + $value->kredit));
                $piutang->sisa = ($sisaPembayaran <= 0) ? floatval(preg_replace("/\D/", "", 0)) : floatval(preg_replace("/\D/", "", $sisaPembayaran));
                $piutang->status_lunas = ($sisaPembayaran <= 0) ? '1' : '0';
                $piutang->save();
                $jumlahPembayaran = ($jumlahPembayaran + $value->kredit) - $value->total;
            } else {
                break;
            }
        }

        if ($data) {
            return (new GeneralResponse)->default_json(true, "Success", $data, 201);
        } else {
            return (new GeneralResponse)->default_json(false, "Error", $data, 403);
        }
    }
}
