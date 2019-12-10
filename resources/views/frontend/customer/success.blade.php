@extends('layouts.customer')

@section('header')
<header class="header">
  <h2>Payment</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;"><img src="{{ static_file('customer/images/icon-back-arrow.png') }}" alt=""></a></span>
</header>
@endsection
@section('content')

<div class="content-pages-success">

  <div class="payment-successful"> <a href="javascript:;"><img src="{{ static_file('customer/images/icon-seucss.png') }}" alt=""></a>
    <p>Thank You !</p>
    <p>Your Order has been placed successfully.</p>
    <p>Click on <a href="{{ route('food.customer.my_order') }}">My Orders</a> to check the order status.</p>
  </div>
</div>
@endsection

@section('back-button')
<li><a href="{{ route('food.customer.home') }}" ><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')
@endsection
