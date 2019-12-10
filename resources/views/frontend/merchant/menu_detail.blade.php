@extends('layouts.merchant')

@section('header')
<header class="header">
  <h2>Food Item</h2>
  <span class="back-btn">
      <a href="javascript:;" onclick="window.history.go(-1); return false;"><img src="{{ static_file('merchant/images/icon-back-arrow.png') }}" alt=""></a>
  </span>
  <span class="icon-right">
      @if($item->type == 'single')
      <a href="{{ route('merchant.item.edit', $item->id) }}"><img src="{{ static_file('merchant/images/icon-edit.png') }}" alt=""></a>
      @else
      <a href="{{ route('merchant.package.edit', $item->id) }}"><img src="{{ static_file('merchant/images/icon-edit.png') }}" alt=""></a>
      @endif
  </span>
</header>
@endsection

@section('content')

<div class="sec-menu">
<div class="menu-details">
    <a href="javascript:;">
        @if($item->image)
            <img src="{{ static_file($item->image) }}" alt="">
        @else
            <img src="{{ static_file('merchant/images/img-menu-01.jpg') }}" alt="">
        @endif
    </a>
<h2>{{ $item->name }}</h2>
<p>{{ $item->description }}</p>
<p>Price: S$ {{ $item->price }}</p>
</div>
</div>

@endsection
