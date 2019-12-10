@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_content">
        <div class="row" >
            <div class="col-md-6 col-xs-12">
            <h4>Tax Invoice</h4>
            </div>
            <div class="col-md-6 col-xs-12">
              <img class="img-rounded" height="{{ $invoice->logo_height }}" src="{{ static_file($invoice->logo) }}">
            </div>
        </div>
        <br />

        <div class="row">
            <div class="col-md-5 col-xs-12">
                <h4>To:</h4>
                <div class="panel panel-default">
                    <div class="panel-body">
                        {!! $invoice->business_details->count() == 0 ? '<i>No business details</i><br />' : '' !!}
                        {{ $invoice->business_details->get('name') }}<br />
                        <!-- ID: {{ $invoice->business_details->get('id') }}<br /> -->
                        {{ $invoice->business_details->get('location') }}<br />
                        {{ $invoice->business_details->get('city') }}
                        {{ $invoice->business_details->get('phone') }}<br />
                        {{ $invoice->business_details->get('country') }} {{ $invoice->business_details->get('zip') }}<br />
                    </div>
                </div>
            </div>
            <div class="col-md-offset-4 col-md-3 col-xs-12">
                <b>{{ $invoice->name }} No.</b><span>:</span> {{ $invoice->number ? '#' . $invoice->number : '' }} <br />
                <b>Invoice Date </b><span>:</span> {{-- $invoice->date->formatLocalized('%d %B %Y') --}}{{ $adinvoice->created_at->format('d M Y') }}<br />
                {{-- <b>Page </b><span>:</span> 1 --}}{{-- $invoice->status --}}

                <!-- <h4>Customer Details:</h4>
                <div class="panel panel-default">
                    <div class="panel-body">
                        {!! $invoice->customer_details->count() == 0 ? '<i>No customer details</i><br />' : '' !!}
                        {{ $invoice->customer_details->get('name') }}<br />
                        ID: {{ $invoice->customer_details->get('id') }}<br />
                        {{ $invoice->customer_details->get('phone') }}<br />
                        {{ $invoice->customer_details->get('location') }}<br />
                        {{ $invoice->customer_details->get('zip') }} {{ $invoice->customer_details->get('city') }}
                        {{ $invoice->customer_details->get('country') }}<br />
                    </div>
                </div> -->
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Customer no</th>
                    <th>Sales Person</th>
                    <th>Payment Mode</th>
                    <th>Credit Term</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                {{-- @foreach ($invoice->items as $item) --}}
                    <tr>
                        <td>{{ $adinvoice->customer_no }}</td>
                        <td>{{ $adinvoice->sales_person }}</td>
                        <td>{{ $adinvoice->payment_mode }}</td>
                        <td>{{ $adinvoice->credit_term }}</td>
                        <td>{{ $adinvoice->due_date }}</td>
                    </tr>
                {{-- @endforeach --}}
            </tbody>
        </table>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>TA Ref</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>GST %</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->get('name') }}</td>
                        <td></td>
                        <td>{{ $item->get('ammount') }}</td>
                        <td>{{ number_format($item->get('price'),2) }} </td>
                        <td>{{ $invoice->tax }} %</td>
                        <td>{{ number_format($item->get('totalPrice'),2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="row" style="clear:both; position:relative;">


            <div class="col-sm-8 col-xs-12">
                <h4>Currency:{{ $invoice->currency }}</h4>
            </div>

            <div class="col-sm-4 col-xs-12">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><b>Subtotal</b></td>
                            <td>{{ $invoice->subTotalPriceFormatted() }}</td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    GST Total
                                </b>
                            </td>
                            <td>{{ $invoice->taxPriceFormatted() }}</td>
                        </tr>
                        <tr>
                            <td><b>Total</b></td>
                            <td><b>{{ $invoice->totalPriceFormatted() }} </b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <br /><br />
        <div class="well">
          For payment via CHEQUE - Crossed cheque is to be made payable to "WLC Facilities Services Pte Ltd".<br />
          Please made full payment by the due date to avoid admin charge on late payment and late payment interest.<br />
          Please indicate the invoice number at the back of your cheque. <br />
          This is a computer generated invoice. No signature is required.
        </div>


        <br />
        <div class="well">
                WLC FACILITIES SERVICES PTE LTD </br>
                45 Ubi Road 1 #05-01 Singapore 408696 | T 6745 3288 | F 6743 5818 |</br>
                Reg No. : 201524486H | GST Reg. No.: 201524486H
        </div>

    </div>
  </div>
</div>
@endsection
