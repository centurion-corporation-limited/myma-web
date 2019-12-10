@extends('layouts.invoice_listing')

@section('styles')
<style>
.order-popup{ position:relative;}
.order-popup h2{ text-align:center; font-size:18px; color:#767676;}
.order-popup li a{ text-decoration:none; color:#fff;}
.order-popup li {
	width: 48%;
	margin-left: 1.4%;
	color: #fff;
	font-size: 14px;
	margin-top: 25px;
	line-height: 1.2;
}
.qrcode-text-btn {
	margin: 0;
	font-weight: 400;
}
.icon-close{ position:absolute; right:-10px; top:-10px;}
.btn-default {
    background: #b90e3b;
    width: 70%;
    color: #fff;
    padding: 20px 0;
    text-transform: uppercase;
}
.btn-default:hover {
	color: #fcfcfc;
	background-color: #333;
	border-color: #333;
}


.star-text .cancel-on-png, .star-text .cancel-off-png, .star-text .star-on-png, .star-text .star-off-png,
.star-text .star-half-png{
    font-size: 1em !important;
}
.star-text-shown .cancel-on-png, .star-text-shown .cancel-off-png, .star-text-shown .star-on-png,
.star-text-shown .star-off-png, .star-text-shown .star-half-png{
    font-size: 1em !important;
}
.star-text{
    cursor: pointer;
    height: 60px;
    text-align: center;
    margin-top: 46px;
}
.qrcode-text-btn > input[type=file] {
  position: absolute;
  overflow: hidden;
  width: 1px;
  height: 1px;
  opacity: 0;
}

.cuisine-left {
	width: 60px;
}
.cuisine-right {
	float: left;
	width: calc(100% - 60px);
}
.cuisine-left img {
	border-radius: 100%;
}
@media only screen and (max-width: 360px){
.order-popup li {
	width: 47% !important;
	margin-top: 16px !important;
}
.order-popup h2 {
	font-size: 15px !important;
}
}

</style>
@endsection

@section('content')
<div class="content-pages page-content">
@if(count($items))
@foreach($items as $item)
<div class="cuisine-detail">
	{{--<div class="cuisine-left"> <a href="{{ route('customer.invoice.view', $item->id) }}">
    <img src="{{ static_file('images/naan.png') }}" alt=""></a>
  </div>--}}
	<div class="cuisine-rightt">
		<div class="full-row">
			<div class="left-box">
				<a href="{{ route('customer.invoice.view', $item->id) }}">
					<h2>{{ $item->description }}</h2>
					<p><b>Amount:</b> S${{ $item->transaction_amount or '0' }}</p>
					<p><b>Dated:</b> {{ Carbon\Carbon::parse($item->transaction_date)->format('d/m/Y') }}</p>
				</a>
			</div>
			<div class="right-box">
				<span class="re-order">
					 <a class="btn-carat" href="{{ route('customer.invoice.view', $item->id) }}" target="_blank">&nbsp;Invoice&nbsp;&nbsp;</a>
				</span>
			</div>
		</div>
		<div class="full-row">
			<div class="left-box">
				<p class="cancel-text">
						{{ $item->transaction_status }}
				</p>
			</div>
			<div class="right-box">
			</div>
		</div>
	</div>
</div>
@endforeach
@else
<div class="list-bg">
  <ul>
    <li>
      <h4>You have not done any transaction yet.</h4>
    </li>
  </ul>
</div>
@endif
</div>

@endsection
