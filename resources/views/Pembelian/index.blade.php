@extends('layout.template')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Pembelian</h4>
                        <form>
                            <div class="form-row align-items-center">
                                <div class="col-sm-3 my-1">
                                    <label class="" for="supplier">Supplier</label>
                                    <select class="form-control" id="supplier" name="supplier">
                                        <option value="null">Pilih Supplier</option>
                                        @foreach ($supplier as $index => $value)
                                            <option value="{{ $value->id }}">{{ Str::upper($value->nama_supplier) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
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
                                    <input type="number" class="form-control" id="ket" name="ket" value="0">
                                </div>
                                <div class="col-sm-3 col-md-1 my-1">
                                    <label class="" for="disc">Disc</label>
                                    <input type="number" class="form-control" id="disc" name="disc" value="0">
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
                        <button type="button" class="btn d-flex btn-danger mb-3 pull-right tambahData">Reset Keranjang
                        </button>
                        <div class="data-tables">
                            <table id="detailPembelianTable" class="text-cente">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Produk</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                        <th>Harga Stn.</th>
                                        <th>Ket.</th>
                                        <th>Disc.</th>
                                        <th>Jumlah</th>
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
                                        <td>123.456.789</td>
                                    </tr>
                                    <tr>
                                        <td>PPN</td>
                                        <td style="padding-left:130px; padding-right:3px;">:</td>
                                        <td>123.456.789</td>
                                    </tr>
                                    <tr>
                                        <td>Discount</td>
                                        <td style="padding-left:130px; padding-right:3px;">:</td>
                                        <td>0</td>
                                    </tr>
                                    <tr>
                                        <th>GRAND TOTAL</th>
                                        <th style="padding-left:130px; padding-right:3px;">:</th>
                                        <th>123.456.789</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
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
                    ajax: "{{ route('pembeliantemp-list') }}",
                    columns: [{
                            data: 'id'
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
                        },
                        {
                            data: 'satuan',
                            render: function(data, type, row) {
                                return data.toUpperCase();
                            }
                        },
                        {
                            data: 'harga_jual',
                            render: function(data, type, row) {
                                return formatRupiah(data.toString(), '');
                            }
                        },
                        {
                            data: 'ket',
                            render: function(data, type, row) {
                                return data + ' Dos';
                            }
                        },
                        {
                            data: 'disc',
                        },
                        {
                            data: 'jumlah',
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


        // add pembelian temp
        $(document).on('click', '.addTemp', function(e) {
            let supplier_id = $('#supplier').val();
            let produk_id = $('#produk').val();
            let ket = $('#ket').val();
            let disc = $('#disc').val();
            console.log(supplier_id);
            console.log(produk_id);

            if ((supplier_id != 'null') && (produk_id != 'null')) {
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
                        } else {

                        }
                    }
                })
            } else {
                swal.fire({
                    text: "Silakan Pilih Supplier dan Produk",
                    title: "Error",
                    icon: "error",
                    showConfirmButton: true,
                    confirmButtonText: "OK",
                })
            }
        });


        $(document).ready(function() {
            dataRow.init();
            $('#supplier').select2();
            $('#produk').select2();
        });
    </script>
@endsection
