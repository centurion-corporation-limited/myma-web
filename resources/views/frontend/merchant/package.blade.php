@extends('layouts.merchant')

@section('header')
<header class="header">
  <h2>Package</h2>
  <span class="back-btn">
    <a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('merchant/images/icon-back-arrow.png') }}" alt="">
    </a>
  </span>
</header>
@endsection
@section('content')
<div class="page-content">
@foreach($orders as $order)
<div class="list-bg">
  <ul>
    <li>
      <label>Package</label>
      <span>: <a href="{{ route('merchant.package.subscribers', $order->item_id) }}">{{ $order->item->name }}</a></span></li>
  </ul>
</div>
@endforeach
</div>
@endsection
