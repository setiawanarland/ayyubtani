@extends('layout.template')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Daftar Pembelian</h4>
                        <button type="button" class="btn d-flex btn-primary mb-3 pull-right tambahData">Tambah Data</button>
                        <div class="data-tables">
                            <table id="daftarPembelianTable" class="text-cente">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th>Nama Supplier</th>
                                        <th>invoice</th>
                                        <th>Tanggal Beli</th>
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
    <div class="modal fade" id="pembelianModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pembelian</h5>
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
                let table = $('#daftarPembelianTable');
                table.DataTable({
                    processing: true,
                    ordering: false,
                    ajax: "{{ route('pembelian-list') }}",
                    columns: [{
                            data: 'nama_supplier',
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
                            data: 'tanggal_beli',
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
                            <button type="button" class="btn btn-primary btn-sm pembelianShow" data-toggle="modal" data-target="#pembelianModal" data-id="${row.id}"><i class="fa fa-eye"></i></button>
                            <button type="button" class="btn btn-danger btn-sm btn-edit pembelianDelete" data-id="${row.id}"><i class="fa fa-trash"></i></button>
                            `;
                            //         <a role="button" href="javascript:;" type="button" data-id="${row.id}" class="btn btn-warning btn-sm pembelianUpdate"><i class="fa fa-edit"></i></a>
                            //         <button type="button" class="btn btn-danger btn-sm btn-delete pembelianDelete" data-id="${row.id}"><i class="fa fa-trash"></i></button>
                            // `;
                    },
                }],
            });

        };

        var destroy = function() {
            var table = $('#daftarPembelianTable').DataTable();
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




    // show pembelian
    $(document).on('click', '.pembelianShow', function() {
        console.log($(this));

        var key = $(this).data('id');
        axios.get('/pembelian/show/' + key)
            .then(function(res) {
                let data = res.data;
                // console.log(data);

                let date = new Date(data.pembelian.tanggal_beli);
                let tanggal_beli = date.getDate() + '/' + (date.getMonth() + 1) + '/' + date
                    .getFullYear();

                let element = `
                                                                <table id="" class="text-cente" style="width: 100%;">
                                                                    <tr>
                                                                        <td colspan="6" style="font-size: 30px;">
                                                                            ` + data.supplier.nama_supplier.toUpperCase() + `
                                                                        </td>
                                                                        <td colspan="3">
                                                                            Makassar, ` + tanggal_beli + `
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="6" style="padding-bottom: 20px;">` +
                    data
                    .supplier
                    .alamat
                    .toUpperCase() +
                    `</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td colspan="2" style="padding-top:-20px;padding-bottom: 20px;">NO. INVOICE </td>
                                                                        <td colspan="4" style="padding-top:-20px;padding-bottom: 20px;">: ` +
                    data
                    .pembelian
                    .invoice.toUpperCase() + `</td>
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

                data.detailPembelian.map(function(value, index) {
                    console.log(value);
                    no = index + 1;
                    element += `
                                                                        <tr class="">
                                                                            <td style="width: 1%;">` + no + `</td>
                                                                            <td colspan="2" style="width: 25%;">` + value
                        .nama_produk
                        .toUpperCase() +
                        ` ` +
                        value
                        .kemasan_produk.toUpperCase() + `</td>
                                                                            <td style="width: 10%;">` + value.ket
                        .toUpperCase() + `</td>
                                                                            <td style="width: 8%;">` + formatRupiah(value
                            .disc
                            .toString(),
                            "") + `</td>
                                                                            <td style="width: 15%;text-align: right;">` +
                        formatRupiah(
                            value
                            .jumlah
                            .toString(),
                            "") + `</td>
                                                                        </tr>`;
                });

                element += `
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
                    formatRupiah(data.pembelian.grand_total
                        .toString(), '') + `</th>
                                                                    </tr>
                                                                </table>
                                                                `;

                $('.modalDetail').children().remove();
                $('.modalDetail').append(element);

            })
            .catch(function(err) {

            });
    });


    // edit supplier
    $(document).on('submit', "#kiosForm[data-type='update']", function(e) {
        e.preventDefault();

        var _id = $("input[name='id']").val();
        var form = document.querySelector('form');
        var formData = new FormData(this);

        AxiosCall.update("{{ route('kios-update') }}", formData,
            "#kiosForm");
    });


    // delete pembelian
    $(document).on('click', '.pembelianDelete', function(e) {
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
                $.ajax({
                    url: `/pembelian/delete/${id}`,
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




        // format npwp
        $('#npwp').on('keyup', function() {
            $(this).val(formatNpwp($(this).val()));


        });

        function formatNpwp(value) {
            if (typeof value === 'string') {
                return value.replace(/(\d{2})(\d{3})(\d{3})(\d{1})(\d{3})(\d{3})/, '$1.$2.$3.$4-$5.$6');
            }
        };

        $(document).ready(function() {
            dataRow.init();

        });
    </script>
@endsection
