@extends('layouts.merchant')

@section('content')
<div class="logo"> <a href="#"><img src="{{ static_file('merchant/images/img-logo.png') }}" alt=""></a> </div>
<div class="login-form">

    <form class="form-login" role="form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
      <input type="text" class="form-control icon-user" placeholder="Username" name="email" required>
      @if ($errors->has('email'))
          <span class="help-block">
              <strong>{{ $errors->first('email') }}</strong>
          </span>
      @endif
    </div>
    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
      <input type="password" class="form-control icon-pass" placeholder="Password" name="password" required>
      @if ($errors->has('password'))
          <span class="help-block">
              <strong>{{ $errors->first('password') }}</strong>
          </span>
      @endif
    </div>
    <div class="form-group">
      <div class=" pull-left login-custom-check-form ">
        <input id="R1" name="remember_me" type="checkbox">
        <label for="R1">Remember Me!</label>
      </div>
    </div>
    <div class="form-group">
      <div class="text-center mg-top">
        <button type="submit" class="btn btn-login">Login to my account > </button>
      </div>
    </div>
  </form>
  <div class="cant-access"> <a href="#">Cannot access your account?</a> </div>
</div>

@endsection
