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
                    <li class="{{ Request::path() == 'supplier' ? 'active' : '' }}"><a href="{{ url('/supplier') }}"><i
                                class="fa fa-bank"></i><span>supplier</span></a></li>
                    <li class="{{ Request::path() == 'produk' ? 'active' : '' }}"><a href="{{ url('/produk') }}"><i
                                class="ti-view-list-alt"></i><span>daftar produk</span></a></li>
                    <li class="{{ Request::path() == 'kios' ? 'active' : '' }}"><a href="{{ url('/kios') }}"><i
                                class="ti-user"></i><span>daftar kios</span></a></li>
                    <li class="{{ Request::path() == 'barang-list' ? 'active' : '' }}"><a
                            href="{{ url('/pembelian') }}"><i
                                class="ti-shopping-cart-full"></i><span>pembelian</span></a></li>
                    <li class="{{ Request::path() == 'barang-list' ? 'active' : '' }}"><a
                            href="{{ url('/dashboard') }}"><i class="ti-shopping-cart"></i><span>penjualan</span></a>
                    </li>
                    <li class="{{ Request::path() == 'barang-list' ? 'active' : '' }}"><a
                            href="{{ url('/dashboard') }}"><i class="ti-upload"></i><span>hutang</span></a>
                    </li>
                    <li class="{{ Request::path() == 'barang-list' ? 'active' : '' }}"><a
                            href="{{ url('/dashboard') }}"><i class="ti-download"></i><span>piutang</span></a>
                    </li>
                    {{-- <li class="{{ Request::path() == 'barang-list' ? 'active' : '' }}"><a
                            href="{{ url('/dashboard') }}"><i class="ti-shopping-cart"></i><span>penjualan</span></a>
                    </li> --}}

                    {{-- <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i
                                class="ti-layout-sidebar-left"></i><span>Sidebar
                                Types
                            </span></a>
                        <ul class="collapse">
                            <li><a href="index.html">Left Sidebar</a></li>
                            <li><a href="index3-horizontalmenu.html">Horizontal Sidebar</a></li>
                        </ul>
                    </li> --}}

                </ul>
            </nav>
        </div>
    </div>
</div>
