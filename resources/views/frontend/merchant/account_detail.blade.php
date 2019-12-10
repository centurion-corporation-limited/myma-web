@extends('layouts.merchant')

@section('header')
<header class="header">
  <h2>Details</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('merchant/images/icon-back-arrow.png') }}" alt=""></a>
  </span>
  <span class="icon-right"><a href="{{ route('merchant.invoice.detail', $batch_id) }}"><img src="{{ static_file('merchant/images/icon-invoice.png') }}" alt=""></a></span>
  </header>
@endsection
@section('content')
<div class="account-sec page-bg">
    @foreach($items as $item)
        <div class="list-bg">
          <ul>
            <li>
              <label>Item Name</label>
              <span>: {{ $item->name }}</span></li>
            <li>
              <label>Qty</label>
              <span>: {{ $item->quantity }}</span></li>
            <li>
              <label>Total Price</label>
              <span>: S${{ $item->quantity*$item->price}}</span></li>
          </ul>
        </div>
    @endforeach

</div>
@endsection
