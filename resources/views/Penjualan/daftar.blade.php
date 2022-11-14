@extends('layout.template')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Daftar Penjualan</h4>
                        <button type="button" class="btn d-flex btn-primary mb-3 pull-right tambahData">Tambah Data</button>
                        <div class="data-tables">
                            <table id="daftarPenjualanTable" class="text-cente">
                                <thead class="bg-light text-capitalize">
                                    <tr>
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
    <div class="modal fade" id="penjualanModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
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
                            data: 'nama_kios',
                            render: function(data, type, row) {
                                return data.toUpperCase();
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
                                return formatRupiah(data.toString(), '');
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
                        width: '10rem',
                        class: "wrapok",
                        render: function(data, type, row, full, meta) {
                            return `
                            <button type="button" class="btn btn-primary btn-sm penjualanShow" data-toggle="modal" data-target="#penjualanModal" data-id="${row.id}"><i class="fa fa-eye"></i></button>
                            <button type="button" class="btn btn-warning btn-sm btn-edit penjualanEdit" data-id="${row.id}"><i class="fa fa-edit"></i></button>
                            `;
                            //         <a role="button" href="javascript:;" type="button" data-id="${row.id}" class="btn btn-warning btn-sm pembelianUpdate"><i class="fa fa-edit"></i></a>
                            //         <button type="button" class="btn btn-danger btn-sm btn-delete pembelianDelete" data-id="${row.id}"><i class="fa fa-trash"></i></button>
                            // `;
                    },
                }],
            });

        };

        var destroy = function() {
            var table = $('#daftarPenjualanTable').DataTable();
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
                    `
                                                                                        <table id="" class="text-cente" style="width: 100%;">
                                                                                            <tr>
                                                                                                <td colspan="6" style="font-size: 30px;">
                                                                                                    ` + data.kios.nama_kios
                    .toUpperCase() + `
                                                                                                </td>
                                                                                                <td colspan="3">
                                                                                                    Jeneponto, ` +
                    tanggal_jual + `
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td colspan="6" style="padding-bottom: 20px;">
                                                                                                    ` + data.kios.alamat
                    .toUpperCase() +
                    ` ` +
                    data
                    .kios
                    .kabupaten
                    .toUpperCase() + `
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td colspan="2" style="padding-top:-20px;padding-bottom: 20px;">NO. INVOICE </td>
                                                                                                <td colspan="4" style="padding-top:-20px;padding-bottom: 20px;">
                                                                                                    : ` + data.penjualan
                    .invoice
                    .toUpperCase() + `
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                        
                                                                                        <table id="item">
                                                                                            <tr class="">
                                                                                                <th style="width: 1%;">No.</th>
                                                                                                <th colspan="2" style="width: 25%;">Nama Produk</th>
                                                                                                <th style="width: 5%;">Ket.</th>
                                                                                                <th style="width: 8%;">Disc.</th>
                                                                                                <th style="width: 15%;">Jumlah</th>
                                                                                            </tr>`;

                data.detailPenjualan.map(function(value, index) {
                    console.log(value);
                    no = index + 1;

                    element +=
                        `
                                                                                            <tr class="">
                                                                                                <td style="width: 1%;">` +
                        no + `</td>
                                                                                                <td colspan="2" style="width: 25%;">
                                                                                                    ` + value.nama_produk
                        .toUpperCase() +
                        ` ` +
                        value
                        .kemasan_produk
                        .toUpperCase() + `
                                                                                                </td>
                                                                                                <td style="width: 10%;">` +
                        value
                        .ket
                        .toUpperCase() + `</td>
                                                                                                <td style="width: 8%;">` +
                        formatRupiah(
                            value
                            .disc
                            .toString(),
                            "") +
                        `</td>
                                                                                                <td style="width: 15%;text-align: right;">` +
                        formatRupiah(
                            value
                            .jumlah
                            .toString(),
                            "") + `</td>
                                                                                            </tr>`;
                });

                element +=
                    `
                                                                                        </table>
                                                                                        
                                                                                        <table width="50%" style="float:right;margin-top: 10px;">`;
                //                             <tr>
                //                                 <td style="width:20%;">DPP </td>
                //                                 <td style="width:1%;">:</td>
                //                                 <td class="dpp" style="width:20%;text-align: right;">` + formatRupiah(
                    //     data
                    //     .pembelian
                    //     .dpp
                    //     .toString(), '') + `</td>
                //                             </tr>
                //                             <tr>
                //                                 <td style="width: 20%;">PPN</td>
                //                                 <td style="width:1%;">:</td>
                //                                 <td class="ppn" style="width:20%;text-align: right;">` + formatRupiah(
                    //     data
                    //     .pembelian
                    //     .ppn
                    //     .toString(), '') + `</td>
                //                             </tr>
                //                             <tr>
                //                                 <td style="width: 20%;">Discount</td>
                //                                 <td style="width:1%;">:</td>
                //                                 <td class="disc" style="width:20%;text-align: right;">` + formatRupiah(
                    //     data
                    //     .pembelian
                    //     .total_disc
                    //     .toString(), '') +
                    // `</td>
                //                             </tr>
                element +=
                    `<tr>
                                                                                        <th style="width: 20%;">GRAND TOTAL</th>
                                                                                        <th style="width:1%;">:</th>
                                                                                        <th class="grand_total" style="width:20%;text-align: right;font-weight:bold">` +
                    formatRupiah(
                        data.penjualan.grand_total.toString(), '') + `</th>
                                                                                    </tr>
                                                                                </table>
                                                                                `;

                $('.modalDetail').children().remove();
                $('.modalDetail').append(element);

            })
            .catch(function(err) {

            });
    });


    $(document).on('click', '.penjualanEdit', function() {
        var id = $(this).data('id');
        console.log(id);

        url = `/penjualan/edit/${id}`;
            window.location = url;
        });




        $(document).ready(function() {
            dataRow.init();

        });
    </script>
@endsection
