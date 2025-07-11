@extends('layout.template')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Daftar Penjualan</h4>
                        {{-- <button type="button" class="btn btn-secondary" id="test">Test</button> --}}

                        <div class="mb-3 pull-left">
                            <ul class="pull-left">
                                <li id="">
                                    <div class="input-group input-group-solid">
                                        <input type="text" class="form-control" disabled
                                            aria-label="Text input with dropdown button" value="Bulan">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{ date('F', mktime(0, 0, 0, session('bulan'), 10)) }}</button>
                                            <div class="dropdown-menu dropdown-menu-left" style="">
                                                @php
                                                    $bulan = session()->has('bulan')
                                                        ? (int) session('bulan')
                                                        : (int) date('M');
                                                    //  return $bulan;
                                                @endphp
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <a class="dropdown-item"
                                                        href="{{ route('set-bulan', ['bulan' => $i]) }}">{{ date('F', mktime(0, 0, 0, $i, 10)) }}</a>
                                                @endfor
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm ml-2 btn-edit" id="export"
                                            data-id=""><i class="fa fa-file-excel-o"></i> Export</button>
                                </li>
                            </ul>
                        </div>

                        <div class="mb-3 pull-right">
                            <ul class="pull-right">
                                <li id="">
                                    <h6 class="header-title">Total Penjualan Bulan
                                        {{ date('F', mktime(0, 0, 0, session('bulan'), 10)) }} {{ session('tahun') }} :
                                        {{ number_format($totalPenjualan) }}</h6>
                                </li>
                            </ul>
                        </div>

                        <div class="data-tables">
                            <table id="daftarPenjualanTable" class="text-cente">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th><input type="checkbox" name="checkall" class="select-checkall" id="checkall"
                                                value=""></th>
                                        <th>Nama Kios</th>
                                        <th>invoice</th>
                                        <th>Tanggal Jual</th>
                                        <th>Grand Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- data table end -->
        </div>
    </div>
@endsection

@section('offset-area')
    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="penjualanModal">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body modalDetail">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // datatable produk list
        var dataRow = function() {
            var init = function() {
                let table = $('#daftarPenjualanTable');
                table.DataTable({
                    processing: true,
                    ordering: false,
                    ajax: "{{ route('penjualan-list') }}",
                    columns: [{
                            data: 'id',
                        },
                        {
                            data: 'nama_kios',
                            render: function(data, type, row) {
                                // console.log(row);
                                return `<a href="" class="penjualanShow" data-toggle="modal" data-target="#penjualanModal" data-id="${row.id}">${row.pemilik.toUpperCase()}, ${data.toUpperCase()}, ${row.kabupaten.toUpperCase()}</a>`;

                            }
                        },
                        {
                            data: 'invoice',
                            render: function(data, type, row) {
                                return data.toUpperCase();
                            }
                        },
                        {
                            data: 'tanggal_jual',
                            render: function(data, type, row) {
                                var date = new Date(data);
                                return date.getDate() + '/' + (date.getMonth() + 1) + '/' + date
                                    .getFullYear();
                            }
                        },
                        {
                            data: 'grand_total',
                            render: function(data, type, row) {
                                return number_format(data, 1);
                            }
                        },
                        {
                            data: 'id',
                            render: function(data, type, row, full, meta) {
                                return ``
                                    // <button type="button" class="btn btn-primary btn-sm penjualanShow" data-toggle="modal" data-target="#penjualanModal" data-id="${row.id}"><i class="fa fa-eye"></i></button>
                                    +
                                    `<button type="button" class="btn btn-danger btn-sm btn-edit penjualanDelete" data-id="${row.id}"><i class="fa fa-trash"></i></button>
                        <button type="button" class="btn btn-success btn-sm btn-print penjualanPrint" data-id="${row.id}"><i class="fa fa-print"></i></button>
                        <button type="button" class="btn btn-warning btn-sm btn-edit penjualanEdit" data-id="${row.id}"><i class="fa fa-edit"></i></button>
                        `;
                            }
                        }
                    ],
                    columnDefs: [{
                        targets: 0,
                        'checkboxes': {
                            'selectRow': true
                        },
                        render: function(data, type, row, full, meta) {
                            return `<input type="checkbox" class="select_all" data-id="${row.id}">`;
                        }
                    }, ],
                    select: {
                        style: 'multi'
                    }

                });

            }

            var destroy = function() {
                var table = $('#daftarPenjualanTable').DataTable();
                table.destroy();
            }

            return {
                init: function() {
                    init();
                },
                destroy: function() {
                    destroy();
                }

            }
        }();


        // axiocall
        var AxiosCall = function() {
            return {
                print: function(_url, _data, _element) {
                    axios.post(_url, _data)
                        .then(function(res) {
                            var data = res.data;
                            // console.log(data);
                            // console.log(data.detail_penjualan.length);

                            var html =
                                `<style>
                                                                                                                                @print {
                                                                                                                                    @page :footer {
                                                                                                                                        display: none
                                                                                                                                        }
                                                                                                                                    @page :header {
                                                                                                                                        display: none
                                                                                                                                        }
                                                                                                                                    }

                                                                                                                                    #item tr th,
                                                                                                                                    #item tr td{
                                                                                                                                        border-top: 1px solid black;
                                                                                                                                        border-right: 1px solid black;
                                                                                                                                        border-bottom: 1px solid black;
                                                                                                                                        border-left: 1px solid black;
                                                                                                                                        }
                                                                                                                                    #item {
                                                                                                                                        border-collapse: collapse;
                                                                                                                                        }
                                                                                                                                    .empty-list td{
                                                                                                                                    height: 20px !important;
                                                                                                                                    }

                                                                                                                                    .flex-container {
                                                                                                                                        display: flex;
                                                                                                                                        height: 200px;
                                                                                                                                        flex-direction: row;
                                                                                                                                        text-align: left;
                                                                                                                                        }

                                                                                                                                    .flex-container h4 {
                                                                                                                                        font-size: 20px;
                                                                                                                                        margin-top:15px;
                                                                                                                                        margin-bootom:0;
                                                                                                                                        text-transform: uppercase;
                                                                                                                                        }

                                                                                                                                    .flex-container p {
                                                                                                                                        margin: 0;
                                                                                                                                        }

                                                                                                                                    .flex-container span {
                                                                                                                                        margin-top: 100px;
                                                                                                                                        }

                                                                                                                                    .flex-item-left {
                                                                                                                                    /* background-color: #f1f1f1; */
                                                                                                                                    padding: 10px;
                                                                                                                                    flex: 50%;
                                                                                                                                    }

                                                                                                                                    .flex-item-right {
                                                                                                                                        /* background-color: dodgerblue; */
                                                                                                                                        padding: 12px;
                                                                                                                                        flex: 50%;
                                                                                                                                        }

                                                                                                                                    /* Responsive layout - makes a one column-layout instead of two-column layout */
                                                                                                                                    // @media (max-width: 800px) {
                                                                                                                                    // .flex-container {
                                                                                                                                    //     flex-direction: column;
                                                                                                                                    // }
                                                                                                                                    }
                                                                                                                                    </style>

                                                                                                                                    <table id="" class="text-cente" style="width: 100%;">
                                                                                                                                        <tr>
                                                                                                                                            <td colspan="6" style="font-size: 30px;">CV. AYYUB TANI</td>
                                                                                                                                            <td colspan="3">Jeneponto, ` +
                                tanggal_jual +
                                `</td>
                                                                                                                                        </tr>
                                                                                                                                        <tr>
                                                                                                                                            <td colspan="6" style="padding-bottom: 5px;">SALAMATARA, KARELOE BONTORAMBA JENEPONTO</td>
                                                                                                                                            <td colspan="3" style="padding-bottom:5px">Kepada Yth,</td>
                                                                                                                                        </tr>
                                                                                                                                        <tr>
                                                                                                                                            <td colspan="2">NO. INVOICE </td>
                                                                                                                                            <td colspan="4">: ` +
                                data
                                .invoice
                                .toUpperCase() +
                                `</td>
                                                                                                                                            <td colspan="3">` +
                                data
                                .kios
                                .nama_kios
                                .toUpperCase() +
                                `</td>
                                                                                                                                        </tr>
                                                                                                                                        <tr>
                                                                                                                                            <td colspan="2">PEMBAYARAN </td>
                                                                                                                                            <td colspan="4">: ` +
                                data
                                .pembayaran
                                .toUpperCase() +
                                `</td>
                                                                                                                                            <td colspan="3">` +
                                data
                                .kios
                                .alamat
                                .toUpperCase() +
                                `, ` +
                                data
                                .kios
                                .kabupaten.toUpperCase() +
                                `</td>
                                                                                                                                        </tr>
                                                                                                                                        <tr>
                                                                                                                                            <td colspan="2">JATUH TEMPO</td>
                                                                                                                                            <td colspan="4">: ` +
                                data
                                .jatuh_tempo +
                                `</td>
                                                                                                                                            <td colspan="3">SULAWESI SELATAN</td>
                                                                                                                                        </tr>
                                                                                                                                        <tr>
                                                                                                                                            <td colspan="2" style="width: 2%;"></td>
                                                                                                                                            <td colspan="4"></td>
                                                                                                                                            <td>NPWP</td>
                                                                                                                                            <td colspan="2">: ` +
                                data
                                .kios
                                .npwp +
                                `</td>
                                                                                                                                        </tr>
                                                                                                                                        <tr>
                                                                                                                                            <td colspan="2" style="padding-top:-20px;padding-bottom: 5px;width: 2%;"></td>
                                                                                                                                            <td colspan="4" style="width: 55%;padding-top:-20px;padding-bottom: 5px;"></td>
                                                                                                                                        </tr>
                                                                                                                                    </table>
                                                                                                                                    
                                                                                                                                    <table id="item" width="100%">
                                                                                                                                        <tr class="">
                                                                                                                                            <th style="width: 1%;">No.</th>
                                                                                                                                            <th colspan="2" style="width: 50%; !important">Nama Produk</th>
                                                                                                                                            <th style="width: 5%;">Qty</th>
                                                                                                                                            <th style="width: 1%; !important">Stn.</th>
                                                                                                                                            <th style="width: 10%;">Harga</th>
                                                                                                                                            <th style="width: 5%;">Ket.</th>
                                                                                                                                            <th style="width: 5%;">Disc.</th>
                                                                                                                                            <th style="width: 10%;">Jumlah</th>
                                                                                                                                        </tr>
                                                                                                                                        `;
                            data.detail_penjualan.map(function(value, index) {

                                no = index + 1;
                                html +=
                                    `<tr class="">
                                                                                                                                <td style="width: 1%;text-align:center">` +
                                    no +
                                    `</td>
                                                                                                                                <td colspan="2" style="width: 43%;">` +
                                    value
                                    .nama_produk
                                    .toUpperCase() +
                                    " " +
                                    value.kemasan.toUpperCase() +
                                    `</td>
                                                                                                                                <td style="width: 5%;text-align:center">` +
                                    value
                                    .qty +
                                    `</td>
                                                                                                                                <td style="width: 5%;text-align:center">` +
                                    value
                                    .satuan
                                    .toUpperCase() +
                                    `</td>
                                                                                                                                <td style="width: 10%;text-align:right">` +
                                    number_format(
                                        value
                                        .harga_perdos,
                                        1) +
                                    `</td>
                                                                                                                                <td style="width: 5%;text-align:center">` +
                                    value
                                    .ket
                                    .toUpperCase() +
                                    `</td>
                                                                                                                                <td style="width: 5%;text-align:center">` +
                                    value
                                    .disc +
                                    `</td>
                                                                                                                                <td style="width: 10%;text-align:right">` +
                                    value
                                    .jumlah +
                                    `</td>
                                                                                                                                </tr>
                                                                                                                            `;
                            });

                            for (let index = 0; index < (16 - data.detail_penjualan.length); index++) {
                                html +=
                                    `<tr class="empty-list" style="line-height:15px;">
                                                                                                                            <td style="width: 1%;text-align:center;"></td>
                                                                                                                            <td colspan="2" style="width: 43%;"></td>
                                                                                                                            <td style="width: 8%;text-align:center"></td>
                                                                                                                            <td style="width: 5%;text-align:center"></td>
                                                                                                                            <td style="width: 10%;text-align:right"></td>
                                                                                                                            <td style="width: 10%;text-align:center"></td>
                                                                                                                            <td style="width: 5%;text-align:center"></td>
                                                                                                                            <td style="width: 12%;text-align:right"></td>
                                                                                                                        </tr>`;
                            }

                            html +=
                                `</table>
                                                                                                                     <table width="30%" style="float:right;margin-top: -1px;">
                                                                                                                        <tr style="outline: thin solid black;">
                                                                                                                            <td style="width:30%;">DPP </td>
                                                                                                                            <td style="width:1%;">:</td>
                                                                                                                            <td class="dpp" style="width:10%;text-align: right;">` +
                                data
                                .dpp +
                                `</td>
                                                                                                                        </tr>
                                                                                                                        <tr style="outline: thin solid black;">
                                                                                                                            <td style="width: 30%;">PPN</td>
                                                                                                                            <td style="width:1%;">:</td>
                                                                                                                            <td class="ppn" style="width:10%;text-align: right;">` +
                                data
                                .ppn +
                                `</td>
                                                                                                                        </tr>
                                                                                                                        <tr style="outline: thin solid black;">
                                                                                                                            <td style="width: 30%;">Discount</td>
                                                                                                                            <td style="width:1%;">:</td>
                                                                                                                            <td class="disc" style="width:10%;text-align: right;">` +
                                data
                                .total_disc +
                                `</td>
                                                                                                                        </tr>
                                                                                                                        <tr style="outline: thin solid black;">
                                                                                                                            <th style="width: 30%;text-align:left;">GRAND TOTAL</th>
                                                                                                                            <th style="width:1%;">:</th>
                                                                                                                            <th class="grand_total" style="width:10%;text-align: right;">` +
                                data
                                .grand_total +
                                `</th>
                                                                                                                        </tr>
                                                                                                                    </table>`;

                            html +=
                                `<div class="flex-container">
                                                                                                                        <div class="flex-item-left">
                                                                                                                            <h4>transfer ke rekening:</h4>
                                                                                                                            <p>CV. AYYUB TANI</p>
                                                                                                                            <p>BANK BRI : 025201001055304</p>
                                                                                                                            <p style="margin-top: 20px; text-align:center;">Tanda terima</p>
                                                                                                                            <p style="margin-top: 50px; text-align:center;">_____________</p>
                                                                                                                        </div>
                                                                                                                        <div class="flex-item-right">
                                                                                                                            <p style="margin-top: 120px; text-align:center;">Hormat Kami</p>
                                                                                                                            <p style="margin-top: 50px; text-align:center;">_____________</p>
                                                                                                                            </div>
                                                                                                                        </div>`;

                            var document_focus = false;
                            var popupWin = window.open('', '_blank', 'width=500,height=500');
                            popupWin.document.open();
                            popupWin.document.write(
                                '<html><body onload="document.title=`Ayyub Tani`;">' +
                                html + '</html>');

                            var document_focus = false; // var we use to monitor document focused status.
                            // Now our event handlers.
                            $(document).ready(function() {
                                popupWin.window.print();
                                document_focus = true;
                            });
                            setInterval(function() {
                                if (document_focus === true) {
                                    popupWin.window.close();
                                }
                            }, 250);


                        }).catch(function(error) {
                            // console.log(error);
                            swal.fire({
                                text: "Pilih kios, masukkan invoice dan pilih pembayaran terlebih dahulu",
                                title: "Error",
                                icon: "error",
                                showConfirmButton: true,
                                confirmButtonText: "OK",
                            })
                        });
                }
            };
        }();




        // show penjualan
        $(document).on('click', '.penjualanShow', function() {

            var key = $(this).data('id');
            axios.get('/penjualan/show/' + key)
                .then(function(res) {
                    let data = res.data;
                    // console.log(data);

                    let date = new Date(data.penjualan.tanggal_jual);
                    let tanggal_jual = date.getDate() + '/' + (date.getMonth() + 1) + '/' + date
                        .getFullYear();

                    let element =
                        `<table id="" class="text-cente" style="width: 100%;">
                            <tr>
                                <td colspan="6" style="font-size: 30px;">` +
                        data.kios.pemilik.toUpperCase() + `, ` +
                        data.kios.nama_kios.toUpperCase() +
                        `</td>
                        <td colspan="3">Jeneponto, ` +
                        tanggal_jual +
                        `</td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding-bottom: 20px;">
                        ` +
                        data.kios.alamat.toUpperCase() +
                        ` ` +
                        data.kios.kabupaten.toUpperCase() +
                        `</td>
                            <td colspan="3">` +
                        data.kios.npwp.replace(/\D/g, '') +
                        `</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top:-20px;padding-bottom: 20px;">NO. INVOICE </td>
                            <td colspan="4" style="padding-top:-20px;padding-bottom: 20px;">
                            : ` +
                        data.penjualan.invoice.toUpperCase() +
                        `</td>
                            <td colspan="3">` +
                        data.kios.nik +
                        `</td>
                    </tr>
                </table>
                <table id="item" class="table-hover">
                    <tr class="">
                        <th style="width: 1%;">No.</th>
                        <th colspan="2" style="width: 30%;">Nama Produk</th>
                        <th style="width: 10%;text-align: right;">DPP Lain</th>
                        <th style="width: 10%;text-align: right;">Harga Satuan</th>
                        <th style="width: 5%;text-align: right;">Ket.</th>
                        {{-- <th style="width: 5%;text-align: right;">Disc.</th> --}}
                        <th style="width: 10%;text-align: right;">Jumlah</th>
                    </tr>`;
                    data.detailPenjualan.map(function(value, index) {
                        no = index + 1;
                        element +=
                            `<tr class="">
                                <td style="width: 1%;">` +
                            no +
                            `</td>
                                <td colspan="2" style="width: 48%;">
                                    ` +
                            value.nama_produk.toUpperCase() +
                            ` ` +
                            value.kemasan_produk.toUpperCase() +
                            `</td>
                            <td style="width: 10%;text-align: right;">` +
                            number_format(value.dppLain, 2) +
                            `</td>
                            <td style="width: 10%;text-align: right;">` +
                            number_format(value.harga_jual, 3) +
                            `</td>
                                <td style="width: 5%;text-align: right;">` +
                            value.ket.toUpperCase() +
                            `</td>
                                {{-- <td style="width: 5%;text-align: right;">` +
                            number_format(value.disc, 1) +
                            `</td> --}}
                                <td style="width: 10%;text-align: right;">` +
                            number_format(value.jumlah) +
                            `</td>
                            </tr>`;
                    });

                    element +=
                        `</table>
                <table width="50%" style="float:right;margin-top: 10px;">
                    <tr>
                        <td style="width:20%;">DPP </td>
                        <td style="width:1%;">:</td>
                        <td class="dpp" style="width:20%;text-align: right;">` +
                        number_format(data.penjualan.dpp, 1) +
                        `</td>
                    </tr>
                    <tr>
                        <td style="width:1%;">PPN</td>
                        <td style="width: 20%;">:</td>
                        <td class="ppn" style="width:20%;text-align: right;">` +
                        number_format(data.penjualan.ppn, 1) +
                        `</td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">Discount</td>
                        <td style="width:1%;">:</td>
                        <td class="disc" style="width:20%;text-align: right;">` +
                        number_format(data.penjualan.total_disc, 1) +
                        `</td>
                    </tr>
                    <tr>
                        <th style="width: 20%;">GRAND TOTAL</th>
                        <th style="width:1%;">:</th>
                        <th class="grand_total" style="width:20%;text-align: right;font-weight:bold">` +
                        number_format(data.penjualan.grand_total, 1) +
                        `</th>
                    </tr>
                </table>`;
                    $('.modalDetail').children().remove();
                    $('.modalDetail').append(element);
                }).catch(function(err) {

                });
        });

        $(document).on('click', '.penjualanPrint', function(e) {
            var key = $(this).data('id');
            console.log(key);
            axios.get('/penjualan/print?id=' + key)
                .then(function(res) {
                    let data = res.data[0];
                    let date = new Date(data.tanggal_jual);
                    let tanggal_jual = date.getDate() + '/' + (date.getMonth() + 1) + '/' + date
                        .getFullYear();
                    // console.log(data);
                    // console.log(data.kios.nama_kios);
                    // console.log(tanggal_jual);

                    var html =
                        `<style>
                            @print {
                                @page :footer {
                                    display: none
                                }

                                @page :header {
                                    display: none
                                }
                            }

                            body {
                                font-family: Verdana, sans-serif;
                            }

                            #item {
                                border-collapse: collapse;
                            }

                            .empty-list td {
                                height: 20px !important;
                            }

                            .flex-container {
                                display: flex;
                                height: 200px;
                                flex-direction: row;
                                text-align: left;
                            }

                            .flex-container h4 {
                                font-size: 20px;
                                margin-top: 15px;
                                margin-bootom: 0;
                                text-transform: uppercase;
                            }

                            .flex-container p {
                                margin: 0;
                            }

                            .flex-container span {
                                margin-top: 100px;
                            }

                            .flex-item-left {
                                /* background-color: #f1f1f1; */
                                padding: 10px;
                                flex: 50%;
                            }

                            .flex-item-right {
                                /* background-color: dodgerblue; */
                                padding: 12px;
                                flex: 50%;
                            }

                            /* Responsive layout - makes a one column-layout instead of two-column layout */
                            // @media (max-width: 800px) {
                            // .flex-container {
                            //     flex-direction: column;
                            // }
                            }
                        </style>
                        
                        <table id="table-header" class="text-cente" style="width: 100%;">
                            <tr>
                                <td colspan="6" style="font-size: 30px;">
                                    <img src="{{ asset('images/author/newAt.png') }}" width="10%" height="25px">
                                    CV. AYYUB TANI
                                    <img src="{{ asset('images/author/dgw.png') }}" width="10%">
                                </td>
                                <td colspan="3">Jeneponto, ` + tanggal_jual + `</td>
                            </tr>
                            <tr>
                                <td colspan="6" style="padding-bottom: 5px;">SALAMATARA, KARELOE BONTORAMBA JENEPONTO
                                    <br>
                                </td>
                                <td colspan="3" style="padding-bottom:5px">TUAN/TOKO,</td>
                            </tr>
                            <tr>
                                <td colspan="3">NO. NOTA </td>
                                <td colspan="3">: ` +
                        data.invoice.toUpperCase() + `
                                </td>
                                <td colspan="3">` +
                        data.kios.pemilik.toUpperCase() + `, ` + data.kios.nama_kios.toUpperCase() +
                        `</td>
                            </tr>
                            <tr>
                                <td colspan="3">PEMBAYARAN </td>
                                <td colspan="3">: ` +
                        data.pembayaran.toUpperCase() +
                        `</td>
                                <td colspan="3" rowspan="2">` +
                        data.kios.alamat.toUpperCase() + `, ` +
                        data.kios.kabupaten.toUpperCase() +
                        `</td>
                            </tr>
                            <tr>
                                <td colspan="3">JATUH TEMPO</td>
                                <td colspan="3">: ` +
                        data.jatuh_tempo +
                        `</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding-top:-20px;padding-bottom: 5px;width: 2%;"></td>
                                <td colspan="3" style="width: 55%;padding-top:-20px;padding-bottom: 5px;"></td>
                            </tr>
                        </table>
                        <table id="item" width="100%">
                            <tr class="" style="border-top: 1px solid black;border-bottom: 1px solid black;">
                                <th style="width: 1%;">No.</th>
                                <th colspan="2" style="width: 50%; !important">Nama Produk</th>
                                <th style="width: 5%;">Ket.</th>
                                <th style="width: 5%;">Qty</th>
                                <th style="width: 1%; !important">Stn.</th>
                                <th style="width: 10%;">Harga</th>
                                <th style="width: 5%;">Disc.</th>
                                <th style="width: 10%;">Jumlah</th>
                            </tr>`;

                    data.detail_penjualan.map(function(value, index) {
                        console.log(value);
                        no = index + 1;
                        html += `
                        <tr class="">
                            <td style="width: 1%;text-align:center">` +
                            no +
                            `</td>
                            <td colspan="2" style="width: 43%;">` +
                            value.nama_produk.toUpperCase() + " " + value.kemasan
                            .toUpperCase() +
                            `</td>
                            <td style="width: 5%;text-align:center">` +
                            value.ket.toUpperCase() +
                            `</td>
                            <td style="width: 5%;text-align:right">` +
                            value.qty +
                            `</td>
                            <td style="width: 5%;text-align:center">` +
                            value.satuan.toUpperCase() +
                            `</td>
                            <td style="width: 10%;text-align:right">` +
                            number_format(
                                value.harga_jual, 0) +
                            `</td>
                            <td style="width: 5%;text-align:center">` +
                            number_format(
                                value.disc, 0) +
                            `</td>
                            <td style="width: 10%;text-align:right">` +
                            number_format(
                                value.jumlah, 0) +
                            `</td>
                        </tr>`;
                    });

                    for (let index = 0; index < (9 - data.detail_penjualan.length); index++) {
                        html +=
                            `<tr class="empty-list" style="line-height:15px;">
                                <td style="width: 1%;text-align:center;"></td>
                                <td colspan="2" style="width: 43%;"></td>
                                <td style="width: 8%;text-align:center"></td>
                                <td style="width: 5%;text-align:center"></td>
                                <td style="width: 10%;text-align:right"></td>
                                <td style="width: 10%;text-align:center"></td>
                                <td style="width: 5%;text-align:center"></td>
                                <td style="width: 12%;text-align:right"></td>
                            </tr>`;
                    }

                    html += `
                    <tr class="empty-list" style="line-height:15px; border-bottom: 1px solid black">
                        <td style="width: 1%;text-align:center;"></td>
                        <td colspan="2" style="width: 43%;"></td>
                        <td style="width: 8%;text-align:center"></td>
                        <td style="width: 5%;text-align:center"></td>
                        <td style="width: 10%;text-align:right"></td>
                        <td style="width: 10%;text-align:center"></td>
                        <td style="width: 5%;text-align:center"></td>
                        <td style="width: 12%;text-align:right"></td>
                    </tr>
                    </table>
                    <table width="30%" style="float:right;margin-top: -1px;">
                        <tr style="">
                            <th style="width: 30%;text-align: left;">GRAND TOTAL</th>
                            <th style="width:1%;">:</th>
                            <th class="grand_total" style="width:10%;text-align: right;">` +
                        number_format(data.grand_total, 0) +
                        `</th>
                        </tr>
                        <tr style="">
                            <td style="width: 30%;text-align:left;">DPP</td>
                            <td style="width:1%;">:</td>
                            <td class="grand_total" style="width:10%;text-align: right;">` +
                        number_format(data.dpp, 0) +
                        `</td>
                        </tr>
                        <tr style="">
                            <td style="width: 30%;">PPN</td>
                            <td style="width:1%;">:</td>
                            <td class="ppn" style="width:10%;text-align: right;">` +
                        number_format(data.ppn, 0) +
                        `</td>
                        </tr>
                        <tr style="">
                            <th style="width: 30%;text-align:left;"></th>
                            <th style="width:1%;"></th>
                            <th class="grand_total" style="width:10%;text-align: right;"></th>
                        </tr>
                    </table>`;

                    html += `
                    <div class="flex-container">
                        <div class="flex-item-left">
                            <h4>transfer ke rekening:</h4>
                            <p>CV. AYYUB TANI</p>
                            <p>BANK BRI : 025201001055304</p>
                            <p style="margin-top: 20px; text-align:center;">Tanda terima</p>
                            <p style="margin-top: 50px; text-align:center;">_____________</p>
                        </div>
                        <div class="flex-item-right">
                            <p style="margin-top: 120px; text-align:center;">Hormat Kami</p>
                            <p style="margin-top: 50px; text-align:center;">_____________</p>
                        </div>
                    </div>`;

                    var document_focus = false;
                    var popupWin = window.open('', '_blank', 'width=1000%,height=500');
                    popupWin.document.open();
                    popupWin.document.write(
                        '<html><body onload="document.title=`Ayyub Tani`;">' +
                        html + '</html>');

                    var document_focus = false; // var we use to monitor document focused status.
                    // Now our event handlers.
                    $(document).ready(function() {
                        popupWin.window.print();
                        document_focus = true;
                    });
                    setInterval(function() {
                        if (document_focus === true) {
                            popupWin.window.close();
                        }
                    }, 250);


                })
                .catch(function(err) {

                });
        });

        $(document).on('click', '.penjualanDelete', function(e) {
            e.preventDefault()
            let id = $(this).attr('data-id');
            // console.log(id);
            Swal.fire({
                title: 'Apakah kamu yakin akan menghapus data ini ?',
                text: "Data akan di hapus permanen",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#preloader').show();
                    $.ajax({
                        url: `/penjualan/delete/${id}`,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#preloader').hide();
                            if (response.status !== false) {
                                Swal.fire('Deleted!',
                                        'Data berhasil dihapus.',
                                        'success')
                                    .then(function() {
                                        dataRow.destroy();
                                        dataRow.init();
                                    });
                            } else {
                                swal.fire({
                                    title: "Failed!",
                                    text: `${res.message}`,
                                    icon: "warning",
                                });
                            }
                        }
                    })
                }
            })
        });

        $(document).on('click', '.penjualanEdit', function(e) {
            e.preventDefault()
            let id = $(this).attr('data-id');
            console.log(id);
            Swal.fire({
                    title: 'Apakah kamu yakin akan mengedit data ini ?',
                    text: "Data akan diedit!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, edit!'
                })
                .then((result) => {
                    $('#preloader').show();
                    $.ajax({
                        url: `/penjualan/edit/${id}`,
                        type: 'GET',
                        data: {
                            '_method': 'GET',
                            '_token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#preloader').hide();
                            window.location.href = `/penjualan/edit/${id}`;
                        }
                    })
                })
        });

        // export
        $(document).on('click', '#export', function() {
            var data = [];
            $('.select_all').each(function(i, v) {
                if ($(v).prop('checked')) {
                    var dataCheck = $(v).data('id');
                    data.push(dataCheck);

                }
            });

            url = `/penjualan/export/?jenis=excel&data=${data}`;
            window.open(url);
        });

        //Selecting all checkboxes that are not disabled
        $("#checkall").on('click', function(e, v) {
            var data = [];
            $('#daftarPenjualanTable')
                .find('input[type="checkbox"]:enabled:visible')
                .prop('checked', this.checked);
        });


        function number_format(number, decimals, decPoint, thousandsSep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
            var n = !isFinite(+number) ? 0 : +number
            var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
            var sep = (typeof thousandsSep === 'undefined') ? '.' : thousandsSep
            var dec = (typeof decPoint === 'undefined') ? ',' : decPoint
            var s = ''

            var toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec)
                return '' + (Math.round(n * k) / k)
                    .toFixed(prec)
            }

            // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || ''
                s[1] += new Array(prec - s[1].length + 1).join('0')
            }

            return s.join(dec)
        }


        $(document).ready(function() {
            dataRow.init();

        });
    </script>
@endsection
