@extends('layout.template')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Laporan Pembelian</h4>
                        <div class="form-row align-items-center">
                            <div class="col-sm-4">
                                <select class="form-control" id="bulan" name="bulan">
                                    <option value="all">SEMUA BULAN</option>
                                    @php
                                        $bulan = [1 => 'Januari', 'Febuari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    @endphp
                                    @foreach ($bulan as $index => $value)
                                        <option value="{{ $index }}">
                                            {{ Str::upper($value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-2 mt-2">
                                <button type="button" class="btn btn-primary mb-3 cetak">Excel</button>
                                <button type="button" class="btn btn-danger mb-3 lihat">Lihat</button>
                            </div>
                        </div>
                        <div class="data-tables">
                            <table id="pembelianTable" class="text-cente">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Tanggal</th>
                                        {{-- <th>Isi Perdos</th> --}}
                                        {{-- <th>Satuan</th> --}}
                                        {{-- <th>Harga Beli</th> --}}
                                        <th>Kios</th>
                                        <th>Pembelian</th>
                                        <th>Ket</th>
                                        {{-- <th>Stok</th>
                                        <th>Harga</th>
                                        <th>DPP</th>
                                        <th>PPN</th> --}}
                                        {{-- <th>Action</th> --}}
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

@section('scripts')
    <script>
        var bulan = $('#bulan').val();
        console.log(bulan);
        // datatable produk list
        var dataRow = function() {
            var init = function() {
                let table = $('#pembelianTable');
                table.DataTable({
                    processing: true,
                    ordering: false,
                    paging: false,
                    ajax: {
                        url: "/laporan/pembelian-laporan",
                        type: "GET",
                        data: {
                            "bulan": bulan
                        }
                    },
                    columns: [{
                            data: 'invoice',
                            render: function(data, type, row) {
                                return data.toUpperCase();
                            }
                        },
                        {
                            data: 'tanggal_beli',
                            render: function(data, type, row) {
                                return moment(new Date(data).toString()).format(
                                    'DD/MM/YYYY');
                            }
                        },
                        // {
                        //     data: 'jumlah_perdos',
                        //     render: function(data, type, row) {
                        //         return data;
                        //     }
                        // },
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
                        // {
                        //     data: 'pembelian',
                        //     render: function(data, type, row) {
                        //         return `${data} Dos`;
                        //     }
                        // },
                        {
                            data: 'supplier',
                            render: function(data, type, row) {
                                return `${data.nama_supplier.toUpperCase()}`;
                            }
                        },
                        {
                            data: 'detailPembelian',
                            render: function(data, type, row) {
                                console.log(data);
                                let html = '';
                                html += '<ul>';
                                $.each(data, function(x, y) {
                                    html +=
                                        `<li>${y.produk.toUpperCase()}, ${y.kemasan.toUpperCase()}<li>`;
                                })
                                html += '</ul>';
                                return html;
                            }
                        },
                        {
                            data: 'detailPembelian',
                            render: function(data, type, row) {
                                let html = '';
                                html += '<ul>';
                                $.each(data, function(x, y) {
                                    html +=
                                        `<li>${y.ket}<li>`;
                                })
                                html += '</ul>';
                                return html;
                            }
                        },
                    ],
                });

            };

            var destroy = function() {
                var table = $('#pembelianTable').DataTable();
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


        // trigger
        $('#bulan').on('change', function() {
            console.log($(this).val());
            bulan = $('#bulan').val();
            dataRow.destroy();
            dataRow.init();
        });



        $('.lihat').on('click', function() {

            let bulan = $('#bulan').val();
            console.log(bulan);

            url = `/laporan/pembelian-rekap/?bulan=${bulan}&jenis=pdf`;
            window.open(url);
            // if (bulan !== 'all') {
            // } else {
            //     Swal.fire(
            //         "Perhatian",
            //         "Pilih bulan terlebih dahulu",
            //         "warning"
            //     );
            // }

        });

        $('.cetak').on('click', function() {
            let bulan = $('#bulan').val();
            console.log(bulan);

            url = `/laporan/pembelian-rekap/?bulan=${bulan}&jenis=excel`;
            window.open(url);

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



        function number_format(number, decimals, decPoint, thousandsSep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
            var n = !isFinite(+number) ? 0 : +number
            var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
            var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
            var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
            var s = ''

            var toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec)
                return '' + (Math.round(n * k) / k)
                    .toFixed(prec)
            }

            // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || ''
                s[1] += new Array(prec - s[1].length + 1).join('0')
            }

            return s.join(dec)
        }



        $(document).ready(function() {
            dataRow.init();
            $('#bulan').select2();

        });
    </script>
@endsection
