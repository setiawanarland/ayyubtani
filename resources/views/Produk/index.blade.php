@extends('layout.template')

@section('content')
    <div class="main-content-inner">


        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Data Produk</h4>
                        <label for="supplier_id_cetak" class="col-form-label">Pilih Supplier untuk cetak data</label>
                        <div class="col-4 mt-2 align-items-center">
                            <select class="form-control mb-1" id="supplier_id_cetak" name="supplier_id_cetak">
                                @foreach ($supplier as $index => $value)
                                    <option value="{{ $value->id }}">{{ Str::upper($value->nama_supplier) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                            <button type="button" class="btn d-inline btn-primary mb-2 tambahData">Tambah
                                Data</button>
                            <button type="button" class="btn d-inline btn-success mb-2 cetak">Cetak
                                Data</button>
                            <button type="button" class="btn d-inline btn-warning mb-2 rekapTahunan">
                                Rekap
                            </button>
                        </div>
                        <div class="data-tables">
                            <table id="produkTable" class="text-cente">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Kemasan</th>
                                        <th>Isi Perdos</th>
                                        {{-- <th>Satuan</th> --}}
                                        {{-- <th>Harga Beli</th> --}}
                                        <th>Harga Satuan</th>
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
                        @csrf

                        <input class="form-control" type="hidden" name="id" id="id">

                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="nama_produk" class="col-form-label">Supplier</label>
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
                            <input class="form-control" type="text" name="qty_kemasan" id="qty_kemasan" value="0">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="qty_perdos" class="col-form-label">Qty Perdos</label>
                            <input class="form-control" type="text" name="qty_perdos" id="qty_perdos" value="0">
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
                            data: 'jumlah_perdos',
                            render: function(data, type, row) {
                                return data;
                            }
                        },
                        // {
                        //     data: 'satuan',
                        //     render: function(data, type, row) {
                        //         return data.toUpperCase();
                        //     }
                        // },
                        // {
                        //     data: 'harga_beli',
                        //     render: function(data, type, row) {
                        //         return formatRupiah(data.toString(), '');
                        //     }
                        // },
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
                                let satuanKemasan = (row.satuan == "ltr") ? "Btl" : "Bks";
                                let ketKemasan = row.jumlah_perdos;
                                let qtyKemasan = row.qty_kemasan;
                                let qtyTotal = row.qty;
                                let ketTotal = Math.round(qtyTotal / qtyKemasan);
                                let ketLeft = ketTotal % ketKemasan;
                                let stok = (ketLeft > 0) ? "" + (ketTotal - ketLeft) / ketKemasan +
                                    " Dos " + ketLeft + " " + satuanKemasan + "" : "" + (ketTotal -
                                        ketLeft) /
                                    ketKemasan +
                                    " Dos";

                                return stok;
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

            var key = $(this).data('id');
            axios.get('produk/show/' + key)
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


        // edit produk
        $(document).on('submit', "#produkForm[data-type='update']", function(e) {
            e.preventDefault();
            console.log($(this));

            var _id = $("input[name='id']").val();
            var form = document.querySelector('form');
            var formData = new FormData(this);

            AxiosCall.update("{{ route('produk-update') }}", formData,
                "#produkForm");
        });


        // delete produk
        $(document).on('click', '.produkDelete', function(e) {
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


        $('.cetak').on('click', function() {
            const d = new Date();
            let supplier_id = $('#supplier_id_cetak').val();
            let bulan = d.getMonth() + 1;
            console.log(bulan);

            url = `/produk/cetak/?supplier_id=${supplier_id}&bulan=${bulan}&jenis=excel`;
            window.open(url);

        });

        // rekap Tahunan
        $(document).on('click', ".rekapTahunan", function(e) {
            e.preventDefault();

            let year = {!! json_encode(session('tahun')) !!};


            Swal.fire({
                title: `Apakah kamu yakin akan melakukan rekap data stok tahun ${year}?`,
                text: "Data akan direkap!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/produk/rekap-tahunan?tahun=${year}`,
                        type: 'POST',
                        data: {
                            '_method': 'POST',
                            '_token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire('Success!',
                                    'Data berhasil direkap.',
                                    'success')
                                .then(function() {
                                    $('#bulan').val('null').trigger('change');
                                    dataRow.destroy();
                                    dataRow.init();
                                });
                        },
                        error: function(res) {
                            swal.fire({
                                title: "Failed!",
                                text: `${res.responseJSON.message}`,
                                icon: "warning",
                            });
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
            $('#bulan').select2();
        });
    </script>
@endsection
