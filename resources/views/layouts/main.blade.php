<!DOCTYPE html>
<html lang="{{ str_replace('_','_',app()->getLocale())}}">
<head>
  <meta charset="utf-8"/>
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>{{ config('app.name','Changa App')}}</title>

  <!-- loader-->

  <link href="{{asset('css/pace.min.css')}}" rel="stylesheet"/>
  <script src="{{asset('js/pace.min.js')}}"></script>
  <!--favicon-->
  <link rel="icon" href="{{asset('images/favicon.ico')}}" type="image/x-icon">
  <!-- Vector CSS -->
  <link href="{{asset('plugins/vectormap/jquery-jvectormap-2.0.2.css')}}" rel="stylesheet"/>
  <!-- simplebar CSS-->
  <link href="{{asset('plugins/simplebar/css/simplebar.css')}}" rel="stylesheet"/>
  <!-- Bootstrap core CSS-->
  <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet"/>
  <!-- animate CSS-->
  <link href="{{asset('css/animate.css')}}" rel="stylesheet" type="text/css"/>
  <!-- Icons CSS-->
  <link href="{{asset('css/icons.css')}}" rel="stylesheet" type="text/css"/>
  <!-- Sidebar CSS-->
  <link href="{{asset('css/sidebar-menu.css')}}" rel="stylesheet"/>
  <!-- Custom Style-->
  <link href="{{asset('css/app-style.css')}}" rel="stylesheet"/>
  {{-- @extends('panels/styles') --}}
</head>

<body class="bg-theme bg-theme1">

     @yield('content')

    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/popper.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    
   <!-- simplebar js -->
    <script src="{{asset('plugins/simplebar/js/simplebar.js')}}"></script>
    <!-- sidebar-menu js -->
    <script src="{{asset('js/sidebar-menu.js')}}"></script>
    <!-- loader scripts -->
    <script src="{{asset('js/jquery.loading-indicator.js')}}"></script>
    <!-- Custom scripts -->
    <script src="{{asset('js/app-script.js')}}"></script>
    <!-- Chart js -->
    
    <script src="{{asset('plugins/Chart.js/Chart.min.js')}}"></script>
   
    <!-- Index js -->
    <script src="{{asset('js/index.js')}}"></script>

</body>

</html>



