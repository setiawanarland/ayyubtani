@extends('layout.template')

@section('styles')
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            /* display: none; <- Crashes Chrome on hover */
            -webkit-appearance: none;
            margin: 0;
            /* <-- Apparently some margin are still there even though it's hidden */
        }

        input[type=number] {
            -moz-appearance: textfield;
            /* Firefox */
        }
    </style>
@endsection

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Pembelian</h4>
                        <form action="" data-type="submit">
                            @csrf
                            <div class="form-row align-items-center">

                                <div class="col-sm-5 my-1">
                                    <label class="" for="produk">Produk</label>
                                    <select class="form-control" id="produk" name="produk">
                                        <option value="null">Pilih Produk</option>
                                        @foreach ($produk as $index => $value)
                                            <option value="{{ $value->id }}">
                                                {{ Str::upper($value->nama_produk) }} {{ Str::upper($value->kemasan) }} |
                                                Stok {{ $value->stok }} dos
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 col-md-1 my-1">
                                    <label class="" for="qty">Qty</label>
                                    <input type="number" class="form-control qty-jual" id="ket" name="ket"
                                        value="0" min="0">
                                </div>
                                <div class="col-sm-3 col-md-1 my-1">
                                    <label class="" for="disc">Disc</label>
                                    <input type="number" class="form-control" id="disc" name="disc" value="0"
                                        min="0">
                                </div>
                                <div class="col-auto my-1" style="padding-top: 30px;">
                                    <button type="button" class="btn btn-success btn-xs addTemp">
                                        <i class="fa fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- data table end -->
        </div>
        <div class="row">
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Detail Penjualan</h4>

                        <button type="button" class="btn d-flex btn-danger mb-3 pull-right tempReset">Reset Keranjang
                        </button>

                        <form id="penjualanForm" action="" data-type="submit">
                            <div class="form-row align-items-center">
                                <div class="col-sm-3 col-md-3 my-3">
                                    <label class="" for="kios">Kios</label>
                                    <select class="form-control" id="kios" name="kios">
                                        <option value="null">Pilih Kios</option>
                                        @foreach ($kios as $index => $value)
                                            <option value="{{ $value->id }}">{{ Str::upper($value->nama_kios) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 col-md-3 my-3">
                                    <label class="" for="invoice">Invoice</label>
                                    <input type="text" class="form-control" id="invoice" name="invoice"
                                        value="{{ $invoice }}">
                                </div>
                                <div class="col-sm-3 col-md-3 my-3">
                                    <label class="" for="tanggal_jual">Tanggal Jual</label>
                                    <input type="text" class="form-control" id="tanggal_jual" name="tanggal_jual">
                                </div>
                            </div>

                            <div class="data-tables">
                                <table id="detailPenjualanTable" class="text-cente">
                                    <thead class="bg-light text-capitalize">
                                        <tr>
                                            <th>No.</th>
                                            <th width="25%">Nama Produk</th>
                                            <th width="10%">Qty</th>
                                            <th width="10%">Satuan</th>
                                            <th width="15%">Harga Stn. <br> ({{ $pajak->nama_pajak }})</th>
                                            <th width="10%">Ket.</th>
                                            <th width="8%">Disc.</th>
                                            <th width="15%">Jumlah</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="data-tables pull-right">
                                <table>
                                    <thead>
                                        <tr>
                                            <td>DPP</td>
                                            <td style="padding-left:130px; padding-right:3px;">:</td>
                                            <td class="dpp">
                                                <input type="text" class="form-control" id="dpp" name="dpp"
                                                    value="">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>PPN</td>
                                            <td style="padding-left:130px; padding-right:3px;">:</td>
                                            <td class="ppn">
                                                <input type="text" class="form-control" id="ppn" name="ppn"
                                                    value="">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Discount</td>
                                            <td style="padding-left:130px; padding-right:3px;">:</td>
                                            <td class="disc">
                                                <input type="text" class="form-control" id="total_disc"
                                                    name="total_disc" value="">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>GRAND TOTAL</th>
                                            <th style="padding-left:130px; padding-right:3px;">:</th>
                                            <th class="grand_total">
                                                <input type="text" class="form-control" id="grand_total"
                                                    name="grand_total" value="">
                                            </th>
                                        </tr>
                                    </thead>
                                </table>

                                <div class="form-group" style="margin-top: 10px;">
                                    <button class="btn btn-primary" type="submit">Save</button>
                                    <button class="btn btn-danger btn-cancel previewPenjualan"
                                        type="">Print</button>
                                </div>
                            </div>

                    </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var dpp;
        var satuan_pajak;
        var ppn;
        var total_disc;
        var grand_total;
        // datatable detail pembelian
        var dataRow = function() {
            var init = function() {
                let table = $('#detailPenjualanTable');
                table.DataTable({
                    processing: true,
                    ordering: false,
                    paging: false,
                    searching: false,
                    lengthChange: false,
                    info: false,
                    ajax: {
                        url: "{{ route('penjualantemp-list') }}",
                        dataSrc: function(data) {
                            dpp = 0;
                            ppn = 0;
                            grand_total = 0;
                            data.data.map(function(data) {
                                grand_total += data.jumlah;
                                satuan_pajak = data.satuan_pajak;
                            });
                            dpp = Math.round(grand_total / 1.1);
                            ppn = Math.round(grand_total - dpp);
                            total_disc = 0;
                            $('#dpp').val(formatRupiah(dpp.toString(), ''));
                            $('#ppn').val(formatRupiah(ppn.toString(), ''));
                            $('#total_disc').val(formatRupiah(total_disc.toString(), ''));
                            $('#grand_total').val(formatRupiah(grand_total.toString(), ''));
                            return data.data;
                        },
                    },
                    columns: [{
                            data: 'id',
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        {
                            data: 'produk_id',
                            render: function(data, type, row) {
                                return `
                                <input type="hidden" name="produk_id[]" value="` + data +
                                    `">
                                <input type="text" class="form-control" id="nama_produk" name="nama_produk[]" value="` +
                                    row.nama_produk.toUpperCase() + ", " + row.kemasan
                                    .toUpperCase() + `">`;
                            }
                        },
                        {
                            data: 'qty',
                            render: function(data, type, row) {
                                return `
                                    <input type="text" class="form-control qty" id="qty" name="qty[]" value="` + data + `">
                                `;
                            }
                        },
                        {
                            data: 'satuan',
                            render: function(data, type, row) {
                                return `
                                    <input type="text" class="form-control satuan" id="satuan" name="satuan[]" value="` +
                                    data.toUpperCase() + `">
                                `;
                            }
                        },
                        {
                            data: 'harga_jual',
                            render: function(data, type, row) {
                                return `
                                    <input type="text" class="form-control harga_beli" id="harga_beli" name="harga_beli[]" value="` +
                                    formatRupiah(data.toString(), '') + `">
                                `;
                            }
                        },
                        {
                            data: 'ket',
                            render: function(data, type, row) {
                                return `
                                <input type="text" class="form-control ket" id="ket" name="ket[]" value="` +
                                    data.toUpperCase() + " Dos" +
                                    `">
                                `;
                            }
                        },
                        {
                            data: 'disc',
                            render: function(data, type, row) {
                                return `
                                <input type="text" class="form-control disc" id="disc" name="disc[]" value="` +
                                    data + `">
                                `;
                            }
                        },
                        {
                            data: 'jumlah',
                            render: function(data, type, row) {

                                return `
                                    <input type="text" class="form-control jumlah" id="jumlah" name="jumlah[]" value="` +
                                    formatRupiah(data.toString(), '') + `">
                                `;
                            }
                        },
                        {
                            data: 'id'
                        }
                    ],
                    columnDefs: [{
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        width: '5rem',
                        class: "wrapok",
                        render: function(data, type, row, full, meta) {
                            return `
                        <button type="button" class="btn btn-danger btn-sm btn-delete tempDelete" data-id="${row.id}"><i class="fa fa-close"></i></button>
                `;
                        },
                    }],
                });

            };

            var destroy = function() {
                var table = $('#detailPenjualanTable').DataTable();
                table.destroy();
            };

            return {
                init: function() {
                    init();

                },
                destroy: function() {
                    destroy();
                }

            };
        }();

        // axiocall
        var AxiosCall = function() {
            return {
                post: function(_url, _data, _element) {
                    axios.post(_url, _data)
                        .then(function(res) {
                            var data = res.data;
                            console.log(data);
                            if (data.fail) {
                                swal.fire({
                                    text: "Maaf Terjadi Kesalahan",
                                    title: "Error",
                                    timer: 2000,
                                    icon: "danger",
                                    showConfirmButton: false,
                                });
                            } else if (data.invalid) {
                                console.log(data);
                                $.each(data.invalid, function(key, value) {
                                    console.log(key);
                                    console.log('errorType', typeof error);
                                    $("input[name='" + key + "']").addClass('is-invalid').siblings(
                                        '.invalid-feedback').html(value[0]);
                                });
                            } else if (data.success) {
                                swal.fire({
                                    text: "Data anda berhasil disimpan",
                                    title: "Sukses",
                                    icon: "success",
                                    showConfirmButton: true,
                                    confirmButtonText: "OK, Siip",
                                }).then(function() {
                                    dataRow.destroy();
                                    dataRow.init();
                                    window.location = "{{ route('daftar-penjualan') }}";
                                });
                            }
                        }).catch(function(error) {
                            console.log(error);
                            swal.fire({
                                text: "Maaf Terjadi Kesalahan",
                                title: "Error",
                                timer: 2000,
                                icon: "danger",
                                showConfirmButton: false,
                            })
                        });
                },
                update: function(_url, _data, _element) {
                    console.log(_url);
                    console.log(_data);
                    console.log(_element);
                    axios.post(_url, _data)
                        .then(function(res) {
                            var data = res.data;
                            console.log(data);
                            if (data.failed) {
                                swal.fire({
                                    text: "Maaf Terjadi Kesalahan",
                                    title: "Error",
                                    timer: 2000,
                                    icon: "danger",
                                    showConfirmButton: false,
                                });
                            } else if (data.invalid) {
                                $.each(data.invalid, function(key, value) {
                                    console.log(key);
                                    $("input[name='" + key + "']").addClass('is-invalid').siblings(
                                        '.invalid-feedback').html(value[0]);
                                });
                            } else if (data.success) {
                                swal.fire({
                                    text: "Data anda berhasil disimpan",
                                    title: "Sukses",
                                    icon: "success",
                                    showConfirmButton: true,
                                    confirmButtonText: "OK, Siip",
                                }).then(function() {
                                    $('.offset-area').toggleClass('show_hide');
                                    $('.settings-btn').toggleClass('active');
                                    var form = $('#produkForm');
                                    form[0].reset();
                                    dataRow.destroy();
                                    dataRow.init();
                                });
                            }
                        }).catch(function(res) {
                            var data = res.data;
                            console.log(data);
                            swal.fire({
                                text: "Terjadi Kesalahan Sistem",
                                title: "Error",
                                icon: "error",
                                showConfirmButton: true,
                                confirmButtonText: "OK",
                            })
                        });
                },
                print: function(_url, _data, _element) {
                    axios.post(_url, _data)
                        .then(function(res) {
                            var data = res.data;
                            console.log(data.produks.length);

                            var html = `
                                        <style>
                                            body {
                                                width: 8.5in;
                                            }
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
                                                margin-top: 5px;
                                                text-transform: uppercase;
                                            }

                                            .flex-container p {
                                                margin: 0;
                                            }

                                            .flex-container span {
                                                margin-top: 50px;
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
                                            @media (max-width: 800px) {
                                            .flex-container {
                                                flex-direction: column;
                                            }
                                            }
                                        </style>

                                        <table id="" class="text-cente" style="width: 100%;">
                                            <tr>
                                                <td colspan="6" style="font-size: 30px;">
                                                    CV. AYYUB TANI
                                                </td>
                                                <td colspan="3">
                                                    Jeneponto, ` + data.tanggal_jual + `
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" style="padding-bottom: 0px;">SALAMATARA, KARELOE BONTORAMBA JENEPONTO</td>
                                                <td colspan="3" style="padding-bottom:0px">Kepada Yth,</td>
                                            </tr>
                                            

                                            <tr>
                                                <td colspan="2">NO. INVOICE </td>
                                                <td colspan="4">: ` + data.invoice.toUpperCase() + `</td>
                                                <td colspan="3">` + data.kios.nama_kios.toUpperCase() + `</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">PEMBAYARAN </td>
                                                <td colspan="4">: KREDIT</td>
                                                <td colspan="3">` + data.kios.alamat.toUpperCase() + `</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">JATUH TEMPO</td>
                                                <td colspan="4">: ` + data.jatuh_tempo + `</td>
                                                <td colspan="3">` + data.kios.kabupaten.toUpperCase() + ` SULAWESI SELATAN</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="width: 2%;"></td>
                                                <td colspan="4"></td>
                                                <td>NPWP</td>
                                                <td colspan="2">: ` + data.kios.npwp + `</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top:-20px;padding-bottom: 5px;width: 2%;"></td>
                                                <td colspan="4" style="width: 55%;padding-top:-20px;padding-bottom: 5px;"></td>
                                                `;
                            //                 <td style="padding-top:-20px;padding-bottom: 5px;">NIK</td>
                            //                 <td colspan="2" style="padding-top:-20px;padding-bottom: 5px;">: ` +
                        // data.kios.nik + `</td>
                            html += `</tr>
                                        </table>
                                        
                                        <table id="item" width:100%>
                                            <tr class="">
                                                <th style="width: 1%;">No.</th>
                                                <th colspan="2" style="width: 43%;">Nama Produk</th>
                                                <th style="width: 5%;">Qty</th>
                                                <th style="width: 5%;">Satuan</th>
                                                <th style="width: 10%;">Harga Stn. (PPN 11%)</th>
                                                <th style="width: 5%;">Ket.</th>
                                                <th style="width: 5%;">Disc.</th>
                                                <th style="width: 10%;">Jumlah</th>
                                                </tr>
                                                `;
                            data.produks.map(function(value, index) {

                                no = index + 1;
                                html += `
                                <tr class="">
                                                <td style="width: 1%;text-align:center">` + no + `</td>
                                                <td colspan="2" style="width: 25%;">` +
                                    value.nama_produk.toUpperCase() + " " + value.kemasan
                                    .toUpperCase() +
                                    `</td>
                                                <td style="width: 5%;text-align:center">` + value.qty + `</td>
                                                <td style="width: 6%;text-align:center">` + value.satuan
                                    .toUpperCase() + `</td>
                                                <td style="width: 13%;text-align:right">` + formatRupiah(value
                                        .harga_jual.toString(),
                                        '') + `</td>
                                                <td style="width: 5%;text-align:center">` + value.ket.toUpperCase() + `</td>
                                                <td style="width: 5%;text-align:center">` + value.disc + `</td>
                                                <td style="width: 13%;text-align:right">` + value.jumlah + `</td>
                                            </tr>
                                `;
                            });

                            for (let index = 0; index < (15 - data.produks.length); index++) {
                                html += `<tr class="empty-list" style="line-height:15px;">
                                                <td style="width: 1%;text-align:center;"></td>
                                                <td colspan="2" style="width: 20%;"></td>
                                                <td style="width: 5%;text-align:center"></td>
                                                <td style="width: 10%;text-align:center"></td>
                                                <td style="width: 13%;text-align:right"></td>
                                                <td style="width: 5%;text-align:center"></td>
                                                <td style="width: 8%;text-align:center"></td>
                                                <td style="width: 13%;text-align:right"></td>
                                            </tr>`;

                            }
                            html += `
                                        </table>
                                        <table width="20%" style="float:right;margin-top: -1px;">
                                        <tr style="outline: thin solid black;">
                                            <td style="width:20%;">DPP </td>
                                            <td style="width:1%;">:</td>
                                            <td class="dpp" style="width:10%;text-align: right;">` + data.dpp + `</td>
                                        </tr>
                                        <tr style="outline: thin solid black;">
                                            <td style="width: 20%;">PPN</td>
                                            <td style="width:1%;">:</td>
                                            <td class="ppn" style="width:10%;text-align: right;">` + data.ppn + `</td>
                                        </tr>
                                        <tr style="outline: thin solid black;">
                                            <td style="width: 20%;">Discount</td>
                                            <td style="width:1%;">:</td>
                                            <td class="disc" style="width:10%;text-align: right;">` + data
                                .total_disc + `</td>
                                        </tr>
                            <tr style="outline: thin solid black;">
                                                <th style="width: 20%;">GRAND TOTAL</th>
                                                <th style="width:1%;">:</th>
                                                <th class="grand_total" style="width:10%;text-align: right;">` + data
                                .grand_total + `</th>
                                            </tr>
                                        </table>
                                `;

                            html += `
                                <div class="flex-container">
                                    <div class="flex-item-left">
                                        <h4>transfer ke rekening:</h4>
                                        <p>CV. AYYUB TANI</p>
                                        <p>BANK BRI : 343853485774547</p>
                                        <p style="margin-top: 150px;">Tanda terima</p>
                                    </div>
                                    <div class="flex-item-right">
                                        <p style="margin-top: 260px;">Hormat Kami</p>
                                    </div>
                                </div>
                                `;

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
                            console.log(error);
                            swal.fire({
                                text: "Pilih kios dan masukkan invoice terlebih dahulu",
                                title: "Error",
                                icon: "error",
                                showConfirmButton: true,
                                confirmButtonText: "OK",
                            })
                        });
                }
            };
        }();


        // add pembelian temp
        $(document).on('click', '.addTemp', function(e) {
            let kios_id = $('#kios').val();
            let produk_id = $('#produk').val();
            let ket = $('#ket').val();
            let disc = $('#disc').val();

            if (produk_id == 'null') {
                swal.fire({
                    text: "Silakan Pilih Produk",
                    title: "Error",
                    icon: "error",
                    showConfirmButton: true,
                    confirmButtonText: "OK",
                });
            }

            if (ket == 0) {
                swal.fire({
                    text: "Silakan masukkan quality Produk",
                    title: "Error",
                    icon: "error",
                    showConfirmButton: true,
                    confirmButtonText: "OK",
                });
            }

            if ((produk_id != 'null') && (ket != 0)) {
                $.ajax({
                    url: `/penjualan/temp`,
                    type: 'POST',
                    data: {
                        '_method': 'POST',
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'kios_id': kios_id,
                        'produk_id': produk_id,
                        'ket': ket,
                        'disc': disc,
                    },
                    success: function(response) {
                        if (response.status !== false) {
                            dataRow.destroy();
                            dataRow.init();

                            $('#kios').val('null').trigger('change');
                            $('#produk').val('null').trigger('change');
                            $('#ket').val(0);
                        } else {
                            swal.fire({
                                title: "Failed!",
                                text: `${response.message}`,
                                icon: "warning",
                            });
                        }
                    },
                    error: function(error) {

                        Swal.fire({
                                title: "Failed!",
                                text: `${error.responseJSON.message}`,
                                icon: "warning",
                            })
                            .then(function() {
                                dataRow.destroy();
                                dataRow.init();

                                $('#kios').val('null').trigger('change');
                                $('#produk').val('null').trigger('change');
                                $('#ket').val(0);
                            });
                    }
                });
            }

        });

        $(document).on('click', '.tempDelete', function(e) {
            e.preventDefault()
            let id = $(this).attr('data-id');
            console.log(id);
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
                    $.ajax({
                        url: `/penjualan/tempdelete/${id}`,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status !== false) {
                                Swal.fire('Deleted!',
                                        'Data berhasil dihapus.',
                                        'success')
                                    .then(function() {
                                        dataRow.destroy();
                                        dataRow.init();

                                        $('#kios').val('null').trigger('change');
                                        $('#produk').val('null').trigger('change');
                                        $('#ket').val(0);
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

        $(document).on('click', '.tempReset', function(e) {
            e.preventDefault()

            Swal.fire({
                title: 'Apakah kamu yakin akan mereset keranjang ini ?',
                text: "Semua data akan di hapus permanen",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/penjualan/tempreset`,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status !== false) {
                                Swal.fire('Deleted!',
                                        'Data berhasil dihapus.',
                                        'success')
                                    .then(function() {
                                        dataRow.destroy();
                                        dataRow.init();

                                        $('#kios').val('null').trigger('change');
                                        $('#produk').val('null').trigger('change');
                                        $('#ket').val(0);
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


        $(document).on('click', '.previewPenjualan', function(e) {
            e.preventDefault();
            // var form = $('#penjualanForm');
            // form.attr('data-type', 'print');

            var dataForm = document.querySelector('#penjualanForm');
            var formData = new FormData(dataForm);

            AxiosCall.print("{{ route('penjualan-preview') }}", formData,
                "#penjualanForm");
        });

        $(document).on('keyup', '.qty-jual', function(e) {
            let produkId = $('#produk').val();
            let qty = $('#ket').val();
            console.log(qty);
            if (produkId == 'null') {
                swal.fire({
                    title: "Warning!",
                    text: "Pilih produk terlebih dahulu",
                    icon: "warning",
                });
            }

            if (produkId != 'null') {
                $.ajax({
                    url: `/penjualan/get-stok/${produkId}`,
                    type: 'GET',
                    data: {
                        '_method': 'GET',
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'qty': qty,
                    },
                    success: function(response) {
                        console.log(response);
                    },
                    error: function(error) {
                        console.log(error.responseJSON.data);
                        let data = error.responseJSON.data;
                        swal.fire({
                            title: `${data.nama_produk.toUpperCase()} ${data.kemasan.toUpperCase()}`,
                            text: ` ${error.responseJSON.message}!`,
                            icon: "warning",
                        }).then(function() {
                            $('#ket').val(0);
                        });
                    }
                })
            }
        });

        $(document).on('submit', "#penjualanForm[data-type='submit']", function(e) {
            e.preventDefault();

            if ($('#kios').val() == 'null') {
                swal.fire({
                    title: `Warning!`,
                    text: `Pilih kios terlebih dahulu`,
                    icon: "warning",
                });
            }

            if ($('#kios').val() != 'null') {
                var dataForm = document.querySelector('#penjualanForm');
                var formData = new FormData(dataForm);

                AxiosCall.post("{{ route('penjualan-store') }}", formData,
                    "#penjualanForm");
            }

        });


        $(document).ready(function() {
            dataRow.init();

            $('#kios').select2();
            $('#produk').select2();

            $('#tanggal_jual').datepicker({
                format: "dd-mm-yyyy",
                weekStart: 1,
                daysOfWeekHighlighted: "6,0",
                autoclose: true,
                todayHighlight: true,
            });
            $('#tanggal_jual').datepicker("setDate", new Date());
        });
    </script>
@endsection
