@extends('layouts.customer')

@section('styles')
@endsection

@section('header')
<header class="header">
  <h2>Checkout</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;"><img src="{{ static_file('customer/images/icon-back-arrow.png') }}" alt=""></a></span>
</header>
@endsection
@section('content')

<div class="content-pages">
    <div class="food-forum">

        <form action="{{ route('food.customer.pay') }}" method="post">
            {{ csrf_field() }}
            <input value="{{ $total or 0 }}" required type="hidden" name="total">
            <input value="{{ $wallet or 0 }}" required type="hidden" name="wallet">
            <div class="top-up-row">
              <button type="submit" class="btn top-btn">Pay Now</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')
@endsection
