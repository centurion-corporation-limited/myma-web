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


@section('content')

<table style="margin: 10px auto; max-width: 90%; width: 100%;">
	<tr>
		<td colspan="2" style="text-align: center;">
			<h2><img src="{{ static_file('images/naan.png') }}" alt="" width="40"> <b>Naanstap</b></h2>
		</td>
	</tr>
	<tr>
		<td colspan="2"><div style="height: 40px; width: 100%"></div></td>
	</tr>
	<tr>
		<td style="text-align: left;">
			<div style="font-size: 14px; display: inline-block; width: auto; padding-left: 5px;">
				<p><b><i>From</i></b></p>
		    <p><b>Naanstap</b></p>
		    <p>Singapore</p>
		    {{--<p>+65: {{ $restaurant->phone_no }}</p>--}}
			</div>
		</td>
		<td style="text-align: right;">
			<div style="font-size: 14px; display: inline-block; width: auto; text-align: left; padding-left: 5px;">
				<p><b><i>To</i></b></p>
		    <p><b>WLC</b></p>
		    <p>Singapore</p>
		      {{--<p>+65: {{ $order->phone_no }}</p>--}}

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
						<td style="padding: 5px 10px;">{{ $item->item->name }}</td>
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
			  <tr>
			  	<td style="padding: 5px 10px;"><strong>Naanstap Charges</strong></td>
			   <td style="padding: 5px 10px;">S${{ number_format($order->naanstap, 2) }}</td>
			  </tr>
				<!-- <tr>
			  <td style="padding: 5px 10px;"><strong>Transaction Charges</strong></td>
			   <td style="padding: 5px 10px;"><strong>S${{ number_format($order->flexm, 2) }}</strong></td>
			  </tr> -->
			  <tr>
			  <td style="padding: 5px 10px;"><strong>Total</strong></td>
			   <td style="padding: 5px 10px;"><b>S${{ number_format(($total+$order->naanstap-$order->discount),2) }}<b></td>
			  </tr>
			</table>
		</td>
	</tr>
</table>

@endsection
