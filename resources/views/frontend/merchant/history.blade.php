@extends('layouts.merchant')
<style>
.page-content, .sec-menu, .profile, .account-sec, .create-menu{ padding-bottom:0px !important;}
</style>
@section('header')

<header class="header">
  <h2>Order History</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('merchant/images/icon-back-arrow.png') }}" alt=""></a>
  </span>
</header>

@endsection

@section('content')
<div class="page-content">
@if(count($orders))
@foreach($orders as $order)

<div class="list-bg">
  <ul>
    <li>
      <label>Batch Id</label>
      <span>: {{ $order->batch_id }}</span>
    </li>
    <li>
      <label>Order Id</label>
      <span>: #{{ $order->id }}</span></li>
      <li>
      <label>Qty</label>
      <span>: {{ $order->items->count() }}</span></li>
      <li>
      <label>Time</label>
      <span>: {{ $order->created_at->format('M d Y H:i A') }}</span></li>
      <li>
      <label>Drop Address</label>
      <span>: {{ $order->address or '-'}}</span></li>
  </ul>
  <span class="@if($order->status_id == 11 ) de-text @else cn-text @endif">Delivered</span>
</div>

@endforeach
@else
<div class="list-bg">
  <h3>No orders in the history</h3>

</div>

@endif
@endsection
</div>
@section('scripts')

@endsection
