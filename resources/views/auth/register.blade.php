@extends('layouts.frontend')

@section('content')
<!-- end-header  -->
<div class="login-wrapper">
<div class="container">
    <div class="sap"> <img src="{{ static_file('images/icon-sap.png') }}" alt="#"></div>
    <h2>Sign Up</h2>
    <div class="login-box">
    <div class="login-innar">
    <div class="login-left signup-left">
      @include('errors.flash-message')
    <h2>Sign Up</h2>
    <form class="form-horizontal" role="form" method="POST" action="{{ route('frontend.register', 'influencer') }}">
      {{ csrf_field() }}
  <div class="form-group{{ $errors->has('instagram_name') ? ' has-error' : '' }}">
    <div class="col-sm-11">
      <input id="name" type="text" class="form-control" name="instagram_name" value="{{ old('instagram_name') }}" required autofocus placeholder="Instagram Username">
      <span>(please exclude '@' handle)</span>
      @if ($errors->has('instagram_name'))
          <span class="help-block">
              <strong>{{ $errors->first('instagram_name') }}</strong>
          </span>
      @endif
    </div>
  </div>
    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
    <div class="col-sm-11">
      <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="email" required placeholder="Password">
      <span>(please use the official collaboration email stated on your Instagram profile)</span>
      @if ($errors->has('email'))
          <span class="help-block">
              <strong>{{ $errors->first('email') }}</strong>
          </span>
      @endif
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-11">
      <input id="password" type="password" class="form-control" name="password" required placeholder="Password">
      @if ($errors->has('password'))
          <span class="help-block">
              <strong>{{ $errors->first('password') }}</strong>
          </span>
      @endif
    </div>
  </div>
   <div class="form-group">
    <div class="col-sm-11">
      <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required  placeholder="Password Confirmation">
      @if ($errors->has('password'))
          <span class="help-block">
              <strong>{{ $errors->first('password') }}</strong>
          </span>
      @endif
    </div>
  </div>

    <div class="form-group">
    <div class="col-sm-11">
      <button type="submit" class="btn btn-login">Sign up</button>
    </div>
  </div>

</form>
    </div>
    <div class="login-right signup-right">

    <h2>Already have an account?</h2>
      <div class="form-group text-center ">
   <a class="text-log" href="{{ route('login') }}">Login here</a>
   </div>
  <h2>Are you signing up as <strong style="line-height: 25px">Brand Manager?</strong></h2>
    <div class="form-group text-center ">
 <a class="text-log" href="{{ route('frontend.register.manager') }}">Sign Up Here</a>
 </div>
    </div>

    </div>
</div>
</div>
</div>

<!-- <div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->
@endsection
