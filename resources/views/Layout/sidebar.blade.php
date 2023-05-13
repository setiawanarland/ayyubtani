<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="index.html"><img src="asset images/icon/logo.png" alt="ayyub tani logo"></a>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">

                    <li class="{{ Request::path() == 'dashboard' ? 'active' : '' }}"><a
                            href="{{ route('dashboard') }}"><i class="ti-dashboard"></i><span>dashboard</span></a></li>
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-database"></i><span>data
                            </span></a>
                        <ul class="collapse">
                            <li class="{{ Request::path() == 'supplier' ? 'active' : '' }}"><a
                                    href="{{ url('/supplier') }}"><i class="fa fa-bank"></i><span>supplier</span></a>
                            </li>
                            <li class="{{ Request::path() == 'produk' ? 'active' : '' }}"><a
                                    href="{{ url('/produk') }}"><i class="ti-view-list-alt"></i><span>daftar
                                        produk</span></a></li>
                            <li class="{{ Request::path() == 'kios' ? 'active' : '' }}"><a href="{{ url('/kios') }}"><i
                                        class="ti-user"></i><span>daftar kios</span></a></li>
                        </ul>
                    </li>

                    <li class="{{ Request::path() == 'pembelian' ? 'active' : '' }}"><a
                            href="{{ url('/pembelian') }}"><i
                                class="ti-shopping-cart-full"></i><span>pembelian</span></a></li>
                    <li class="{{ Request::path() == 'pembelian/daftar' ? 'active' : '' }}"><a
                            href="{{ url('/pembelian/daftar') }}"><i class="ti-layout-list-thumb-alt"></i><span>daftar
                                pembelian</span></a></li>
                    <li class="{{ Request::path() == 'penjualan' ? 'active' : '' }}"><a
                            href="{{ url('/penjualan') }}"><i class="ti-shopping-cart"></i><span>penjualan</span></a>
                    </li>
                    <li class="{{ Request::path() == 'penjualan/daftar' ? 'active' : '' }}"><a
                            href="{{ url('/penjualan/daftar') }}"><i class="ti-layout-list-thumb"></i><span>daftar
                                penjualan</span></a></li>

                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i
                                class="fa fa-money"></i><span>hutang/piutang
                            </span></a>
                        <ul class="collapse">

                            <li class="{{ Request::path() == 'hutang' ? 'active' : '' }}"><a
                                    href="{{ url('/hutang') }}"><i class="ti-upload"></i><span>hutang</span></a>
                            </li>
                            <li class="{{ Request::path() == 'barang-list' ? 'active' : '' }}"><a
                                    href="{{ url('/piutang') }}"><i class="ti-download"></i><span>piutang</span></a>
                            </li>
                            {{-- <li class="{{ Request::path() == 'barang-list' ? 'active' : '' }}"><a
                                    href="{{ url('/dashboard') }}"><i class="ti-shopping-cart"></i><span>penjualan</span></a>
                            </li> --}}
                        </ul>
                    </li>


                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-file"></i><span>laporan
                            </span></a>
                        <ul class="collapse">
                            {{-- <li class="{{ Request::path() == 'laporan/stok' ? 'active' : '' }}"><a
                                    href="{{ url('/laporan/stok') }}"><i
                                        class="fa fa-file-text"></i><span>stok</span></a>
                            </li> --}}
                            <li class="{{ Request::path() == 'laporan/produk-jual' ? 'active' : '' }}"><a
                                    href="{{ url('/laporan/produk-jual') }}"><i
                                        class="fa fa-credit-card"></i><span>produk
                                        Jual</span></a>
                            </li>
                            <li class="{{ Request::path() == 'laporan/produk-beli' ? 'active' : '' }}"><a
                                    href="{{ url('/laporan/produk-beli') }}"><i
                                        class="fa fa-credit-card-alt"></i><span>produk Beli</span></a>
                            </li>
                            <li class="{{ Request::path() == 'laporan/penjualan' ? 'active' : '' }}"><a
                                    href="{{ url('/laporan/penjualan') }}"><i
                                        class="ti-layout-cta-btn-right"></i><span>penjualan</span></a>
                            </li>
                            <li class="{{ Request::path() == 'laporan/pembelian' ? 'active' : '' }}"><a
                                    href="{{ url('/laporan/pembelian') }}"><i
                                        class="ti-layout-cta-btn-left"></i><span>pembelian</span></a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-cog"></i><span>Settings
                            </span></a>
                        <ul class="collapse">
                            <li class="{{ Request::path() == 'tambah-stok' ? 'active' : '' }}"><a
                                    href="{{ url('/setting/tambah-stok') }}"><i
                                        class="fa fa-plus-square"></i><span>tambah stok</span></a>
                            </li>
                            <li class="{{ Request::path() == 'kurang-stok' ? 'active' : '' }}"><a
                                    href="{{ url('/setting/kurang-stok') }}"><i
                                        class="fa fa-minus-square"></i><span>kurang stok</span></a>
                            </li>
                            <li class="{{ Request::path() == 'pajak' ? 'active' : '' }}"><a
                                    href="{{ url('/pajak') }}"><i class="fa fa-credit-card"></i><span>pajak</span></a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </nav>
        </div>
    </div>
</div>
