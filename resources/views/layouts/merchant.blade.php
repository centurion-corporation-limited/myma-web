<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="{{  static_file('manifest.json') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Bootstrap -->
    <link href="{{  static_file('css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{  static_file('css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- bootstrap-daterangepicker -->
    <link href="{{  static_file('assets/admin/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{  static_file('merchant/css/style.css') }}" rel="stylesheet">
    <link href="{{  static_file('merchant/css/responsive.css') }}" rel="stylesheet">
    <!-- Owl Stylesheets -->
    <link rel="stylesheet" href="{{ static_file('css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ static_file('css/owl.theme.default.min.css') }}">

    @yield('styles')

  </head>

  @if(Auth::guest())
  <body class="login-bg">
      @yield('content')
  @else
  <body class="">

      @yield('header')

          @include('errors.flash-message')
          @include('errors.error')
          @yield('content')

          <!-- End-Recommended -->
          <footer class="footer">
            <ul>
              <li><a class="active" href="{{ route('merchant.home') }}"><span><img src="{{ static_file('merchant/images/icon-dashboard.png') }}" alt=""></span>Dashboard</a></li>
              <li><a href="{{ route('merchant.profile.view') }}"><span><img src="{{ static_file('merchant/images/icon-profile-1.png') }}" alt=""></span>Profile</a></li>
              <li><a href="{{ route('merchant.menu.list') }}"><span><img src="{{ static_file('merchant/images/icon-management-1.png') }}" alt=""></span>Management</a></li>
              <li><a href="{{ route('merchant.account.list') }}"><span><img src="{{ static_file('merchant/images/icon-account-1.png') }}" alt=""></span>Account</a></li>
              <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                       document.getElementById('logout-form').submit();">
                       <span><img src="{{ static_file('merchant/images/icon-logout-1.png') }}" alt=""></span>Logout</a>
              </li>
            </ul>
          </footer>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              {{ csrf_field() }}
          </form>

          <!-- End-footer -->

   @endif
    <!-- jQuery -->
    <script src="{{  static_file('assets/admin/vendors/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{  static_file('assets/admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- bootstra{{  static_file('assets/admin/vendors/Flot/jquery.flot.time.js') }}picker -->
    <script src="{{  static_file('assets/admin/vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{  static_file('js/owl.carousel.js') }}"></script>
    @yield('scripts')

  </body>
</html>
