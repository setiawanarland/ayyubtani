@extends('layout.template')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Data Kios</h4>
                        <button type="button" class="btn d-flex btn-primary mb-3 pull-right tambahData">Tambah Data</button>
                        <div class="data-tables">
                            <table id="kiosTable" class="text-cente">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th>Nama Kios</th>
                                        <th>Pemilik</th>
                                        <th>Alamat</th>
                                        <th>NPWP</th>
                                        <th>NIK</th>
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
                let table = $('#kiosTable');
                table.DataTable({
                    processing: true,
                    ordering: false,
                    ajax: "{{ route('kios-list') }}",
                    columns: [{
                            data: 'nama_kios',
                            render: function(data, type, row) {
                                return data.toUpperCase();
                            }
                        },
                        {
                            data: 'pemilik',
                            render: function(data, type, row) {
                                return data.toUpperCase();
                            }
                        },
                        {
                            data: 'alamat',
                            render: function(data, type, row) {
                                return data.toUpperCase() + ", " + row.kabupaten.toUpperCase();
                            }
                        },
                        {
                            data: 'npwp',
                            render: function(data, type, row) {
                                return data.toUpperCase();
                            }
                        },
                        {
                            data: 'nik',
                            render: function(data, type, row) {
                                return data.toUpperCase();
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
                            <a role="button" href="javascript:;" type="button" data-id="${row.id}" class="btn btn-warning btn-sm kiosUpdate"><i class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-danger btn-sm btn-delete kiosDelete" data-id="${row.id}"><i class="fa fa-trash"></i></button>
                    `;
                        },
                    }],
                });

            };

            var destroy = function() {
                var table = $('#kiosTable').DataTable();
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
                                    var form = $('#kiosForm');
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
                                    var form = $('#kiosForm');
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
            };
        }();


        // trigger form
        $('.tambahData, .btn-cancel').on('click', function() {
            $('.offset-area').toggleClass('show_hide');
            $('.settings-btn').toggleClass('active');
            var form = $('#kiosForm');
            form.attr('data-type', 'submit');
            form[0].reset();
        });

        // create supplier
        $(document).on('submit', "#kiosForm[data-type='submit']", function(e) {
            e.preventDefault();

            var form = document.querySelector('form');
            var formData = new FormData(this);

            AxiosCall.post("{{ route('kios-store') }}", formData,
                "#kiosForm");
        });


        // show update supplier
        $(document).on('click', '.kiosUpdate', function() {
            console.log($(this));
            $('.offset-area').toggleClass('show_hide');
            $('.settings-btn').toggleClass('active');
            var key = $(this).data('id');
            var form = $('#kiosForm');
            form.attr('data-type', 'update');

            var key = $(this).data('id');
            axios.get('kios/show/' + key)
                .then(function(res) {
                    let data = res.data;
                    // console.log(data);
                    $.map(data.data, function(val, i) {
                        let value = val;
                        if ((i == 'harga_beli') || (i == 'harga_jual') || (i == 'harga_perdos')) {
                            $("input[name=" + i + "]").val(formatRupiah(value.toString()));
                        } else {
                            $("input[name=" + i + "]").val(val);
                            $("input[name=" + i + "]").attr('style', 'text-transform: uppercase');
                        }

                    })
                })
                .catch(function(err) {

                });
        });


        // edit supplier
        $(document).on('submit', "#kiosForm[data-type='update']", function(e) {
            e.preventDefault();
            console.log($(this));

            var _id = $("input[name='id']").val();
            var form = document.querySelector('form');
            var formData = new FormData(this);

            AxiosCall.update("{{ route('kios-update') }}", formData,
                "#kiosForm");
        });


        // delete supplier
        $(document).on('click', '.kiosDelete', function(e) {
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
                        url: `/kios/delete/${id}`,
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
        $('#npwpp').on('keyup', function() {
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
