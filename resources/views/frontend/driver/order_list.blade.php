@extends('layouts.driver')

@section('header')
<header class="header">
  <h2>Order List</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('images/icon-back-arrow.png') }}" alt=""></a>
  </span>
</header>
@endsection

@section('content')
<div class="page-content page-bg">
    @foreach($pickups as $pickup)
<div class="list-bg">
    <a href="{{ route('driver.order.detail', $pickup->id) }}">
  <ul>
    <li>
      <label>Total Items</label>
      <span>: {{ $pickup->total_picked }} / {{ $pickup->total }}</span></li>
    <li>
      <label>Pick Up Address</label>
      <span>: {{ $pickup->pickup->address }}</span></li>
  </ul>
    </a>
</div>
    @endforeach

</div>
@endsection
