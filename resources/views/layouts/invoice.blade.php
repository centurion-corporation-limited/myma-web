<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invoice</title>

  <!-- Bootstrap -->
  <link href="{{  static_file('css/bootstrap.min.css') }}" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="{{  static_file('css/font-awesome.min.css') }}" rel="stylesheet">
  <!-- Custom Theme Style -->
  <link href="{{  static_file('customer/css/style.css') }}" rel="stylesheet">
  <link href="{{  static_file('customer/css/responsive.css') }}" rel="stylesheet">

  @yield('styles')

</head>
    <body onload="window.print();">
        <!-- Content Wrapper. Contains page content -->
        <div class="wrapper" style="margin: 15px;  ">
            @yield('content')
        </div>


        <!-- jQuery -->
        <script src="{{  static_file('assets/admin/vendors/jquery/dist/jquery.min.js') }}"></script>
        <!-- Bootstrap -->
        <script src="{{  static_file('assets/admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        @yield('scripts')
    </body>
</html>
