<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', $page_title ?? 'Ayyub Tani')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('page_description', $page_description ?? 'Ayyub Tani page describtion')" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" type="image/png" href="{{ asset('images/icon/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/metisMenu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/slicknav.min.css') }}">
    <!-- amchart css -->
    <link rel="stylesheet" href="{{ asset('css/amchart.css') }}" type="text/css" media="all" />
    <!-- Start datatable css -->
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <!-- Start datepicker css -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
    <!-- Start select2 css -->
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <!-- others css -->
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-css.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <!-- modernizr css -->
    <script src="{{ asset('js/vendor/modernizr-2.8.3.min.js') }}"></script>

    <!-- style area start -->
    @yield('styles')
    <!-- style area end -->
</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="page-container">

        <!-- sidebar menu area start -->
        @include('layout.sidebar')
        <!-- sidebar menu area end -->

        <!-- main content area start -->
        <div class="main-content">

            <!-- header area start -->
            @include('layout.header')
            <!-- header area end -->

            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Dashboard</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="{{ url('/') }}">Home</a></li>
                                @foreach ($breadcrumbs as $item)
                                    <li><span>{{ $item }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile pull-right">
                            {{-- <img class="avatar user-thumb" src="assets/images/author/avatar.png" alt="avatar"> --}}
                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown">{{ Auth::user()->username }}
                                <i class="fa fa-angle-down"></i>
                            </h4>
                            <div class="dropdown-menu">
                                {{-- <a class="dropdown-item" href="#">Message</a>
                                <a class="dropdown-item" href="#">Settings</a> --}}
                                <a class="dropdown-item" href="{{ url('/logout') }}">Log Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- content area start -->
            @yield('content')
            <!-- content area end -->

        </div>
        <!-- main content area end -->
        <!-- footer area start-->
        <footer>
            <div class="footer-area">
                <p>© Copyright 2022. All right reserved. Template by <a href="https://colorlib.com/wp/">Colorlib</a>.
                </p>
            </div>
        </footer>
        <!-- footer area end-->
    </div>
    <!-- page container area end -->

    <!-- offset area start -->
    @yield('offset-area')
    <!-- offset area end -->

    <!-- jquery latest version -->
    <script src="{{ asset('js/vendor/jquery-2.2.4.min.js') }}"></script>
    <!-- bootstrap 4 js -->
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('js/jquery.slicknav.min.js') }}"></script>

    <!-- Start axios -->
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <!-- Start datatable js -->
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/datatables.rowGroup.min.js') }}"></script>
    <script src="{{ asset('js/datatables.fixedheader.min.js') }}"></script>
    <!-- Start datepicker js -->
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <!-- Start select2 js -->
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <!-- Start Format Rupiah js -->
    <script src="{{ asset('js/rupiahFormat.js') }}"></script>

    <!-- start chart js -->
    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <!-- start highcharts js -->
    <script src="{{ asset('js/highcharts.js') }}"></script>
    <script src="{{ asset('js/exporting.js') }}"></script>
    <script src="{{ asset('js/export-data.js') }}"></script>
    <!-- start amcharts -->
    <script src="{{ asset('js/amcharts.js') }}"></script>
    <script src="{{ asset('js/ammap.js') }}"></script>
    <script src="{{ asset('js/worldLow.js') }}"></script>
    <script src="{{ asset('js/serial.js') }}"></script>
    <script src="{{ asset('js/export.min.js') }}"></script>
    <script src="{{ asset('js/light.js') }}"></script>
    <!-- all line chart activation -->
    <script src="{{ asset('js/line-chart.js') }}"></script>
    <!-- all pie chart -->
    <script src="{{ asset('js/pie-chart.js') }}"></script>
    <!-- all bar chart -->
    <script src="{{ asset('js/bar-chart.js') }}"></script>
    <!-- all map chart -->
    <script src="{{ asset('js/maps.js') }}"></script>
    <!-- others plugins -->
    <script src="{{ asset('js/plugins.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>

    {{-- script --}}
    @yield('scripts')

</body>

</html>
