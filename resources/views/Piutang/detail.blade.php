@extends('layout.template')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Detail Piutang</h4>
                        <button type="button" class="btn btn-sm bayarPiutang pull-right mb-2" data-toggle="modal"
                            data-target="#bayarPiutangModal" data-id="{{ $kios->id }}"
                            style="background-color:forestgreen" title="Bayar Piutang">
                            <i style="color:#fff;" class="fa fa-money"></i>
                        </button>

                        <div class="data-tables">
                            <table id="piutangTable" class="text-center table table-bordered">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        {{-- @dd($data->piutang) --}}
                                        <th colspan="6">{{ Str::upper($kios->pemilik) }},
                                            {{ Str::upper($kios->nama_kios) }},
                                            {{ Str::upper($kios->kabupaten) }}</th>
                                    </tr>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tgl. Transaksi</th>
                                        <th>Invoice</th>
                                        <th>Debet</th>
                                        <th>Ket.</th>
                                        <th>Detail Bayar</th>
                                        <th>Tgl. Bayar</th>
                                        <th>Status</th>
                                        <th>Sisa</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @foreach ($kios->transaksi as $key => $value)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $value['tanggal_transaksi'] }}</td>
                                            <td>{{ $value['invoice'] }}</td>
                                            <td class="text-right">{{ number_format($value['debet']) }}</td>
                                            <td>
                                                @if ($value['count'] > 0)
                                                    @foreach ($value['bayar_piutang'] as $item)
                                                        {{ strtoupper($item['ket']) }}
                                                        <br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if ($value['count'] > 0)
                                                    @foreach ($value['bayar_piutang'] as $item)
                                                        {{ number_format($item['total_bayar']) }}
                                                        <br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if ($value['count'] > 0)
                                                    @foreach ($value['bayar_piutang'] as $item)
                                                        {{ $item['tanggal_bayar'] }}
                                                        <br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if ($value['status_lunas'] == '1')
                                                    <button type="button" class="btn btn-sm btn-success" title="Lunas"
                                                        disabled>
                                                        <i style="color:#fff;" class="fa fa-check"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-danger" title="Belum"
                                                        disabled>
                                                        <i style="color:#fff;" class="fa fa-close"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="text-right">{{ number_format($value['total']) }}</td>
                                        </tr>
                                    @endforeach
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
    <div class="modal fade" id="bayarPiutangModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bayar Piutang <br>
                        {{ Str::upper($kios->pemilik) }},
                        {{ Str::upper($kios->nama_kios) }},
                        {{ Str::upper($kios->kabupaten) }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="bayarPiutangForm" data-type="submit">
                        @csrf

                        <input class="form-control" type="hidden" name="kios_id" id="kios_id"
                            value="{{ $kios->id }}">

                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="totalPiutang" class="col-form-label">Total Piutang</label>
                            <input type="text" class="form-control" id="totalPiutang" name="totalPiutang"
                                value="{{ $totalPiutang }}" disabled>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="tanggal_bayar" class="col-form-label">Tanggal Bayar</label>
                            <input type="text" class="form-control" id="tanggal_bayar" name="tanggal_bayar">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="keterangan" class="col-form-label">Keterangan</label>
                            <input class="form-control" type="text" name="keterangan" id="keterangan">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="total" class="col-form-label">Total Bayar</label>
                            <input class="form-control" type="text" name="total" id="total">
                            <div class="invalid-feedback"></div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Bayar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // datatable produk list
        var dataRow = function() {
            var init = function() {
                let table = $('#piutangTable');
                var tahun = null;
                var bulan = null;
                var totalHutang = 0;

                table.DataTable({
                    ordering: false,
                    paging: false,
                });

            };

            var destroy = function() {
                var table = $('#piutangTable').DataTable();
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
                                    $('#bayarPiutangModal').modal('toggle');

                                    window.location = "{{ url('/piutang/detail-piutang') }}/" + data
                                        .data.kios_id;
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

        $('#total').on('keyup', function() {
            $(this).val(formatRupiah($(this).val(), ''));
        });

        $(document).on('submit', "#bayarPiutangForm[data-type='submit']", function(e) {
            e.preventDefault();

            var form = document.querySelector('form');
            var formData = new FormData(this);

            AxiosCall.post("{{ route('bayar-piutang') }}", formData,
                "#bayarPiutangForm");
        });

        $(document).ready(function() {
            dataRow.init();

            $('#tanggal_bayar').datepicker({
                format: "dd-mm-yyyy",
                weekStart: 1,
                daysOfWeekHighlighted: "6,0",
                autoclose: true,
                todayHighlight: true,
            });
            $('#tanggal_bayar').datepicker("setDate", new Date());
            let totalPiutang = $('#totalPiutang').val();
            $('#totalPiutang').val(formatRupiah(totalPiutang, ''));

        });
    </script>
@endsection
