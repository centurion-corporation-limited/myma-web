@extends('layouts.driver')

@section('header')
<header class="header">
  <h2>Profile</h2>
  <span class="icon-right"><a href="{{ route('driver.profile.edit') }}"><img src="{{ static_file('driver/images/icon-edit.png') }}" alt=""></a></span>
</header>
@endsection

@section('content')
<div class="profile">
  <div class="user-info"> <a href="#"><img src="{{ static_file($prof_image) }}" alt=""></a>
    <h3>{{ $auth_user->name }}</h3>
    <p>{{ $auth_user->email }}</p>

  </div>
  <div class="view-blade">
  <form >
    <div class="form-group">
      <label>FIN NO.</label><span>: {{ $auth_user->profile->fin_no or '-' }}</span>
    </div>

    <div class="form-group">
        <label>Gender</label><span>: {{ $auth_user->profile->gender or 'Male' }}</span>
    </div>
    <div class="form-group">
      <label>Signup  Date</label><span>: {{ $auth_user->created_at->format('m D Y') }}</span>
    </div>
    <div class="form-group">
      <label>Phone No.</label><span>: {{ $auth_user->profile->phone or '-' }}</span>
    </div>
    <div class="form-group">
      <label>Address.</label><span>: {{ $auth_user->profile->street_address or '-' }}</span>
    </div>
    <div class="form-group">
      <label>Vehicle No.</label><span>: {{ $auth_user->profile->vehicle_no or '-'}}</span>
    </div>

  </form>
  </div>
</div>
@endsection
