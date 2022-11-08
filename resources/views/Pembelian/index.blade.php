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

                                <div class="col-sm-3 my-1">
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
                                        <option value="null">Pilih Supplier</option>
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
                                            <th>Qty</th>
                                            <th>Satuan</th>
                                            <th width="15%">Harga Stn.</th>
                                            <th width="9%">Ket.</th>
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
                                        <tr>
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
                                        </tr>
                                        <tr>
                                            <th>GRAND TOTAL</th>
                                            <th style="padding-left:130px; padding-right:3px;">:</th>
                                            <th class="grand_total">
                                                <input type="text" class="form-control" id="grand_total"
                                                    name="grand_total" value="">
                                            </th>
                                        </tr>
                                    </thead>
                                </table>

                                <div class="form-group" style="margin-top: 10px;">
                                    <button class="btn btn-primary" type="submit">Save</button>
                                    <button class="btn btn-danger btn-cancel printPreview" type="">Print</button>
                                </div>
                            </div>

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
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        {
                            data: 'id_produk',
                            render: function(data, type, row) {
                                return `
                                <input type="text" class="form-control" id="produk_id" name="produk_id[]" value="` +
                                    row.nama_produk.toUpperCase() + ", " + row.kemasan
                                    .toUpperCase() + `">`;
                            }
                        },
                        {
                            data: 'qty',
                            render: function(data, type, row) {
                                return `
                                    <input type="text" class="form-control qty" id="qty" name="qty[]" value="` + data + `">
                                `;
                            }
                        },
                        {
                            data: 'satuan',
                            render: function(data, type, row) {
                                return `
                                    <input type="text" class="form-control satuan" id="satuan" name="satuan[]" value="` +
                                    data.toUpperCase() + `">
                                `;
                            }
                        },
                        {
                            data: 'harga_jual',
                            render: function(data, type, row) {
                                return `
                                    <input type="text" class="form-control harga_jual" id="harga_jual" name="harga_jual[]" value="` +
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
                            render: function(data, type, row) {
                                return `
                                <input type="text" class="form-control disc" id="disc" name="disc[]" value="` +
                                    data + `">
                                `;
                            }
                        },
                        {
                            data: 'jumlah',
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
                        width: '5rem',
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
                print: function(_url, _data, _element) {
                    axios.post(_url, _data)
                        .then(function(res) {
                            console.log(res.data);
                            // var data = res.data;
                            // if (data.fail) {
                            //     swal.fire({
                            //         text: "Maaf Terjadi Kesalahan",
                            //         title: "Error",
                            //         timer: 2000,
                            //         icon: "danger",
                            //         showConfirmButton: false,
                            //     });
                            // } else if (data.invalid) {
                            //     console.log(data);
                            //     $.each(data.invalid, function(key, value) {
                            //         console.log(key);
                            //         console.log('errorType', typeof error);
                            //         $("input[name='" + key + "']").addClass('is-invalid').siblings(
                            //             '.invalid-feedback').html(value[0]);
                            //     });
                            // } else if (data.success) {
                            //     swal.fire({
                            //         text: "Data anda berhasil disimpan",
                            //         title: "Sukses",
                            //         icon: "success",
                            //         showConfirmButton: true,
                            //         confirmButtonText: "OK, Siip",
                            //     }).then(function() {
                            //         $('.offset-area').toggleClass('show_hide');
                            //         $('.settings-btn').toggleClass('active');
                            //         var form = $('#produkForm');
                            //         form[0].reset();
                            //         dataRow.destroy();
                            //         dataRow.init();
                            //     });
                            // }
                        }).catch(function(error) {
                            swal.fire({
                                text: "Terjadi Kesalahan Sistem",
                                title: "Error",
                                icon: "error",
                                showConfirmButton: true,
                                confirmButtonText: "OK",
                            })
                        });
                }
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
                        if (response.status !== false) {
                            dataRow.destroy();
                            dataRow.init();

                            $('#supplier').val('null').trigger('change');
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
                    $.ajax({
                        url: `/pembelian/tempdelete/${id}`,
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

                                        $('#supplier').val('null').trigger('change');
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
                    $.ajax({
                        url: `/pembelian/tempreset`,
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

                                        $('#supplier').val('null').trigger('change');
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


        $(document).on('click', '.printPreview', function(e) {
            e.preventDefault();
            var form = $('#pembelianForm');
            form.attr('data-type', 'print');

            var dataForm = document.querySelector('#pembelianForm');
            var formData = new FormData(dataForm);

            AxiosCall.print("{{ route('pembelian-preview') }}", formData,
                "#produkForm");

            var html = `

                            <table id="" class="text-cente">
                                <tr>
                                    <td>PT. TIGA GENERASI</td>
                                </tr>
                                
                                    <tr>
                                        <th>No.</th>
                                        <th width="25%">Nama Produk</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                        <th width="15%">Harga Stn.</th>
                                        <th width="9%">Ket.</th>
                                        <th width="8%">Disc.</th>
                                        <th width="15%">Jumlah</th>
                                        <th>#</th>
                                    </tr>
                                
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="data-tables pull-right">
                            <table>
                                <thead>
                                    <tr>
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
                                    </tr>
                                    <tr>
                                        <th>GRAND TOTAL</th>
                                        <th style="padding-left:130px; padding-right:3px;">:</th>
                                        <th class="grand_total">
                                            <input type="text" class="form-control" id="grand_total"
                                                name="grand_total" value="">
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                   `;
            var popupWin = window.open('', '_blank', 'width=500,height=500');
            popupWin.document.open();
            popupWin.document.write('<html><body onload="window.print()">' + html + '</h1></html>');
            popupWin.document.close();
        });


        $(document).ready(function() {
            dataRow.init();

            $('#supplier').select2();
            $('#produk').select2();

            $('#tanggal_beli').datepicker({
                weekStart: 1,
                daysOfWeekHighlighted: "6,0",
                autoclose: true,
                todayHighlight: true,
            });
            $('#tanggal_beli').datepicker("setDate", new Date());
        });
    </script>
@endsection
