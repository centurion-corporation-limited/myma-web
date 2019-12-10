@extends('layouts.merchant')

@section('header')
<style>
.list-bg a, .order-details a{ background:inherit !important; padding:0;}
</style>
<header class="header">
  <h2>Package</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('merchant/images/icon-back-arrow.png') }}" alt=""></a>
  </span>
</header>
@endsection
@section('content')
<div class="page-content">
@foreach($orders as $order)
<div class="list-bg">
<a href="{{ route('merchant.package.subscription', ['id' =>$order->id, 'item_id' => $id]) }}">
  <ul>
    <li>
      <label><strong>Name</strong></label>
      <span>: {{ $order->user->name }}</span></li>
  </ul>
  </a>
</div>
@endforeach
</div>
@endsection
