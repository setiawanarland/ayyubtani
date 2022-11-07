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
                            <table id="pajakTable" class="text-cente">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th>Nama Pajak</th>
                                        <th>Satuan Pajak</th>
                                        <th>Active</th>
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

                    <form id="pajakForm" data-type="submit">
                        @csrf

                        <input class="form-control" type="hidden" name="id" id="id">

                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="nama_pajak" class="col-form-label">Nama Pajak</label>
                            <input class="form-control" type="text" name="nama_pajak" id="nama_pajak" autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="satuan_pajak" class="col-form-label">Satuan Pajak</label>
                            <input class="form-control" type="text" name="satuan_pajak" id="satuan_pajak">
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
                let table = $('#pajakTable');
                table.DataTable({
                    processing: true,
                    ordering: false,
                    ajax: "{{ route('pajak-list') }}",
                    columns: [{
                            data: 'nama_pajak',
                            render: function(data, type, row) {
                                return data.toUpperCase();
                            }
                        },
                        {
                            data: 'satuan_pajak',
                            // render: function(data, type, row) {
                            //     return data.toUpperCase();
                            // }
                        },
                        {
                            data: 'active',
                            render: function(data, type, row) {
                                let active;
                                active = (data == 1) ? 'checked' : '';
                                return `<input type="checkbox" ${active} data-id="${row.id}" class="status" id="">`
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
                            <a role="button" href="javascript:;" type="button" data-id="${row.id}" class="btn btn-warning btn-sm pajakUpdate"><i class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-danger btn-sm btn-delete pajakDelete" data-id="${row.id}"><i class="fa fa-trash"></i></button>
                    `;
                        },
                    }],
                });

            };

            var destroy = function() {
                var table = $('#pajakTable').DataTable();
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
                                    var form = $('#pajakForm');
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
                                    var form = $('#pajakForm');
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
            var form = $('#pajakForm');
            form.attr('data-type', 'submit');
            form[0].reset();
        });

        // create produk
        $(document).on('submit', "#pajakForm[data-type='submit']", function(e) {
            e.preventDefault();

            var form = document.querySelector('form');
            var formData = new FormData(this);

            AxiosCall.post("{{ route('pajak-store') }}", formData,
                "#pajakForm");
        });


        // show update produk
        $(document).on('click', '.pajakUpdate', function() {

            $('.offset-area').toggleClass('show_hide');
            $('.settings-btn').toggleClass('active');
            var key = $(this).data('id');
            var form = $('#pajakForm');
            form.attr('data-type', 'update');

            var key = $(this).data('id');
            axios.get('pajak/show/' + key)
                .then(function(res) {
                    let data = res.data;
                    // console.log(data);
                    $.map(data.data, function(val, i) {
                        let value = val;

                        $("input[name=" + i + "]").val(val);
                        $("input[name=" + i + "]").attr('style', 'text-transform: uppercase');
                        $("input[name=" + i + "]");


                    })
                })
                .catch(function(err) {

                });
        });


        // edit produk
        $(document).on('submit', "#pajakForm[data-type='update']", function(e) {
            e.preventDefault();
            console.log($(this));

            var _id = $("input[name='id']").val();
            var form = document.querySelector('form');
            var formData = new FormData(this);

            AxiosCall.update("{{ route('pajak-update') }}", formData,
                "#pajakForm");
        });


        // active
        $(document).on('click', '.status', function(e) {
            e.preventDefault()
            let id = $(this).attr('data-id');
            console.log(id);
            Swal.fire({
                title: 'Apakah kamu yakin akan mengubah status data ini ?',
                text: "Status akan diubah",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/pajak/active/${id}`,
                        type: 'POST',
                        data: {
                            '_method': 'POST',
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(response) {
                            if (response.status !== false) {
                                Swal.fire('Success!',
                                        'Status berhasil diubah.',
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


        // delete produk
        $(document).on('click', '.pajakDelete', function(e) {
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
                        url: `/produk/delete/${id}`,
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
