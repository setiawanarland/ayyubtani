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
                            <table id="kiosTable" class="text-center">
                                <thead class="bg-light text-capitalize">
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Office</th>
                                        <th>Age</th>
                                        <th>Start Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Office</th>
                                        <th>Age</th>
                                        <th>Start Date</th>
                                        <th>Action</th>
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

                    <form action="#">
                        <div class="form-group">
                            <label for="example-text-input" class="col-form-label">Text</label>
                            <input class="form-control" type="text" value="Carlos Rath" id="example-text-input">
                        </div>
                        <b class="text-muted mb-3 d-block">Radios:</b>
                        <div class="custom-control custom-radio">
                            <input type="radio" checked id="customRadio1" name="customRadio" class="custom-control-input">
                            <label class="custom-control-label" for="customRadio1">Checked Radios</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input">
                            <label class="custom-control-label" for="customRadio2">Unchecked Radios</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" checked disabled id="customRadio3" name="customRadio33"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio3">Disabled Radios</label>
                        </div>
                        <b class="text-muted mb-3 mt-4 d-block">Inline Radios:</b>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" checked id="customRadio4" name="customRadio2"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio4">Checked Radios</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadio5" name="customRadio2" class="custom-control-input">
                            <label class="custom-control-label" for="customRadio5">Unchecked Radios</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" checked id="customRadio1" name="customRadio" class="custom-control-input">
                            <label class="custom-control-label" for="customRadio1">Checked Radios</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input">
                            <label class="custom-control-label" for="customRadio2">Unchecked Radios</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" checked disabled id="customRadio3" name="customRadio33"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio3">Disabled Radios</label>
                        </div>
                        <b class="text-muted mb-3 mt-4 d-block">Inline Radios:</b>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" checked id="customRadio4" name="customRadio2"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio4">Checked Radios</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadio5" name="customRadio2" class="custom-control-input">
                            <label class="custom-control-label" for="customRadio5">Unchecked Radios</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" checked disabled id="customRadio6" name="customRadio3"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio6">Disabled Radios</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" checked id="customRadio1" name="customRadio"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio1">Checked Radios</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input">
                            <label class="custom-control-label" for="customRadio2">Unchecked Radios</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" checked disabled id="customRadio3" name="customRadio33"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio3">Disabled Radios</label>
                        </div>
                        <b class="text-muted mb-3 mt-4 d-block">Inline Radios:</b>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" checked id="customRadio4" name="customRadio2"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio4">Checked Radios</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadio5" name="customRadio2" class="custom-control-input">
                            <label class="custom-control-label" for="customRadio5">Unchecked Radios</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" checked disabled id="customRadio6" name="customRadio3"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio6">Disabled Radios</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" checked id="customRadio1" name="customRadio"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio1">Checked Radios</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input">
                            <label class="custom-control-label" for="customRadio2">Unchecked Radios</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" checked disabled id="customRadio3" name="customRadio33"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio3">Disabled Radios</label>
                        </div>
                        <b class="text-muted mb-3 mt-4 d-block">Inline Radios:</b>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" checked id="customRadio4" name="customRadio2"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio4">Checked Radios</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadio5" name="customRadio2" class="custom-control-input">
                            <label class="custom-control-label" for="customRadio5">Unchecked Radios</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" checked disabled id="customRadio6" name="customRadio3"
                                class="custom-control-input">
                            <label class="custom-control-label" for="customRadio6">Disabled Radios</label>
                        </div>

                        <div class="form-group" style="margin-top: 790%">
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
        var dataRow = function() {
            var init = function() {
                let table = $('#kiosTable');
                table.DataTable({
                    processing: true,
                    ordering: false,
                    ajax: "",
                    columns: [{
                            data: 'nama_pendidikan'
                        },
                        {
                            data: 'fakultas'
                        },
                        {
                            data: 'jurusan'
                        },
                        {
                            data: 'nomor_ijazah'
                        },
                        {
                            data: 'tanggal_ijazah',
                            render: function(data, type, row) {
                                if (type === "sort" || type === "type") {
                                    return data;
                                }
                                return moment(data).format("MM-DD-YYYY");
                            }
                        },
                        {
                            data: 'nama_kepala_sekolah'
                        },
                        {
                            data: 'nama_sekolah'
                        },
                        {
                            data: 'alamat_sekolah'
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
                            <a role="button" href="javascript:;" type="button" data-id="${row.id}" class="btn btn-warning btn-sm formal_update">Ubah</a>
                            <button type="button" class="btn btn-danger btn-sm btn-delete formal_delete" data-id="${row.id}">Hapus</button>
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

        $('.tambahData, .btn-cancel').on('click', function() {
            $('.offset-area').toggleClass('show_hide');
            $('.settings-btn').toggleClass('active');
            console.log("ok");
        });




        $(document).ready(function() {
            dataRow.init();

        });
    </script>
@endsection
