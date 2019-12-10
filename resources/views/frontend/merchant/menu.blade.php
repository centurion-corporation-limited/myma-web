@extends('layouts.merchant')

@section('header')
<header class="header">
  <h2>Menu</h2>
</header>
@endsection

@section('content')
<div class="page-content">
<div class="sec-menu">
    @if(count($menu))
        @foreach($menu as $item)
        <div class="menu-items">
            <div class="menu-left">
                <a href="{{ route('merchant.menu.view', $item->id) }}"><img src="@if($item->image) {{  static_file($item->image) }} @else {{ static_file('merchant/images/img-menu-01.jpg') }} @endif" alt=""></a>
            </div>
            <div class="menu-text">
                <h2><a href="{{ route('merchant.menu.view', $item->id) }}">{{ $item->name }}</a></h2>
                <p>{{ $item->description }}</p>
                <p>S$ {{ $item->price }}</p>
            </div>
        </div>
        @endforeach
    @else
        Click on create menu to add items.
    @endif

</div>
<div class="tabs">
    <ul>
        <li class="active"><a class="active" href="{{ route('merchant.item.add') }}">Add Item</a></li>
        <li><a href="{{ route('merchant.package.add') }}">Add Package</a></li>
    </ul>
</div>

</div>
@endsection
