@extends('layout.template')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Data Tambah Stok</h4>
                        <button type="button" class="btn d-flex btn-primary mb-3 pull-right tambahData">Tambah Data</button>
                        <div class="data-tables">
                            <table id="tambahStokTable" class="text-cente">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Kemasan</th>
                                        <th>Jumlah Tambah</th>
                                        <th>Ket.</th>
                                        <th>Tanggal</th>
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
                <h6 class="active">Tambah Data</h6>
            </li>
        </ul>
        <div class="offset-content tab-content">
            <div id="activity" class="tab-pane fade in show active">
                <div class="recent-activity">

                    <form id="tambahStokForm" data-type="submit">
                        @csrf

                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="nama_produk" class="col-form-label">Nama Produk</label>
                            <select class="form-control" id="produk" name="produk">
                                <option value="null">Pilih Produk</option>
                                @foreach ($produk as $index => $value)
                                    <option value="{{ $value->id }}">
                                        {{ Str::upper($value->nama_produk) }} {{ Str::upper($value->kemasan) }} |
                                        Stok {{ $value->stok }} dos
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="stok" class="col-form-label">Stok</label>
                            <input class="form-control" type="number" name="stok" id="stok">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="qty_dos" class="col-form-label">Qty/dos</label>
                            <input class="form-control" type="text" name="qty_dos" id="qty_dos">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="stok_tambah" class="col-form-label">Stok Tambah</label>
                            <input class="form-control" type="number" name="stok_tambah" id="stok_tambah">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="stok_after" class="col-form-label">Stok After</label>
                            <input class="form-control" type="number" name="stok_after" id="stok_after">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="ket" class="col-form-label">Ket.</label>
                            <input class="form-control" type="text" name="ket" id="ket">
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
                let table = $('#tambahStokTable');
                table.DataTable({
                    processing: true,
                    ordering: false,
                    ajax: "{{ route('list-tambah-stok') }}",
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
                            data: 'jumlah',
                            render: function(data, type, row) {
                                return `${data} Dos`;
                            }
                        },
                        {
                            data: 'alasan',
                            render: function(data, type, row) {
                                return data.toUpperCase();
                            }
                        },
                        {
                            data: 'created_at',
                            render: function(data, type, row) {
                                let date = new Date(data);
                                const day = date.toLocaleString('default', {
                                    day: '2-digit'
                                });
                                const month = date.toLocaleString('default', {
                                    month: 'short'
                                });
                                const year = date.toLocaleString('default', {
                                    year: 'numeric'
                                });
                                return day + '-' + month + '-' + year;
                            }
                        },
                    ],
                });

            };

            var destroy = function() {
                var table = $('#tambahStokTable').DataTable();
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
                                    var form = $('#tambahStokForm');
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
            var form = $('#tambahStokForm');
            form.attr('data-type', 'submit');
            form[0].reset();
        });

        // get produk info
        $(document).on('change', '#produk', function() {
            let produkId = $(this).val();
            $.ajax({
                url: `/setting/produk/${produkId}`,
                type: 'GET',
                data: {
                    '_method': 'GET',
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    let qty = `${response.data.qty_perdos} ${response.data.satuan}`;
                    $('#stok').val(response.data.stok);
                    $('#qty_dos').val(qty);

                    $('#stok_tambah').focus();
                }
            })
        });

        // calculate stok
        $(document).on('keyup', '#stok_tambah', function() {
            let stokTambah = $(this).val();
            let stok = $('#stok').val();
            let qtyDos = $('#qty_dos').val();

            let stokAfter = parseInt(stok) + parseInt(stokTambah);
            $('#stok_after').val(stokAfter);
        });

        // process tambah stok
        $(document).on('submit', "#tambahStokForm[data-type='submit']", function(e) {
            e.preventDefault();

            var form = document.querySelector('form');
            var formData = new FormData(this);

            AxiosCall.post("{{ route('process-tambah') }}", formData,
                "#tambahStokForm");
        });





        $(document).ready(function() {
            dataRow.init();
            $('#produk').select2();
        });
    </script>
@endsection
