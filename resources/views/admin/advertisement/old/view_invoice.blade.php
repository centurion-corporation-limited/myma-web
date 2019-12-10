@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_content">
        <div class="row" >
            <div class="col-md-8 col-xs-12">
                <img class="img-rounded" height="{{ $invoice->logo_height }}" src="{{ $invoice->logo }}">
            </div>
            <div class="col-md-4 col-xs-12">
                <b>Date: </b> {{ $invoice->date->formatLocalized('%d %B %Y') }}<br />
                <b>Status: </b> {{ $invoice->status }}
                <br />
            </div>
        </div>
        <br />

        <h2 >{{ $invoice->name }} {{ $invoice->number ? '#' . $invoice->number : '' }}</h2>
        <div class="row">
            <div class="col-md-5 col-xs-12">
                <h4>Business Details:</h4>
                <div class="panel panel-default">
                    <div class="panel-body">
                        {!! $invoice->business_details->count() == 0 ? '<i>No business details</i><br />' : '' !!}
                        {{ $invoice->business_details->get('name') }}<br />
                        ID: {{ $invoice->business_details->get('id') }}<br />
                        {{ $invoice->business_details->get('phone') }}<br />
                        {{ $invoice->business_details->get('location') }}<br />
                        {{ $invoice->business_details->get('zip') }} {{ $invoice->business_details->get('city') }}
                        {{ $invoice->business_details->get('country') }}<br />
                    </div>
                </div>
            </div>
            <div class="col-md-7 col-xs-12">
                <h4>Customer Details:</h4>
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
                </div>
            </div>
        </div>
        <h4>Items:</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <!-- <th>ID</th> -->
                    <th>Item Name</th>
                    <th>Price ({{ $invoice->formatCurrency()->symbol }})</th>
                    <th>Quantity</th>
                    <th>Total ({{ $invoice->formatCurrency()->symbol }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <!-- <td>{{ $item->get('id') }}</td> -->
                        <td>{{ $item->get('name') }}</td>
                        <td>{{ $item->get('price') }} </td>
                        <td>{{ $item->get('ammount') }}</td>
                        <td>{{ $item->get('totalPrice') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="row" style="clear:both; position:relative;">

            @if($invoice->notes)
                <div class="col-sm-8 col-xs-12">
                    <h4>Notes:</h4>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            {{ $invoice->notes }}
                        </div>
                    </div>
                </div>
                @else
                <div class="col-sm-8 col-xs-12">

                </div>

            @endif
            <div class="col-sm-4 col-xs-12">
                <h4>Total:</h4>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><b>Subtotal</b></td>
                            <td>{{ $invoice->subTotalPriceFormatted() }} {{ $invoice->formatCurrency()->symbol }}</td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Taxes {{ $invoice->tax_type == 'percentage' ? '(' . $invoice->tax . '%)' : '' }}
                                </b>
                            </td>
                            <td>{{ $invoice->taxPriceFormatted() }} {{ $invoice->formatCurrency()->symbol }}</td>
                        </tr>
                        <tr>
                            <td><b>TOTAL</b></td>
                            <td><b>{{ $invoice->totalPriceFormatted() }} {{ $invoice->formatCurrency()->symbol }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @if ($invoice->footnote)
            <br /><br />
            <div class="well">
                {{ $invoice->footnote }}
            </div>
        @endif

    </div>
  </div>
</div>
@endsection
