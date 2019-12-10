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
    <link href="{{  static_file('driver/css/style.css') }}" rel="stylesheet">
    <link href="{{  static_file('driver/css/responsive.css') }}" rel="stylesheet">
    <!-- Owl Stylesheets -->
    <link rel="stylesheet" href="{{ static_file('css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ static_file('css/owl.theme.default.min.css') }}">

    @yield('styles')

  </head>

  <body>
      @yield('header')

          @include('errors.flash-message')
          @include('errors.error')
          @yield('content')

          <!-- End-Recommended -->
          <footer class="footer">
            <ul>
                <li><a class="active" href="{{ route('driver.home') }}"><span><img src="{{ static_file('driver/images/icon-dashboard.png') }}" alt=""></span>Dashboard</a></li>
                <li><a href="{{ route('driver.profile.view') }}"><span><img src="{{ static_file('driver/images/icon-profile-1.png') }}" alt=""></span>Profile</a></li>
                <li><a href="{{ route('driver.earning.list') }}"><span><img src="{{ static_file('driver/images/icon-earning-1.png') }}" alt=""></span>Runsheet</a></li>
                <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
                         <span><img src="{{ static_file('driver/images/icon-logout-1.png') }}" alt=""></span>Logout</a>
                </li>
            </ul>
          </footer>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              {{ csrf_field() }}
          </form>
          <!-- End-footer -->

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
