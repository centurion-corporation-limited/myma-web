@extends('layouts.frontend')

@section('content')
<div class="login-wrapper">
<div class="container">
    <div class="sap"> <img src="{{ static_file('images/icon-sap.png') }}" alt="#"></div>
    <!-- <h2>Login</h2> -->
    <!-- <p> Use your Instagram username</p> -->
    <div class="login-box">
    <div class="login-innar">

      <div class="{{ $errors->has('email') ? ' has-error' : '' }}">
      @if ($errors->has('email'))
          <span class="help-block">
              <strong>{{ $errors->first('email') }}</strong>
          </span>
      @endif
    </div>

    </div>
</div>
</div>
</div>
@endsection
