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
			<h2>
        <!-- <img src="{{ static_file('images/naan.png') }}" alt="" width="40"> -->
        <b>MyMA</b>
      </h2>
		</td>
	</tr>
	<tr>
		<td colspan="2"><div style="height: 40px; width: 100%"></div></td>
	</tr>
	<tr>
		<td style="text-align: left;">
			<div style="font-size: 14px; display: inline-block; width: auto; padding-left: 5px;">
				<p><b><i>From</i></b></p>
		    <p><b>{{ @$merchant->merchant_name }}</b></p>
		    <p>{{ @$merchant->merchant_address_1 }}</p>
		    {{-- <p>+65: {{ @$restaurant->phone_no }}</p> --}}
        @if($merchant->gst)
        <p>GST Number : {{ $merchant->gst_number }}</p>
        @endif
			</div>
		</td>
		<td style="text-align: right;">
			<div style="font-size: 14px; display: inline-block; width: auto; text-align: left; padding-left: 5px;">
				<p><b><i>To</i></b></p>
				<p><b>{{ $item->user->name or ''}}</b></p>
        @if($item->user)
          <?php $user = $item->user; ?>
          @if(@$user->profile->dormitory)
          <p>{{ $user->profile->dormitory->address or '' }}</p>
          @else
          <p>{{ $user->profile->street_address or '' }}</p>
          @endif
        @endif

			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr>
		</td>
	</tr>
	<tr>
		<td colspan="2"><b>Invoice Id:</b> {{ $item->invoice_id }}</td>
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

					<tr>
						<td style="padding: 5px 10px;">1</td>
						<td style="padding: 5px 10px;">{{ $item->description }}</td>
						<td style="text-align: right; padding: 5px 10px;">S${{ number_format($item->transaction_amount, 2) }}</td>
					</tr>

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
			    <td style="padding: 5px 10px;"><strong>Subtotal</strong>
						@if($merchant->gst) (Inclusive GST.) @endif
					</td>
			    <td style="padding: 5px 10px;">S${{ number_format($item->transaction_amount,2) }}</td>
			  </tr>
			  {{-- <tr>
			    <td style="padding: 5px 10px;"><strong>WLC Share</strong></td>
			    <td style="padding: 5px 10px;">S${{ number_format(($item->transaction_amount-$item->other_share), 2) }}</td>
			  </tr>
			  <tr>
			  	<td style="padding: 5px 10px;"><strong>Merchant Share</strong></td>
			   <td style="padding: 5px 10px;">S${{ number_format($item->other_share, 2) }}</td>
       </tr> --}}
				<tr>
			  <td style="padding: 5px 10px;"><strong>Total</strong></td>
			   <td style="padding: 5px 10px;"><b>S${{ number_format($item->transaction_amount,2) }}<b></td>
			  </tr>
			</table>
		</td>
	</tr>
</table>

@endsection
