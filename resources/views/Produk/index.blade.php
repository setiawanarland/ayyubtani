@extends('layout.template')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Data Produk</h4>
                        <button type="button" class="btn d-flex btn-primary mb-3 pull-right tambahData">Tambah Data</button>
                        <div class="data-tables">
                            <table id="produkTable" class="text-cente">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Kemasan</th>
                                        <th>Satuan</th>
                                        <th>Harga Beli</th>
                                        <th>Harga Jual</th>
                                        <th>Harga Perdos</th>
                                        <th>Stok</th>
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
                <h6 class="active">Tambah Data Produk</h6>
            </li>
        </ul>
        <div class="offset-content tab-content">
            <div id="activity" class="tab-pane fade in show active">
                <div class="recent-activity">

                    <form id="produkForm" data-type="submit">
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

                        <div class="form-group" style="margin-bottom: 0px;">
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
                let table = $('#produkTable');
                table.DataTable({
                    processing: true,
                    ordering: false,
                    ajax: "{{ route('produk-list') }}",
                    columns: [{
                            data: 'nama_produk',
                            render: function(data, type, row) {
                                return data.toUpperCase();
                            }
                        },
                        {
                            data: 'kemasan',
                            render: function(data, type, row) {
                                return data.toUpperCase();
                            }
                        },
                        {
                            data: 'satuan',
                            render: function(data, type, row) {
                                return data.toUpperCase();
                            }
                        },
                        {
                            data: 'harga_beli',
                            render: function(data, type, row) {
                                return formatRupiah(data.toString(), '');
                            }
                        },
                        {
                            data: 'harga_jual',
                            render: function(data, type, row) {
                                return formatRupiah(data.toString(), '');
                            }
                        },
                        {
                            data: 'harga_perdos',
                            render: function(data, type, row) {
                                return formatRupiah(data.toString(), '');
                            }
                        },
                        {
                            data: 'stok',
                            render: function(data, type, row) {
                                return `${data} Dos`;
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
                            <a role="button" href="javascript:;" type="button" data-id="${row.id}" class="btn btn-warning btn-sm produkUpdate"><i class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-danger btn-sm btn-delete produkDelete" data-id="${row.id}"><i class="fa fa-trash"></i></button>
                    `;
                        },
                    }],
                });

            };

            var destroy = function() {
                var table = $('#produkTable').DataTable();
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
                                    $('.offset-area').toggleClass('show_hide');
                                    $('.settings-btn').toggleClass('active');
                                    var form = $('#produkForm');
                                    form[0].reset();
                                    dataRow.destroy();
                                    dataRow.init();
                                });
                            }
                        }).catch(function(error) {
                            swal.fire({
                                text: "Terjadi Kesalahan Sistem",
                                title: "Error",
                                icon: "error",
                                showConfirmButton: true,
                                confirmButtonText: "OK",
                            })
                        });
                },
            };
        }();


        // trigger form
        $('.tambahData, .btn-cancel').on('click', function() {
            $('.offset-area').toggleClass('show_hide');
            $('.settings-btn').toggleClass('active');
            var form = $('#produkForm');
            form.attr('data-type', 'submit');
            form[0].reset();
        });

        // create produk
        $(document).on('submit', "#produkForm[data-type='submit']", function(e) {
            e.preventDefault();

            var form = document.querySelector('form');
            var formData = new FormData(this);

            AxiosCall.post("{{ route('produk-store') }}", formData,
                "#produkForm");
        });

        // show update produk
        $(document).on('click', '.produkUpdate', function() {

            $('.offset-area').toggleClass('show_hide');
            $('.settings-btn').toggleClass('active');
            var key = $(this).data('id');
            var form = $('#produkForm');
            form.attr('data-type', 'update');

            // $.ajax({
            //     url: `profile/jabatan/${key}`,
            //     method: "GET",
            //     success: function(data) {
            //         let result = JSON.parse(data);

            //         if (result.status) {
            //             var res = result.data;

            //             $.each(res, function(key, value) {
            //                 $("select[name='" + key + "']").val(value);
            //                 $("input[name='" + key + "']").val(value);
            //                 $(`#${key}`).attr("src",
            //                     `{{ asset('storage/${value}') }}`);
            //             });
            //         }
            //     }
            // });
        });


        // format rupiah
        $('#harga_beli, #harga_jual, #harga_perdos').on('keyup', function() {
            $(this).val(formatRupiah($(this).val(), 'Rp. '));

            var jumlah_perdos = parseInt($('#jumlah_perdos').val());
            var harga_jual = parseInt($('#harga_jual').val().replace(/[^0-9]/g, ''));
            var harga_perdos = harga_jual * jumlah_perdos;

            $('#harga_perdos').val(formatRupiah(harga_perdos.toString(), 'Rp. '));
        });

        $('#jumlah_perdos').on('keyup', function() {
            var jumlah_perdos = parseInt($('#jumlah_perdos').val());
            var harga_jual = parseInt($('#harga_jual').val().replace(/[^0-9]/g, ''));
            var harga_perdos = harga_jual * jumlah_perdos;

            $('#harga_perdos').val(formatRupiah(harga_perdos.toString(), 'Rp. '));
        });



        $(document).ready(function() {
            dataRow.init();

        });
    </script>
@endsection
