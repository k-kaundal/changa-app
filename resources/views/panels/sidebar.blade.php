
 <div class="nav nav-pills nav-sidebar" id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true" role="menu">
    <div class="brand-logo">
     <a href="{{ route('dashboard') }}">
      <img src="{{asset('images/logo-icon.png')}}" class="logo-icon" alt="logo icon">
      <h5 class="logo-text">Changa App Admin</h5>
    </a>
  </div>
  <ul class="sidebar-menu do-nicescrol ">
     <li>
       <a href="{{ route('dashboard') }}" class=" nav-link {{ (Request::segment(1) == "dashboard") ? "active":''}}">
         <i class="zmdi zmdi-view-dashboard"></i> <span>Dashboard</span>
       </a>
     </li>

     <li>
       <a href="{{route('users')}}" class=" nav-link {{ (Request::segment(1) == "users") ? "active":''}}">
         <i class="zmdi zmdi-account-circle"></i> <span>Customers</span>
       </a>
     </li>

   </ul>
  
  </div>
  <!--End sidebar-wrapper-->



