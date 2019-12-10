@extends('layouts.driver')

@section('styles')
<style>
.list-bg a{
    color: #777777;
}
</style>
@endsection

@section('header')
<header class="header">
  <h2>Run Sheet</h2>
</header>
@endsection

@section('content')
<div class="page-content page-bg">
<div class="date-pic">
    <div class="form-group">
      <input type="text" class="form-control-md icon-cal" placeholder="22/03/2018">
      <input type="text" class="form-control-md icon-cal" placeholder="28/03/2018">
    </div>

</div>
@if(count($trips))
    @foreach($trips as $trip)
    <div class="list-bg">
      <ul>
        <li>
          <label>Trip Id</label>
          <span>: # <a href="{{ route('driver.earning.detail', $trip->id) }}">{{ $trip->id }}</a></span>
        </li>
        <li>
          <label>Earning</label>
          <span>: S${{ $trip->price }}</span>
        </li>
        <li>
          <label>Date</label>
          <span>: {{ $trip->trip_date }}</span>
        </li>
        <li>
          <label>Payment Status </label>
          <span>: Pending</span>
        </li>
      </ul>
    </div>
    @endforeach
@else
    <h4>Have not done any trip yet</h4>
@endif
</div>
@endsection
