@extends('layouts.customer')

@section('styles')
<style>
.top-head {
	border-bottom: 1px solid #dcdcdc;
	margin-bottom: 15px;
	padding: 10px 0;
}
.row{margin-right: 0;margin-left:0;}
.detail_headerr p .brought {
	font-size: 15px;
	margin-bottom: 8px;
	display: inline-block;
}
.detail_headerr p {
	padding-top: 20px;
	text-align: center;
	color: #fff;
	font-size: 1.6em;
}
p {
	line-height: 1.4;
}
.top-head p {
	line-height: 1.4;
	margin-bottom: 5px;
}
.mt-10 {
	margin-top: 10px;
}

</style>

@endsection

@section('header')
<header class="header">
  <h2>Order Detail</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;"><img src="{{ static_file('customer/images/icon-back-arrow.png') }}" alt=""></a></span>
</header>
@endsection

@section('content')
<div class="content-pages page-content sec-recept">
    <div class="detail_headerr" style="background:#b90e3b;height:163px;">
        <p><span>{{ $order->status->name or '--' }}</span>
            {{-- @if($order->rating)
            <span class="star-text" data-score="{{ $order->rating}}"></span>
            @endif --}}
            <br>
            <span class="brought">This service is brought to you by</span><br>
            <img src="{{ static_file('images/naan.png') }}" alt="" width="70">
        </p>
        <!-- <h2> </h2> -->
    </div>
<div class="row top-head">
  <div class="col-xs-12">
    <p><b><i>From</i></b></p>
    <p><b>{{ $restaurant->name }}</b></p>
    <p>{{ $restaurant->address }}</p>
    <p>+65: {{ $restaurant->phone_no }}</p>
  </div>
  <div class="col-xs-12 mt-10">
    <p><b><i>Deliver To</i></b></p>
    <p><b>{{ $order->user->name or ''}}</b></p>
    @if($order->address)
      <p>{{ $order->block_no }}</p>
      <p>{{ $order->address }}</p>
    @else
      <p>{{ $order->dormitory->full_name }}</p>
      <p>Singapore</p>
      <p>+65: {{ $order->phone_no }}</p>
    @endif
  </div>
	@if($order->type == 'package')
	<div class="col-xs-12">
    <p><b>Start Date</b>: {{ $start_date or '' }}</p>
  </div>
	<div class="col-xs-12">
    <p><b>End Date</b>: {{ $end_date or '' }}</p>
  </div>
	@else
	<div class="col-xs-12">
    <p><b>Order Date</b>: {{ Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</p>
  </div>
	@endif
</div>
<div class="order-detail">
<h1>Invoice: {{$order->invoice_id}}</h1>
<div class="table-wrapper">
 <table class="gridtable" width="100%" cellspacing="0" cellpadding="0">
     <?php $total = 0; ?>
  @foreach($order->items as $item)
  <tr>
    <td><strong>{{ $item->quantity }}x {{ $item->item->name }}</strong></td>
    <?php $total += (isset($item->item->price)?$item->item->price:0)*$item->quantity; ?>
     <td style="text-align:right;">S${{ number_format((isset($item->item->price)?$item->item->price:0)*$item->quantity , 2) }}</td>
  </tr>
  @endforeach
  <tr >
    <td><strong>Subtotal</strong></td>
    <td style="text-align:right;">S${{ number_format($total,2) }}</td>
  </tr>
  <tr>
    <td><strong>Discount</strong></td>
    <td style="text-align:right;">S${{ number_format($order->discount, 2) }}</td>
  </tr>
  <tr>
  <td><strong>NaanStap Charges</strong></td>
   <td style="text-align:right;">S${{ number_format($order->naanstap, 2) }}</td>
  </tr>
  <!-- <tr>
  <td><strong>Transaction Charges</strong></td>
   <td style="text-align:right;"><strong>S${{ number_format($order->flexm, 2) }}</strong></td>
  </tr> -->
  <tr>
  <td><strong>Total</strong></td>
   <td class="colorRed" style="text-align:right;"><b>S${{ number_format(($total+$order->naanstap-$order->discount),2) }}<b></td>
  </tr>
</table>
<div class="text-center">
  <span class="re-order">
     <a class="btn-carat" href="{{ route('food.customer.order.print', $order->id) }}" target="_blank">Print</a>
  </span>
</div>
</div>
</div>
</div>
@endsection
@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')

@endsection
