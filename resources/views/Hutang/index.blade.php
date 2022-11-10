@extends('layout.template')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Data Hutang</h4>

                        <div class="data-tables">
                            <table id="hutangTable" class="text-cente">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th>Tanggal Beli</th>
                                        <th>Invoice</th>
                                        <th>Debet</th>
                                        <th>Tanggal Bayar</th>
                                        <th>Ket.</th>
                                        <th>Kredit</th>
                                        <th>Sisa Hutang</th>
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
    <div class="offset-area">
        <div class="offset-close"><i class="ti-close"></i></div>
        <ul class="nav offset-menu-tab">
            <li>
                <h6 class="active">Tambah Data Kios</h6>
            </li>
        </ul>
        <div class="offset-content tab-content">
            <div id="activity" class="tab-pane fade in show active">
                <div class="recent-activity">

                    <form id="kiosForm" data-type="submit">
                        @csrf

                        <input class="form-control" type="hidden" name="id" id="id">

                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="nama_kios" class="col-form-label">Nama Kios</label>
                            <input class="form-control" type="text" name="nama_kios" id="nama_kios" autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="pemilik" class="col-form-label">Nama Pemilik</label>
                            <input class="form-control" type="text" name="pemilik" id="pemilik" autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="kabupaten" class="col-form-label">Wil. Kabupaten</label>
                            <input class="form-control" type="text" name="kabupaten" id="kabupaten" autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="alamat" class="col-form-label">Alamat</label>
                            <input class="form-control" type="text" name="alamat" id="alamat">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="npwp" class="col-form-label">NPWP</label>
                            <input class="form-control" type="text" name="npwp" id="npwp">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="nik" class="col-form-label">NIK</label>
                            <input class="form-control" type="text" name="nik" id="nik">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0px; bottom:0;">
                            <button class="btn btn-primary" type="submit">Save</button>
                            <button class="btn btn-danger btn-cancel" type="reset">Cancel</button>
                        </div>
                    </form>

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
                let table = $('#hutangTable');
                table.DataTable({
                    processing: true,
                    ordering: false,
                    ajax: "{{ route('hutang-list') }}",
                    columns: [{
                            data: 'tanggal_beli',
                            render: function(data, type, row) {
                                var date = new Date(data);
                                return date.getDate() + '/' + (date.getMonth() + 1) + '/' + date
                                    .getFullYear();
                            }
                        },
                        {
                            data: 'pembelian_id',
                            render: function(data, type, row) {
                                return row.invoice.toUpperCase();
                            }
                        },
                        {
                            data: 'debet',
                            render: function(data, type, row) {
                                return formatRupiah(data.toString(), '');
                            }
                        },
                        {
                            data: 'tanggal_bayar',
                            render: function(data, type, row) {
                                if (data != null) {
                                    var date = new Date(data);
                                    return date.getDate() + '/' + (date.getMonth() + 1) + '/' + date
                                        .getFullYear();
                                }

                                return data;
                            }
                        },
                        {
                            data: 'ket',
                            render: function(data, type, row) {
                                if (data != null) {
                                    return data.toUpperCase();
                                }
                                return data;
                            }
                        },
                        {
                            data: 'kredit',
                            render: function(data, type, row) {
                                return formatRupiah(data.toString(), '');
                            }
                        },
                        {
                            data: 'sisa',
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
                            <button type="button" class="btn btn-primary btn-sm bayarHutang" data-toggle="modal" data-target="#bayarHutangModal" data-id="${row.id}"><i class="fa fa-money"></i></button>
                    `;
                        },
                    }],
                });

            };

            var destroy = function() {
                var table = $('#hutangTable').DataTable();
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
