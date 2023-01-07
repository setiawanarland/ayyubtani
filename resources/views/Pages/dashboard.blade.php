@extends('layout.template')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- seo fact area start -->
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-md-3 mt-5 mb-3">
                        <div class="card">
                            <div class="seo-fact sbg1">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="ti-view-list-alt"></i> Produk</div>
                                    <h2>{{ $dataProduk }}</h2>
                                </div>
                                <canvas id="seolinechart" height="50"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-md-5 mb-3">
                        <div class="card">
                            <div class="seo-fact sbg2">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="ti-user"></i> Kios</div>
                                    <h2>{{ $dataKios }}</h2>
                                </div>
                                <canvas id="seolinechart" height="50"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-md-5 mb-3">
                        <div class="card">
                            <div class="seo-fact sbg3">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="ti-shopping-cart-full"></i> Pembelian</div>
                                    <h2>{{ $dataPembelian }}</h2>
                                </div>
                                <canvas id="seolinechart" height="50"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-md-5 mb-3">
                        <div class="card">
                            <div class="seo-fact sbg4">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="ti-shopping-cart"></i> Penjualan</div>
                                    <h2>{{ $dataPenjualan }}</h2>
                                </div>
                                <canvas id="seolinechart" height="50"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- seo fact area end -->

            <!-- sales area start -->
            {{-- <div class="col-ml-8 col-lg-8 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Sales</h4>
                        <div id="salesanalytic"></div>
                    </div>
                </div>
            </div>
            <!-- sales area end -->

            <!-- Advertising area start -->
            <div class="col-lg-4 mt-5">
                <div class="card h-full">
                    <div class="card-body">
                        <h4 class="header-title">Advertising & Marketing</h4>
                        <canvas id="seolinechart8" height="233"></canvas>
                    </div>
                </div>
            </div>
            <!-- Advertising area end -->

            <!-- map area start -->
            <div class="col-xl-8 col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Marketing Area</h4>
                        <div id="seomap"></div>
                    </div>
                </div>
            </div>
            <!-- map area end -->
            <!-- timeline area start -->
            <div class="col-xl-4 col-ml-4 col-lg-4 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Timeline</h4>
                        <div class="timeline-area">
                            <div class="timeline-task">
                                <div class="icon bg1">
                                    <i class="fa fa-envelope"></i>
                                </div>
                                <div class="tm-title">
                                    <h4>Rashed sent you an email</h4>
                                    <span class="time"><i class="ti-time"></i>09:35</span>
                                </div>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio
                                    itaque at.
                                </p>
                            </div>
                            <div class="timeline-task">
                                <div class="icon bg2">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </div>
                                <div class="tm-title">
                                    <h4>Rashed sent you an email</h4>
                                    <span class="time"><i class="ti-time"></i>09:35</span>
                                </div>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio
                                    itaque at.
                                </p>
                            </div>
                            <div class="timeline-task">
                                <div class="icon bg2">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </div>
                                <div class="tm-title">
                                    <h4>Rashed sent you an email</h4>
                                    <span class="time"><i class="ti-time"></i>09:35</span>
                                </div>
                            </div>
                            <div class="timeline-task">
                                <div class="icon bg3">
                                    <i class="fa fa-bomb"></i>
                                </div>
                                <div class="tm-title">
                                    <h4>Rashed sent you an email</h4>
                                    <span class="time"><i class="ti-time"></i>09:35</span>
                                </div>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio
                                    itaque at.
                                </p>
                            </div>
                            <div class="timeline-task">
                                <div class="icon bg3">
                                    <i class="ti-signal"></i>
                                </div>
                                <div class="tm-title">
                                    <h4>Rashed sent you an email</h4>
                                    <span class="time"><i class="ti-time"></i>09:35</span>
                                </div>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio
                                    itaque at.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            <!-- timeline area end -->

        </div>
    </div>
@endsection
