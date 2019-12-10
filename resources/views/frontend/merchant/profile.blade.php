@extends('layouts.merchant')

@section('header')
<header class="header">
  <h2>Profile</h2>
  <span class="icon-right"><a href="{{ route('merchant.profile.edit') }}">
      <img src="{{ static_file('merchant/images/icon-edit.png') }}" alt=""></a>
  </span>
</header>
@endsection

@section('content')

<div class="profile">
  <div class="user-info"> <a href="javascript:;"><img src="{{ static_file($prof_image) }}" alt=""></a>
    <h3>{{ $restaurant->name or '--'}}</h3>
    <p>{{ $auth_user->email }}</p>
    <!--<span class="edit-profile"><a href="{{ route('merchant.profile.edit') }}"><img src="{{ static_file('merchant/images/icon-edit-p.png') }}" alt=""></a></span>-->
  </div>
  <form >
    <div class="form-group">
        <label>Fin No</label>
      <span>: {{ $restaurant->fin_no }}</span>
    </div>

    <div class="form-group">
    <label>Open</label>
      <span>: {{ $restaurant->open_at }}</span>
    </div>
    <div class="form-group">
    <label>Closes</label>
      <span>: {{ $restaurant->closes_at }}</span>
    </div>
    <div class="form-group">
    <label>Phone No</label>
      <span>: {{ $restaurant->phone_no }}</span>
    </div>
    <div class="form-group">
    <label>Address</label>
      <span>: {{ $restaurant->address }}</span>
    </div>

  </form>
</div>

@endsection
