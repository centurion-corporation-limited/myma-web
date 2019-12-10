@extends('layouts.merchant')

@section('header')
<header class="header">
  <h2>Invoice</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('merchant/images/icon-back-arrow.png') }}" alt=""></a>
  </span>
  </header>
@endsection
@section('content')
<div class="invoice-recept page-content">
  <h2>Invoice No <span>: #{{ $invoice->id }}</span> <span class="pull-right badge">{{ $invoice->status }}</span></h2>
  <h2>Invoice Date <span>: {{ $invoice->created_at->format('m/d/Y') }}</span></h2>
  <table class="table-grid" width="100%%" cellspacing="0" cellpadding="0">
    <tr class="row-line">
      <td>Item Name</td>
      <td style="text-align:right;">Amount</td>
    </tr>
    <?php $total = 0; ?>
    @foreach($items as $item)
    <tr class="row-line">
      <td> {{ $item->name }}<span>S${{ $item->price }} x {{ $item->quantity }}</span></td>
      <td style="text-align:right;">S${{ $item->price*$item->quantity }}</td>
    </tr>
    <?php $total += $item->price*$item->quantity; ?>
    @endforeach
    <tr>
      <td style="text-align:right;">Sub Total</td>
      <td style="text-align:right;">S${{ $total }}</td>
    </tr>
    <!-- <tr>
      <td style="text-align:right;">Vat</td>
      <td style="text-align:right;">$10</td>
    </tr> -->
    <tr>
      <td style="text-align:right;">Sales Tax</td>
      <td style="text-align:right;">S$0</td>
    </tr>
    <tr class="row-line">
      <td style="text-align:right;">WLC Charges</td>
      <td style="text-align:right;">S$0</td>
    </tr>
    <tr>
      <td style="text-align:right; font-weight:bold;">Total Paid:</td>
      <td style="text-align:right; font-weight:bold;">S${{ $total }}</td>
    </tr>
  </table>
</div>

@endsection
