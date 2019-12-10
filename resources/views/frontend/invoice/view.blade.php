@extends('layouts.invoice_listing')

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

@section('content')
<div class="content-pages page-content sec-recept">
    <div class="detail_headerr" style="background:#b90e3b;height:60px;">
        <p><span>{{ $item->transaction_status or '--' }}</span>
            <br>
            <!-- <span class="brought">This service is brought to you by</span><br>
            <img src="{{ static_file('images/naan.png') }}" alt="" width="70"> -->
        </p>
    </div>
<div class="row top-head">
  <div class="col-xs-12">
    <p><b><i>From</i></b></p>
    <p><b>{{ $item->merchant_name }}</b></p>
    <p>{{ @$item->merchant->merchant_address_1 }}</p>

  </div>
  <div class="col-xs-12 mt-10">
    <p><b><i>To</i></b></p>
    <p><b>{{ $item->user->name or ''}}</b></p>
    @if($item->user)
      <?php $user = $item->user; ?>
      @if(@$user->profile->dormitory)
      <p>{{ $user->profile->dormitory->address or '' }}</p>
      @else
      <p>{{ $user->profile->street_address }}</p>
      @endif
    @endif
  </div>
</div>
<div class="order-detail">
<h1>Invoice: {{$item->invoice_id}}</h1>
<div class="table-wrapper">
 <table class="gridtable" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td><strong>1 x {{ $item->description }}</strong></td>
    <td style="text-align:right;">S${{ number_format($item->transaction_amount , 2) }}</td>
  </tr>

  {{-- <tr >
    <td><strong>Subtotal</strong></td>
    <td style="text-align:right;">S${{ number_format($total,2) }}</td>
  </tr>
  <tr>
    <td><strong>Discount</strong></td>
    <td style="text-align:right;">S${{ number_format($item->discount, 2) }}</td>
  </tr>
  <tr>
  <td><strong>NaanStap Charges</strong></td>
   <td style="text-align:right;">S${{ number_format($item->naanstap, 2) }}</td>
 </tr> --}}
  <tr>
    <td><strong>Total</strong></td>
    <td class="colorRed" style="text-align:right;"><b>S${{ number_format(($item->transaction_amount),2) }}<b></td>
  </tr>
</table>
<div class="text-center">
  <span class="re-order">
     <!--<a class="btn-carat" href="{{ route('customer.invoice.print', $item->id) }}" target="_blank">Print</a>-->
  </span>
</div>
</div>
</div>
</div>
@endsection

@section('scripts')
@endsection
