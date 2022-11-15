 <div class="header-area">
     <div class="row align-items-center">
         <!-- nav and search button -->
         <div class="col-md-6 col-sm-8 clearfix">
             <div class="nav-btn pull-left">
                 <span></span>
                 <span></span>
                 <span></span>
             </div>
             <div class="search-box pull-left">
                 <form action="#">
                     <input type="text" name="search" placeholder="Search..." required>
                     <i class="ti-search"></i>
                 </form>
             </div>
         </div>
         <div class="col-md-6 col-sm-4 clearfix">
             <ul class="notification-area pull-right">
                 <li id="">
                     <div class="input-group input-group-solid">
                         <input type="text" class="form-control" disabled
                             aria-label="Text input with dropdown button" value="Tahun">
                         <div class="input-group-append">
                             <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                                 aria-haspopup="true" aria-expanded="false">
                                 {{ session('tahun') }}</button>
                             <div class="dropdown-menu dropdown-menu-right" style="">
                                 @php
                                     $tahun = session()->has('tahun') ? (int) session('tahun') : (int) date('Y');
                                     //  return $tahun;
                                 @endphp
                                 @for ($i = $tahun - 3; $i <= $tahun + 3; $i++)
                                     <a class="dropdown-item"
                                         href="{{ route('set-tahun', ['tahun' => $i]) }}">{{ $i }}</a>
                                 @endfor
                             </div>
                         </div>
                 </li>
                 <li id="full-view"><i class="ti-fullscreen"></i></li>
                 <li id="full-view-exit"><i class="ti-zoom-out"></i></li>
             </ul>
         </div>
     </div>
 </div>
