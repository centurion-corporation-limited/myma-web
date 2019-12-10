@extends('layouts.admin')

@section('content')
<style>
.login-logo{ padding-bottom:20px;}
.form-group{ padding:0;}
</style>
<div class="login_wrapper">
  <div class="animate form login_form">
    <section class="login_content">
      <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
          {{ csrf_field() }}
        <!--<h1>Login</h1>-->
        <div class="login-logo">
        <img src="{{ static_file('images/img-logo-b.png') }}" alt="">
        </div>
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
          <input type="email" class="form-control" name="email" placeholder="Username" value="{{ old('email') }}" required="" />
          @if ($errors->has('email'))
              <span class="help-block">
                  <strong>{{ $errors->first('email') }}</strong>
              </span>
          @endif
        </div>
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
          <input type="password" name="password" class="form-control" placeholder="Password" required="" />
          @if ($errors->has('password'))
              <span class="help-block">
                  <strong>{{ $errors->first('password') }}</strong>
              </span>
          @endif
        </div>
        <div class="pull-left">
          <!-- <a class="btn btn-default submit" href="index.html">Log in</a> -->
          <button type="submit" class="btn btn-success submit">
              Log In
          </button>
          </div>
           <div class="pull-right">
          <a class="reset_pass" href="{{ route('password.request') }}">Forgot password?</a>
        </div>

      </form>
    </section>
  </div>

  <!-- <div id="register" class="animate form registration_form">
    <section class="login_content">
      <form>
        <h1>Create Account</h1>
        <div>
          <input type="text" class="form-control" placeholder="Username" required="" />
        </div>
        <div>
          <input type="email" class="form-control" placeholder="Email" required="" />
        </div>
        <div>
          <input type="password" class="form-control" placeholder="Password" required="" />
        </div>
        <div>
          <a class="btn btn-default submit" href="index.html">Submit</a>
        </div>

        <div class="clearfix"></div>

        <div class="separator">
          <p class="change_link">Already a member ?
            <a href="#signin" class="to_register"> Log in </a>
          </p>

          <div class="clearfix"></div>
          <br />

          <div>
            <h1><i class="fa fa-paw"></i> Gentelella Alela!</h1>
            <p>©2016 All Rights Reserved. Gentelella Alela! is a Bootstrap 3 template. Privacy and Terms</p>
          </div>
        </div>
      </form>
    </section>
  </div> -->
</div>
 <!-- <div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

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
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->
@endsection
