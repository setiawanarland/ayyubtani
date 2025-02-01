@extends('layout.template')

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
                                                {{ Str::upper($value->nama_produk) }} | {{ Str::upper($value->kemasan) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 col-md-1 my-1">
                                    <label class="" for="qty">Qty</label>
                                    <input type="number" class="form-control" id="ket" name="ket" value="0"
                                        min="0">
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

                        <div class="col-auto my-1 pl-0">
                            <button type="button" class="btn btn-primary btn-xs newProduk" data-toggle="modal"
                                data-target="#newProduk">
                                <i class="fa fa-plus"></i> Produk Baru
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <!-- data table end -->
        </div>
        <div class="row">
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Detail Pembelian</h4>

                        <button type="button" class="btn d-flex btn-danger mb-3 pull-right tempReset">Reset Keranjang
                        </button>

                        <form id="pembelianForm" action="" data-type="submit">
                            <div class="form-row align-items-center">
                                <div class="col-sm-3 col-md-3 my-3">
                                    <label class="" for="supplier">Supplier</label>
                                    <select class="form-control" id="supplier" name="supplier">
                                        {{-- <option value="null">Pilih Supplier</option> --}}
                                        @foreach ($supplier as $index => $value)
                                            <option value="{{ $value->id }}">{{ Str::upper($value->nama_supplier) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 col-md-3 my-3">
                                    <label class="" for="invoice">Invoice</label>
                                    <input type="text" class="form-control" id="invoice" name="invoice">
                                </div>
                                <div class="col-sm-3 col-md-3 my-3">
                                    <label class="" for="tanggal_beli">Tanggal Beli</label>
                                    <input type="text" class="form-control" id="tanggal_beli" name="tanggal_beli">
                                </div>
                            </div>

                            <div class="data-tables">
                                <table id="detailPembelianTable" class="text-cente">
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
                                        {{-- <tr>
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
                                        </tr> --}}
                                        <tr>
                                            <th>GRAND TOTAL</th>
                                            <th style="padding-left:130px; padding-right:3px;">:</th>
                                            <th class="grand_total">
                                                <input type="text" class="form-control" id="grand_total"
                                                    name="grand_total" value="">
                                                <div class="invalid-feedback">Masukkan grand total.</div>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>

                                <div class="form-group" style="margin-top: 10px;">
                                    <button class="btn btn-primary" type="submit">Save</button>
                                    {{-- <button class="btn btn-danger btn-cancel printPreview" type="">Print</button> --}}
                                    <button class="btn btn-success btn-po po" type="button">PO</button>
                                </div>
                            </div>

                    </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('offset-area')
    <!-- Modal -->
    <div class="modal fade" id="newProduk">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pembelian</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body modalDetail">

                    <form id="newProdukForm" data-type="submit">
                        @csrf

                        <input class="form-control" type="hidden" name="id" id="id">

                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="supplier_id" class="col-form-label">Supplier</label>
                            <select class="form-control" id="supplier_id" name="supplier_id">
                                @foreach ($supplier as $index => $value)
                                    <option value="{{ $value->id }}">{{ Str::upper($value->nama_supplier) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="nama_produk" class="col-form-label">Nama Produk</label>
                            <input class="form-control" type="text" name="nama_produk" id="nama_produk" autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="kemasan" class="col-form-label">Kemasan</label>
                            <input class="form-control" type="text" name="kemasan" id="kemasan">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="satuan" class="col-form-label">Satuan</label>
                            <input class="form-control" type="text" name="satuan" id="satuan">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="jumlah_perdos" class="col-form-label">Jumlah Perdos</label>
                            <input class="form-control" type="text" name="jumlah_perdos" id="jumlah_perdos"
                                value="0">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="qty_kemasan" class="col-form-label">Qty Kemasan</label>
                            <input class="form-control" type="text" name="qty_kemasan" id="qty_kemasan"
                                value="0">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="qty_perdos" class="col-form-label">Qty Perdos</label>
                            <input class="form-control" type="text" name="qty_perdos" id="qty_perdos"
                                value="0">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="harga_beli" class="col-form-label">Harga Beli</label>
                            <input class="form-control" type="text" name="harga_beli" id="harga_beli">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="harga_jual" class="col-form-label">Harga Jual</label>
                            <input class="form-control" type="text" name="harga_jual" id="harga_jual">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="harga_perdos" class="col-form-label">Harga Perdos</label>
                            <input class="form-control" type="text" name="harga_perdos" id="harga_perdos" readonly>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="stok_masuk" class="col-form-label">Stok Masuk</label>
                            <input class="form-control" type="text" name="stok_masuk" id="stok_masuk">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="disc_harga" class="col-form-label">Disc.</label>
                            <input class="form-control" type="text" name="disc_harga" id="disc_harga">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0px;">
                            <button class="btn btn-primary" type="submit">Save</button>
                            <button class="btn btn-danger btn-cancel" type="reset" data-dismiss="modal">Cancel</button>
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
                let table = $('#detailPembelianTable');
                table.DataTable({
                    processing: true,
                    ordering: false,
                    paging: false,
                    searching: false,
                    lengthChange: false,
                    info: false,
                    ajax: {
                        url: "{{ route('pembeliantemp-list') }}",
                        dataSrc: function(data) {
                            dpp = 0;
                            ppn = 0;
                            grand_total = 0;
                            data.data.map(function(data) {
                                grand_total += data.jumlah;
                                satuan_pajak = data.satuan_pajak;
                            });
                            grand_total = 0;
                            ppn = grand_total * satuan_pajak / 100;
                            dpp = grand_total - ppn;
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
                            width: '1%',
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
                            // visible: false,
                            render: function(data, type, row) {
                                return `
                                    <input type="text" class="form-control qty" id="qty" name="qty[]" value="` + data + `">
                                `;
                            }
                        },
                        {
                            data: 'satuan',
                            // visible: false,
                            render: function(data, type, row) {
                                return `
                                    <input type="text" class="form-control satuan" id="satuan" name="satuan[]" value="` +
                                    data.toUpperCase() + `">
                                `;
                            }
                        },
                        {
                            data: 'harga_beli',
                            visible: false,
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
                            visible: false,
                            render: function(data, type, row) {
                                return `
                                <input type="text" class="form-control disc" id="disc" name="disc[]" value="` +
                                    data + `">
                                `;
                            }
                        },
                        {
                            data: 'jumlah',
                            visible: false,
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
                        width: '3%',
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
                var table = $('#detailPembelianTable').DataTable();
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
                            $('#preloader').show();
                            var data = res.data;
                            console.log(data);
                            if (data.fail) {
                                $('#preloader').hide();
                                swal.fire({
                                    text: "Maaf Terjadi Kesalahan",
                                    title: "Error",
                                    timer: 2000,
                                    icon: "danger",
                                    showConfirmButton: false,
                                });
                            } else if (data.invalid) {
                                $('#preloader').hide();
                                console.log(data);
                                $.each(data.invalid, function(key, value) {
                                    console.log(key);
                                    console.log('errorType', typeof error);
                                    $("input[name='" + key + "']").addClass('is-invalid').siblings(
                                        '.invalid-feedback').html(value[0]);
                                });
                            } else if (data.success) {
                                $('#preloader').hide();
                                swal.fire({
                                    text: "Data anda berhasil disimpan",
                                    title: "Sukses",
                                    icon: "success",
                                    showConfirmButton: true,
                                    confirmButtonText: "OK, Siip",
                                }).then(function() {
                                    dataRow.destroy();
                                    dataRow.init();
                                    window.location = "{{ route('daftar-pembelian') }}";
                                });
                            }
                        }).catch(function(error) {
                            $('#preloader').hide();
                            console.log(error);
                            swal.fire({
                                text: "Pilih Supplier dan masukkan invoice terlebih dahulu",
                                title: "Error",
                                icon: "error",
                                showConfirmButton: true,
                                confirmButtonText: "OK",
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
                            // console.log(res.data);
                            var data = res.data;

                            var html = `
                                        <style>
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
                                        </style>

                                        <table id="" class="text-cente" style="width: 100%;">
                                            <tr>
                                                <td colspan="6" style="font-size: 30px;">
                                                    ` + data.supplier.nama_supplier.toUpperCase() + `
                                                </td>
                                                <td colspan="3">
                                                    Makassar, ` + data.tanggal_beli + `
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" style="padding-bottom: 20px;">` + data.supplier.alamat
                                .toUpperCase() + `</td>
                                                <td colspan="3" style="padding-bottom:20px">Kepada Yth,</td>
                                            </tr>
                                            

                                            <tr>
                                                <td colspan="2">NO. INVOICE </td>
                                                <td colspan="4">: ` + data.invoice.toUpperCase() + `</td>
                                                <td colspan="3">CV. AYYUB TANI</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">PEMBAYARAN </td>
                                                <td colspan="4">: KREDIT</td>
                                                <td colspan="3">SALAMATARA KARELOE BONTORAMBA</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">JATUH TEMPO</td>
                                                <td colspan="4">: ` + data.jatuh_tempo + `</td>
                                                <td colspan="3">JENEPONTO SULAWESI SELATAN</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="width: 2%;">GUDANG</td>
                                                <td colspan="4">: 1</td>
                                                <td>NPWP</td>
                                                <td colspan="2">: 12343546576879875645</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top:-20px;padding-bottom: 20px;width: 2%;">PO. NO. </td>
                                                <td colspan="4" style="width: 55%;padding-top:-20px;padding-bottom: 20px;">: 13243546443</td>
                                                <td style="padding-top:-20px;padding-bottom: 20px;">NIK</td>
                                                <td colspan="2" style="padding-top:-20px;padding-bottom: 20px;">: 23245467675445</td>
                                            </tr>
                                        </table>
                                        
                                        <table id="item">
                                            <tr class="">
                                                <th style="width: 1%;">No.</th>
                                                <th colspan="2" style="width: 25%;">Nama Produk</th>
                                                <th style="width: 5%;">Qty</th>
                                                <th style="width: 10%;">Satuan</th>
                                                <th style="width: 15%;">Harga Stn. (PPN 11%)</th>
                                                <th style="width: 5%;">Ket.</th>
                                                <th style="width: 8%;">Disc.</th>
                                                <th style="width: 15%;">Jumlah</th>
                                                </tr>
                                                `;
                            data.produks.map(function(value, index) {
                                console.log(value);
                                no = index + 1;
                                html += `
                                <tr class="">
                                                <td style="width: 1%;">` + no + `</td>
                                                <td colspan="2" style="width: 20%;">` +
                                    value.nama_produk.toUpperCase() + " " + value.kemasan
                                    .toUpperCase() +
                                    `</td>
                                                <td style="width: 5%;text-align:center">` + value.qty + `</td>
                                                <td style="width: 10%;text-align:center">` + value.satuan + `</td>
                                                <td style="width: 13%;text-align:right">` + formatRupiah(value
                                        .harga_beli.toString(),
                                        '') + `</td>
                                                <td style="width: 5%;text-align:center">` + value.ket + `</td>
                                                <td style="width: 8%;text-align:center">` + value.disc + `</td>
                                                <td style="width: 13%;text-align:right">` + value.jumlah + `</td>
                                            </tr>
                                `;
                            })
                            html += `
                                        </table>
                                        <table width="20%" style="float:right;margin-top: -1px;">`;
                            //             <tr style="outline: thin solid black;">
                            //                 <td style="width:20%;">DPP </td>
                            //                 <td style="width:1%;">:</td>
                            //                 <td class="dpp" style="width:10%;text-align: right;">` + data.dpp + `</td>
                            //             </tr>
                            //             <tr style="outline: thin solid black;">
                            //                 <td style="width: 20%;">PPN</td>
                            //                 <td style="width:1%;">:</td>
                            //                 <td class="ppn" style="width:10%;text-align: right;">` + data.ppn + `</td>
                            //             </tr>
                            //             <tr style="outline: thin solid black;">
                            //                 <td style="width: 20%;">Discount</td>
                            //                 <td style="width:1%;">:</td>
                            //                 <td class="disc" style="width:10%;text-align: right;">` + data
                        // .total_disc + `</td>
                            //             </tr>
                            html += `<tr style="outline: thin solid black;">
                                                <th style="width: 20%;">GRAND TOTAL</th>
                                                <th style="width:1%;">:</th>
                                                <th class="grand_total" style="width:10%;text-align: right;">` + data
                                .grand_total + `</th>
                                            </tr>
                                        </table>
                                `;
                            var popupWin = window.open('', '_blank', 'width=500,height=500');
                            popupWin.document.open();
                            popupWin.document.write(
                                '<html><body onload="document.title=`Ayyub Tani`;document.footer=`Ayyub Tani`;window.print()">' +
                                html + '</html>');
                            popupWin.document.close();

                        }).catch(function(error) {
                            console.log(error);
                            swal.fire({
                                text: "Pilih supplier dan masukkan invoice terlebih dahulu",
                                title: "Error",
                                icon: "error",
                                showConfirmButton: true,
                                confirmButtonText: "OK",
                            })
                        });
                },
                newProduk: function(_url, _data, _element) {
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
                                    $('#newProduk').modal('hide');
                                });
                            }
                        }).catch(function(error) {
                            console.log(error);
                            swal.fire({
                                text: "Pilih Supplier dan masukkan invoice terlebih dahulu",
                                title: "Error",
                                icon: "error",
                                showConfirmButton: true,
                                confirmButtonText: "OK",
                            })
                        });
                },
            };
        }();


        // add pembelian temp
        $(document).on('click', '.addTemp', function(e) {
            let supplier_id = $('#supplier').val();
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

                $('#preloader').show();

                $.ajax({
                    url: `/pembelian/temp`,
                    type: 'POST',
                    data: {
                        '_method': 'POST',
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'supplier_id': supplier_id,
                        'produk_id': produk_id,
                        'ket': ket,
                        'disc': disc,
                    },
                    success: function(response) {
                        $('#preloader').hide();
                        if (response.status !== false) {
                            dataRow.destroy();
                            dataRow.init();

                            // $('#supplier').val('null').trigger('change');
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
                        $('#preloader').hide();

                        Swal.fire({
                                title: "Failed!",
                                text: `${error.responseJSON.message}`,
                                icon: "warning",
                            })
                            .then(function() {
                                dataRow.destroy();
                                dataRow.init();

                                $('#supplier').val('null').trigger('change');
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
                    $('#preloader').show();
                    $.ajax({
                        url: `/pembelian/tempdelete/${id}`,
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

                                        // $('#supplier').val('null').trigger('change');
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
                    $('#preloader').show();
                    $.ajax({
                        url: `/pembelian/tempreset`,
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

                                        // $('#supplier').val('null').trigger('change');
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

        // create new produk
        $(document).on('click', ".newProduk", function(e) {
            $("input").removeClass('is-invalid');
            $("select").removeClass('is-invalid');
            $("textarea").removeClass('is-invalid');
            var form = $('#newProdukForm');
            form[0].reset();

            // const number = 3500;

            // console.log(new Intl.NumberFormat("id-ID", {
            //     style: "currency",
            //     currency: "IDR",
            //     minimumFractionDigits: 0,
            // }).format(number));
        });

        // format rupiah
        $('#harga_beli, #harga_jual, #harga_perdos').on('keyup', function() {
            $(this).val(formatRupiah($(this).val(), 'Rp. '));

            var jumlah_perdos = parseInt($('#jumlah_perdos').val());
            var harga_jual = parseInt($('#harga_jual').val().replace(/[^0-9]/g, ''));
            var harga_perdos = harga_jual * jumlah_perdos;

            $('#harga_perdos').val(formatRupiah(harga_perdos.toString(), 'Rp. '));
        });

        $(document).on('submit', "#newProdukForm[data-type='submit']", function(e) {
            e.preventDefault();

            var form = document.querySelector('form');
            var formData = new FormData(this);

            AxiosCall.newProduk("{{ route('pembelian-produk-new') }}", formData,
                "#newProdukForm");
        });


        // add pembelian
        $(document).on('submit', "#pembelianForm[data-type='submit']", function(e) {
            e.preventDefault();
            let grand_total = $('#grand_total').val();

            if (grand_total <= 0) {
                return swal.fire({
                    text: "Silakan masukkan grand total",
                    title: "Error",
                    icon: "error",
                    showConfirmButton: true,
                    confirmButtonText: "OK",
                }).then(function() {
                    $("input[name='grand_total']").addClass('is-invalid').siblings(
                        '.invalid-feedback');
                    $('#grand_total').focus();
                    $('#grand_total').trigger('focus');
                });
            }

            var dataForm = document.querySelector('#pembelianForm');
            var formData = new FormData(dataForm);

            AxiosCall.post("{{ route('pembelian-store') }}", formData,
                "#pembelianForm");
        });


        $(document).on('click', '.printPreview', function(e) {
            e.preventDefault();
            // var form = $('#pembelianForm');
            // form.attr('data-type', 'print');

            var dataForm = document.querySelector('#pembelianForm');
            var formData = new FormData(dataForm);

            AxiosCall.print("{{ route('pembelian-preview') }}", formData,
                "#produkForm");
        });

        // PO
        $(document).on('click', '.po', () => {
            console.log('ok');
            const url = `/pembelian/po`;
            window.open(url);
        });

        // auto
        $(document).on('focus', '#ket, #grand_total', function(e) {
            if ($(this).val() == 0) {
                $(this).val('');
            }
        });


        $('#grand_total').on('keyup', function() {
            if ($(this).val() > 0) {
                $(this).removeClass('is-invalid');
            }
            $(this).val(formatRupiah($(this).val(), ''));
        });


        $(document).ready(function() {
            dataRow.init();

            $('#supplier').select2();
            $('#produk').select2();

            $('#tanggal_beli').datepicker({
                format: "dd-mm-yyyy",
                weekStart: 1,
                daysOfWeekHighlighted: "6,0",
                autoclose: true,
                todayHighlight: true,
            });
            $('#tanggal_beli').datepicker("setDate", new Date());

            $(window).keydown(function(e) {
                switch (e.keyCode) {
                    case 190:
                        $("#produk").select2('open');
                        break;
                    case 188:
                        $("#invoice").focus();
                        break;
                }
            });

        });
    </script>
@endsection
