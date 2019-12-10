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
    <!-- Custom Theme Style -->
    <link href="{{  static_file('customer/css/style.css') }}" rel="stylesheet">
    <link href="{{  static_file('customer/css/responsive.css') }}" rel="stylesheet">
    <!-- Owl Stylesheets -->

    @yield('styles')

  </head>

  <body>
          @yield('header')

          @include('errors.flash-message')
          @include('errors.error')
          @yield('content')


    <!-- jQuery -->
    <script src="{{  static_file('assets/admin/vendors/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{  static_file('assets/admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>

    @yield('scripts')


  </body>
</html>
