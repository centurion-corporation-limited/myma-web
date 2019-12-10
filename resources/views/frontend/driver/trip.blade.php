@extends('layouts.driver')

@section('header')
<header class="header">
  <h2>Trip Detail</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('driver/images/icon-back-arrow.png') }}" alt=""></a></span>
</header>
@endsection

@section('content')
<div class="page-content page-bg">
@foreach($data as $batch_id => $batch)
<div class="list-bg">
  <ul>
    <li>
      <label>Batch Id</label>
      <span>: {{ $batch_id }}</span></li>
    <li>
      <label>Total Items</label>
      <span>: {{ $batch['total_items'] }}</span></li>
    <li>
      <label>Pick up Address</label>
      <span>: {{ $batch['address'] }} </span></li>
  </ul>
</div>
@endforeach
</div>
@endsection
