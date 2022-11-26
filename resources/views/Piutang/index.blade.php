@extends('layout.template')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Data Piutang</h4>

                        <div class="data-tables">
                            <table id="piutangTable" class="text-center table table-bordered">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th>Nama Kios</th>
                                        <th>Debet</th>
                                        <th>Kredit</th>
                                        <th>Sisa Hutang</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['kios'] as $key => $value)
                                        <tr style="background-color: aquamarine">
                                            <td class="text-left">{{ Str::upper($value->nama_kios) }}</td>
                                            <td class="text-right">{{ number_format($value->debet, 1) }}</td>
                                            <td class="text-right">{{ number_format($value->kredit, 1) }}</td>
                                            <td class="text-right">{{ number_format($value->sisa, 1) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm bayarPiutang" data-toggle="modal"
                                                    data-target="#bayarPiutangModal" data-id="{{ $value->id }}}"
                                                    style="background-color:forestgreen"
                                                    {{ $value->sisa <= 0 ? 'disabled' : '' }}><i
                                                        class="fa fa-money"></i></button>
                                            </td>
                                            {{-- <td>{{ $val->invoice }}</td>
                                            <td class="text-right">{{ number_format($val->debet) }}</td>
                                            <td>
                                                @if ($val->tanggal_bayar != null)
                                                    {{ date('d-m-Y', strtotime($val->tanggal_bayar)) }}
                                                @endif
                                            </td>
                                            <td>{{ $val->ket }}</td>
                                            <td class="text-right">{{ number_format($val->kredit) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm bayarPiutang" data-toggle="modal"
                                                    data-target="#bayarPiutangModal" data-id="{{ $val->id }}}"
                                                    style="background-color:forestgreen"
                                                    {{ $val->sisa <= 0 ? 'disabled' : '' }}><i
                                                        class="fa fa-money"></i></button>
                                            </td> --}}
                                        </tr>

                                        {{-- <tr class="text-uppercase"
                                            style="background-color: {{ $value['sisa'] != 0 ? 'lightsalmon' : 'green' }} ">
                                            <th colspan="2">total hutang bulan {{ $key }}</th>
                                            <th class="text-right">{{ number_format($value['debet']) }}</th>
                                            <th colspan="3">total sisa bulan {{ $key }}</th>
                                            <th class="text-right">{{ number_format($value['sisa']) }}</th>
                                            @if ($value['sisa'] != 0)
                                                <th>belum lunas</th>
                                            @else
                                                <th>lunas</th>
                                            @endif
                                        </tr> --}}
                                    @endforeach

                                    <tr class="text-uppercase pt-5"
                                        style="background-color: {{ $data['total_piutang'] != 0 ? 'gold' : 'lightgreen' }} ">
                                        <th colspan="1">total piutang </th>
                                        <th class="text-right">{{ number_format($data['total_piutang']) }}</th>
                                        <th colspan="1">total sisa </th>
                                        <th class="text-right">{{ number_format($data['total_sisa']) }}</th>
                                        <th class="text-right"></th>
                                    </tr>

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
                            <label for="nama_kios" class="col-form-label">Nama
                                Kios</label>
                            <input class="form-control" type="text" name="nama_kios" id="nama_kios" autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="pemilik" class="col-form-label">Nama
                                Pemilik</label>
                            <input class="form-control" type="text" name="pemilik" id="pemilik" autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="kabupaten" class="col-form-label">Wil.
                                Kabupaten</label>
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






        // format npwp
        $(document).on('click', '.bayarPiutang', function() {
            console.log('ok');
            window.location = "{{ route('hutang') }}";
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
