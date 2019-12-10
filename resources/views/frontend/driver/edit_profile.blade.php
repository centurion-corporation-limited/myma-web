@extends('layouts.driver')

@section('styles')
<style>
.user-info p{padding: 0;}
</style>
@endsection
@section('header')
<header class="header">
  <h2>Edit Profile</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('driver/images/icon-back-arrow.png') }}" alt=""></a></span>
  </header>
@endsection

@section('content')
<div class="profile">
  <div class="user-info"> <a href="#"><img src="{{ static_file('driver/images/img-profile.jpg') }}" alt=""></a>
    <h3>{{ $auth_user->name }}</h3>
    <p>{{ $auth_user->email }}</p>

  </div>
  <form method="post" action="{{ route('driver.profile.edit') }}">
      {{ csrf_field() }}
      <!-- <div class="form-group">
        <input type="text" class="form-control" placeholder="Name" name="name" value="{{ $auth_user->name }}">
      </div> -->

      <!-- <div class="form-group">
        <input type="text" class="form-control" placeholder="Email" name="email" value="{{ $auth_user->email }}">
      </div> -->

    <div class="form-group">
      <input type="text" class="form-control" placeholder="G1234567F" name="fin_no" disabled value="{{ $auth_user->profile->fin_no or '' }}">
    </div>

    <div class="form-group">
        <select class="form-control" name="gender">
            <option value="male" @if($auth_user->profile->gender == 'male') selected @endif>male</option>
            <option value="female" @if($auth_user->profile->gender == 'female') selected @endif>female</option>
        </select>
    </div>
    <div class="form-group">
      <input type="text" class="form-control icon-cal" placeholder="Date of Signup" value="{{ $auth_user->created_at->format('m D Y') }}" disabled>
    </div>
    <div class="form-group">
      <input type="text" class="form-control" placeholder="12345678"  name="phone" value="{{ $auth_user->profile->phone or '' }}">
    </div>
    <div class="form-group">
      <input type="text" class="form-control" placeholder="921 Church St San, Orchard Road Singapore" name="address" value="{{ $auth_user->profile->street_address or '' }}">
    </div>
    <div class="form-group">
      <input type="text" class="form-control" placeholder="Vehicle No." name="vehicle_no" value="{{ $auth_user->profile->vehicle_no or '' }}">
    </div>
    <div class="text-center mg-top" >
 <button type="submit" class="btn btn-default">Update</button>
 </div>
  </form>
</div>
@endsection
