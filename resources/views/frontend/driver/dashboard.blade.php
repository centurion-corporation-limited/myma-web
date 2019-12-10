@extends('layouts.driver')

@section('styles')
<style>
.list-bg span a{
    color: #777777;
    padding: 0;
}
</style>
@endsection

@section('header')
<header class="header">
  <h2>Dashboard</h2>
  <span class="icon-right">
      <a href="{{ route('driver.trip.notification') }}">
          <img src="{{ static_file('merchant/images/icon-invoice.png') }}" alt="">
      </a>
  </span>
</header>
@endsection

@section('content')
<div class="page-content page-bg">
    @if(count($data))
        @foreach($data as $batch_id => $item)
        <div class="list-bg">
          <ul>
            <li>
              <label><strong>Batch Id</strong></label>
              <span>: <a href="{{ route('driver.order.list', $trip->id) }}">{{ $batch_id }}</a></span>
            </li>
            <li>
              <label>Total Pickups</label>
              <span>: {{ $item['pickups'] }}</span>
            </li>
            <li>
              <label>Delivery Address</label>
              <span>: {{ $item['address'] }}</span></li>
          </ul>
        </div>
        @endforeach
    @else
    <h4>No orders to pick</h4>
    @endif

</div>
@endsection
