@extends('layouts.customer')

@section('styles')
<style>
.list-bg a, .order-details a{
    padding: 0 !important;
}
.de-text{
  color: #34a853 !important;
}
</style>
@endsection
@section('header')
<header class="header">
  <h2>Subscription</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;"><img src="{{ static_file('images/icon-back-arrow.png ') }}'" alt=""></a></span>
</header>
<!-- End-Header -->
@endsection
@section('content')
@if($orders->count())
<div class="page-content page-bg">
  @foreach($orders as $order)
  <div class="list-bg">
      <ul>
          <li>
              <label>Name :</label>
              <span><a href="{{ route('food.customer.subscription', $order->id) }}"> {{ $order->item->name }}</a></span>
          </li>
          <li>
              <label>Start date :</label>
              <span> {{ $order->start_date }} </span>
          </li>
          <li>
              <label>End date :</label>
              <span> {{ $order->end_date }}</span>
          </li>

     </ul>
     <span class="de-text">{{ $order->status or 'Completed'}}</span>
  </div>
  @endforeach
</div>
 @else
 <div class="list-bg">
     <ul>
         <li>
    <h4>Please go to <a style="padding:0;" href="{{ route('food.customer.cuisine') }}">Cuisine Page</a> and checkout with a package to subscribe. </h4>
</li>
</ul>
</div>
 @endif

@endsection

@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')
@endsection
