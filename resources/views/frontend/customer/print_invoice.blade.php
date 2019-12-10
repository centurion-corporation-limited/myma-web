@extends('layouts.invoice')

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
	margin-bottom: 2px;
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

<table style="margin: 10px auto; max-width: 90%; width: 100%;">
	<tr>
		<td colspan="3" style="text-align: center;">
			<h2>
			     
				<img src="{{ static_file('images/naan.png') }}" alt="" width="70">
				 
			    <b>Naanstap</b></h2>
 		@if($order->type == "single")
				<span>SG Interactive Pte Ltd 10 Ubi Crescent, Ubi Techpark #07-17, Singapore 408567</span>
				@endif
		</td>
	</tr>
	<tr>
		<td colspan="2"><div style="height: 40px; width: 100%"></div></td>
	</tr>
	<tr>
		<td style="text-align: left;">
			<div style="font-size: 14px; display: inline-block; width: auto; padding-left: 5px;">
				<p><b><i>From</i></b></p>
		    <p><b>{{ @$restaurant->name }}</b></p>
		    <p>{{ @$restaurant->address }}</p>
		    <p>+65: {{ @$restaurant->phone_no }}</p>
		     @if($restaurant->gst_no && $order->type == "package")
				<p><strong>GST No</strong> - {{ @$restaurant->gst_no }}</p>
			 @endif 
			</div>
		</td>
		<td style="text-align: right;">
			<div style="font-size: 14px; display: inline-block; width: auto; text-align: left; padding-left: 5px;">
				<p><b><i>Deliver To</i></b></p>
		    <p><b>{{ $order->user->name or ''}}</b></p>
		    @if($order->address)
		      <p>{{ $order->block_no }}</p>
		      <p>{{ $order->address }}</p>
		    @else
		      <p>{{ @$order->dormitory->address }}</p>
		      <p>+65: {{ $order->phone_no }}</p>
		    @endif
		    @if($order->type == 'package')
					<p><b>Start Date</b>: {{ $start_date or '' }}</p>
			  	<p><b>End Date</b>: {{ $end_date or '' }}</p>
			  @else
				  <p><b>Order Date</b>: {{ Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</p>
			  @endif
				<p><b>Order ID</b>: {{ $order->id }}</p>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr>
		</td>
	</tr>
	<tr>
		<td colspan="2"><b>Invoice Id:</b> {{$order->invoice_id}}</td>
		
	</tr>
	<tr>
	@if(@$restaurant->nea_number)
			<td colspan="2"><b>NEA LICENSE:</b> {{ @$restaurant->nea_number }}</td>
	  @endif
	  </tr>
	<tr>
		<td colspan="2">
			<table style="margin-top: 20px;width: 100%; font-size: 14px;">
				<thead style="background: #f3f6f9;">
					<tr>
						<th style="padding: 8px 10px;">Quantity</th>
						<th style="padding: 8px 10px;">Item</th>
						<th style="text-align: right; padding: 8px 10px;">Price</th>
					</tr>
				</thead>
				<tbody>
					<?php $total = 0; ?>
		  	  @foreach($order->items as $item)
					<tr>
						<td style="padding: 5px 10px;">{{ $item->quantity }}</td>
						<td style="padding: 5px 10px;">{{ @$item->item->name }}</td>
						<?php $total += (isset($item->item->price)?$item->item->price:0)*$item->quantity; ?>
						<td style="text-align: right; padding: 5px 10px;">S${{ number_format((isset($item->item->price)?$item->item->price:0)*$item->quantity , 2) }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin-bottom: 0;">
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<table style="width: 100%; margin-top: 20px; text-align: right;font-size: 14px;">
				<tr>
			    <td style="padding: 5px 10px;"><strong>Subtotal</strong></td>
			    <td style="padding: 5px 10px;">S${{ number_format($total,2) }}</td>
			  </tr>
			  <tr>
			    <td style="padding: 5px 10px;"><strong>Discount</strong></td>
			    <td style="padding: 5px 10px;">S${{ number_format($order->discount, 2) }}</td>
			  </tr>
			 @if($order->type == "single")
			  <tr>
			  	<td style="padding: 5px 10px;"><strong>Naanstap Charges</strong></td>
			   <td style="padding: 5px 10px;">S${{ number_format($order->naanstap, 2) }}</td>
			  </tr>
				<tr>
			  <td style="padding: 5px 10px;"><strong>Total</strong></td>
			   <td style="padding: 5px 10px;"><b>S${{ number_format(($total+$order->naanstap-$order->discount),2) }}<b></td>
			  </tr>
				@else
				<tr>
			  <td style="padding: 5px 10px;"><strong>Total</strong></td>
			   <td style="padding: 5px 10px;"><b>S${{ number_format(($total-$order->discount),2) }}<b></td>
			  </tr>
				@endif
			</table>
		</td>
	</tr>
</table>

<!-- <a class="btn-carat" href="{{ route('food.customer.order.print', $order->id) }}" target="_blank">Print</a> -->

@endsection
@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')

@endsection
