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
    {{--<link href="{{  static_file('assets/admin/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">--}}

    <!-- Custom Theme Style -->
    <link href="{{  static_file('customer/css/style.css') }}" rel="stylesheet">
    <link href="{{  static_file('customer/css/responsive.css') }}" rel="stylesheet">
    <!-- Owl Stylesheets -->

    @yield('styles')

  </head>

  <body>
      {{-- @yield('header') --}}

          @include('errors.flash-message')
          @include('errors.error')
          @yield('content')

          <!-- End-Recommended -->
          <div class="footer">
            <ul>
              @yield('back-button')
              <li><a href="{{ route('food.customer.home') }}"><span><img src="{{ static_file('customer/images/icon-home.png') }}" alt=""></span>Home</a></li>
              <li><a href="{{ route('food.customer.my_order') }}"><span><img src="{{ static_file('customer/images/icon-order-1.png') }}" alt=""></span>My Orders</a></li>
              <li><a href="{{ route('food.customer.package') }}"><span><img src="{{ static_file('customer/images/icon-sub-1.png') }}" alt=""></span>Subscription</a></li>
              <li><a href="{{ route('food.customer.cart') }}"><span><img src="{{ static_file('customer/images/icon-cart-1.png') }}" alt=""></span>Cart <span class="badge cart_count">{{ $cart_count }}</span></a></li>
            </ul>
          </div>

          <!-- End-footer -->

    <!-- jQuery -->
    <script src="{{  static_file('assets/admin/vendors/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{  static_file('assets/admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- bootstra{{  static_file('assets/admin/vendors/Flot/jquery.flot.time.js') }}picker -->
    {{--<script src="{{  static_file('assets/admin/vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>--}}

    @yield('scripts')


  </body>
</html>
