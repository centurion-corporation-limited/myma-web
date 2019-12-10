@extends('layouts.driver')

@section('styles')
<style>
.list-bg a{
    color: #777777;
}
.list-bg a.btn{
    color: #ffffff;
    background-color:#b90e3b !important;
    margin-left: 50%;
    transform: translateX(-50%);
}
</style>

@endsection
@section('header')
<header class="header">
  <h2>Order Details</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('driver/images/icon-back-arrow.png') }}" alt=""></a>
  </span>
</header>
@endsection

@section('content')
<div class="page-content page-bg">
    @foreach($data as $key => $item)
    <div class="list-bg row">
      <ul class="col-xs-10">
        <li>
              <label>Item Name</label>
              <span>: {{ $item['name'] }}</span>
        </li>
        <li>
              <label>Qty</label>
              <span>: {{ $item['qty'] }}</span>
        </li>
        <li>
              <label>Status</label>
              <span>: {{ $item['status'] }}</span>
        </li>
      </ul>
    </div>
    @endforeach

</div>

@endsection

@section('scripts')

@endsection
