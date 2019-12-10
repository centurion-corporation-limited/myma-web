@extends('layouts.customer')

@section('header')
<header class="header">
<h2>Cuisine</h2>
<span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;"><img src="{{ static_file('customer/images/icon-back-arrow.png') }}" alt=""></a></span>
</header>
@endsection
@section('content')
<div class="content-pages page-content">
<div class="item-cuisine">
  @foreach($cuisine as $key => $cat)
      <a href="{{ route('food.customer.food_list', ['type' => "$cat->slug"]) }}">
          <li>{{ $cat->name }} <span><img src="@if($cat->img != '') {{ static_file($cat->img) }} @else {{ static_file('customer/images/icon-02.png') }}@endif" alt=""></span></li>
      </a>
  @endforeach
  <!-- <li><a href="{{ route('food.customer.food_list', ['type' => 'italian']) }}">Italian</a> <span><img src="{{ static_file('customer/images/icon-02.png') }}" alt=""></span></li>
  <li><a href="{{ route('food.customer.food_list', ['type' => 'thai']) }}">Thai</a> <span><img src="{{ static_file('customer/images/icon-03.png') }}" alt=""></span></li>
  <li><a href="{{ route('food.customer.food_list', ['type' => 'indian']) }}">Indian</a> <span><img src="{{ static_file('customer/images/icon-04.png') }}" alt=""></span></li>
  <li><a href="{{ route('food.customer.food_list', ['type' => 'french']) }}">French</a> <span><img src="{{ static_file('customer/images/icon-05.jpg') }}" alt=""></span></li>
  <li><a href="{{ route('food.customer.food_list', ['type' => 'japanese']) }}">Japanese</a> <span><img src="{{ static_file('customer/images/icon-06.jpg') }}" alt=""></span></li>
  <li><a href="{{ route('food.customer.food_list', ['type' => 'asian']) }}">Asian </a> <span><img src="{{ static_file('customer/images/icon-07.jpg') }}" alt=""></span></li> -->
</div>
</div>

@endsection
@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')
@endsection
