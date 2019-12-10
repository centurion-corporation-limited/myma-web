@extends('layouts.merchant')

@section('header')
<header class="header">
  <h2>Account Management</h2>
</header>
@endsection
@section('content')
<div class="account-sec page-bg">
@if(count($orders))
  @foreach($orders as $order)
  <div class="account-info clearfix">
    <div class="pull-left">
      <h2>Batch Id : <a href="{{ route('merchant.account.detail', $order->batch_id) }}"> #{{ $order->batch_id }}</a></h2>
    </div>
    <div class="pull-right">
      <h1>Total Price : S${{ $order->sub_total }}</h1>
    </div>
  </div>
  @endforeach
@else
    <h4>No account history yet.</h4>
@endif
</div>

@endsection
