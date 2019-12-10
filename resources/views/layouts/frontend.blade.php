<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Myma') }}</title>

    <!-- Styles -->
    <link href="{{  static_file('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{  static_file('css/frontend/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{  static_file('css/frontend/responsive.css') }}" rel="stylesheet" type="text/css">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
    @yield('styles')
</head>

<body class="bg-scr">

        @yield('content')

<!-- Scripts -->
<!-- <script src="{{  static_file('js/app.js') }}"></script> -->
<script src="{{  static_file('js/jquery.min.js') }}"></script>
<script src="{{  static_file('js/bootstrap.min.js') }}"></script>
</body>
</html>
