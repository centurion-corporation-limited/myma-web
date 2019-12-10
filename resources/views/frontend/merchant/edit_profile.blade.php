@extends('layouts.merchant')

@section('header')
<header class="header">
  <h2>Edit Profile</h2>
</header>
@endsection

@section('content')
<div class="page-content">
<div class="profile">
<div class="user-info">
<a href="#"><img src="{{ static_file('merchant/images/img-profile.jpg') }}" alt=""></a>
    <!-- <h3>Jumbo Seafood</h3> -->
    <p>{{ $auth_user->email }}</p>
</div>
<form method="POST" action="{{ route('merchant.profile.edit') }}">
    {{ csrf_field() }}
    <div class="form-group">
      <input type="text" class="form-control" placeholder="Restaurant Name" name="name" value="{{ $restaurant->name }}">
    </div>
    <div class="form-group">
      <input type="text" class="form-control" placeholder="Fin No" name="fin_no" value="{{ $restaurant->fin_no }}">
    </div>
    <div class="form-group">
      <input type="time" class="form-control icon-time" placeholder="Opening Time" name="open_at" value="{{ $restaurant->open_at }}">
    </div>
    <div class="form-group">
      <input type="time" class="form-control icon-time" placeholder="Closing Time" name="closes_at" value="{{ $restaurant->closes_at }}">
    </div>
    <div class="form-group">
      <input type="text" class="form-control" placeholder="Phone No" name="phone_no" value="{{ $restaurant->phone_no }}">
    </div>
    <div class="form-group">
      <input type="text" class="form-control" placeholder="Address" name="address" value="{{ $restaurant->address }}">
    </div>
    <div class="text-center mg-top">
 <button type="submit" class="btn btn-default">Submit</button>
 </div>
  </form>



</div>
</div>
@endsection
