@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">

<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/3.1.6/footable.bootstrap.min.css" />
<style>
    form {
    display: inline-block;
    width: 100%;
    }

    .transaction-form .select2 {
    	width: 100% !important;
    }
    .button-tran-abs {
    	bottom: 20px;
    	right: 2.5%;
    }
    @media only screen and (max-width: 991px){
      .row.flex-row {
      	width: 100% !important;
      }
    }
</style>
@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">

    <div class="x_panel">
        <div class="x_title">
            <h2>Revenue Report</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="form-outer">

                <form action="{{ route('admin.revenue.report') }}" method="GET" class="form-inline">
                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="dormitory_id">Date range </label>
                            <div class="input-group input-daterange">
                                <input type="text" autocomplete="off" placeholder="From" class="form-control" name="start" value="{{ Request::input('start') }}">
                                <div class="input-group-addon">to</div>
                                <input type="text" autocomplete="off" placeholder="To" class="form-control" name="end" value="{{ Request::input('end') }}">
                            </div>
                        </div>

                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="mid">Merchant Name</label>
                            <div class="input-group">
                                <select class="form-control" name="mid">
                                    <option value="" @if(Request::input('mid')=="" ) selected="selected" @endif>Select a merchant</option>
                                    @foreach($merchants as $mid => $name)
                                    <option value="{{ $mid }}" @if(Request::input('mid')==$mid) selected="selected" @endif>{{ $name }}</option>
                                    @endforeach
                                    <option value="singx" @if(Request::input('mid')== 'singx') selected="selected" @endif>Singx</option>
                                    @foreach($ad_manager as $mid => $name)
                                    <option value="ad_{{ $mid }}" @if(Request::input('mid')==$mid) selected="selected" @endif>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row flex-row  button-tran">
                            <button class="btn btn-success" type="submit">Search</button>
                            <a href="{{ route('admin.revenue.report') }}" class="btn btn-success">Reset</a>
                        </div>

                    </form>
                    <form action="{{ route('admin.revenue.download') }}" method="GET" class="form-inline button-tran-abs">
                        <input type="hidden" name="mid" value="{{ Request::input('mid') }}">
                        <input type="hidden" name="start" value="{{ Request::input('start') }}">
                        <input type="hidden" name="end" value="{{ Request::input('end') }}">
                        <button class="btn btn-success" type="submit">Export</button>
                    </form>
            </div>
            <div class="reponsive-table">
            <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>@sortablelink('vendor_code', 'Vendor Code')</th>
                        <th>@sortablelink('vendor_name', 'Vendor Name')</th>
                        <th>@sortablelink('txn_date_from', 'Txn date from')</th>
                        <th>@sortablelink('txn_date_to', 'Txn date to')</th>
                        <th>@sortablelink('product_type', 'Product type')</th>
                        <th>@sortablelink('vendor_qty', 'Vendor QTY')</th>
                        <th>@sortablelink('vendor_sale', 'Vendor Sale')</th>
                        <th>@sortablelink('revenue_earned', 'Myma Comms share')</th>
                        <th>@sortablelink('sharing_charged', 'Cost sharing charged')</th>
                        <th>@sortablelink('txn_fee', 'Myma Wallet Txn fee earned')</th>
                        <th>@sortablelink('wallet_cost', 'Flexm cost')</th>
                        <th>@sortablelink('net_revenue_earned', 'Net revenue earned')</th>
                        <th>@sortablelink('gst', 'GST')</th>
                        <th>@sortablelink('payback_vendor', 'Payback to vendor')</th>
                        <th>@sortablelink('revenue_model', 'Revenue Model')</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $key => $item)
                    <?php
                    $total['vendor_sale'] += str_replace(',','',$item->vendor_sale) + 0;
                    $total['gross'] += str_replace(',','',$item->gross) + 0;
                    $total['cost_sharing'] += str_replace(',','',$item->cost_sharing) + 0;
                    $total['txn_fee'] += str_replace(',','',$item->txn_fee) + 0;
                    $total['cost_charged'] += str_replace(',','',$item->cost_charged) + 0;
                    $total['net'] += str_replace(',','',$item->net) + 0;
                    $total['gst'] += str_replace(',','',@$item->gst) + 0;
                    $total['payback_vendor'] += str_replace(',','',$item->payback_vendor) + 0;
                    ?>
                    <tr>
                        <!-- <td>{{ ++$key }}</td> -->
                        <td>{{ $item->vendor_code }}</td>
                        <td>{{ $item->vendor_name }}</td>
                        <td>{{ $item->from_date }}</td>
                        <td>{{ $item->to_date }}</td>
                        <td>{{ $item->product_type }}</td>
                        <td>{{ $item->vendor_qty }}</td>
                        <td class="amount">${{ $item->vendor_sale }}</td>
                        <td class="amount">${{ $item->gross }}</td>
                        <td class="amount">${{ number_format($item->cost_sharing,4) }}</td>
                        <td class="amount">${{ $item->txn_fee }}</td>
                        <td class="amount">${{ $item->cost_charged }}</td>
                        <td class="amount">${{ $item->net }}</td>
                        <td class="amount">${{ @$item->gst }}</td>
                        <td class="amount">${{ $item->payback_vendor }}</td>
                        <td class="mob-right">{{ $item->revenue_model }}</td>
                        <td>
                          @if($item->type == 'instore')
                            <a title="View Transactions" href="{{ route('admin.transactions.instore', ['start' => $item->from_date, 'end' => $item->to_date, 'mid' => $item->vendor_code, 'detail' => true]) }}"><i class="fa fa-2x fa-eye"></i></a>
                          @elseif($item->type == 'singx')
                            <a title="View Transactions" href="{{ route('admin.singx.remittance', ['start' => $item->from_date, 'end' => $item->to_date, 'detail' => true]) }}"><i class="fa fa-2x fa-eye"></i></a>
                          @else
                            <a title="View Transactions" href="{{ route('admin.transactions.inapp', ['start' => $item->from_date, 'end' => $item->to_date, 'mid' => $item->vendor_code, 'detail' => true]) }}"><i class="fa fa-2x fa-eye"></i></a>
                          @endif
                        </td>
                    </tr>
                    @endforeach
                    <td><b>Total</b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="amount">${{ number_format($total['vendor_sale'],4) }}</td>
                    <td class="amount">${{ number_format($total['gross'],4) }}</td>
                    <td class="amount">${{ number_format($total['cost_sharing'],4) }}</td>
                    <td class="amount">${{ number_format($total['txn_fee'],4) }}</td>
                    <td class="amount">${{ number_format($total['cost_charged'],4) }}</td>
                    <td class="amount">${{ number_format($total['net'],4) }}</td>
                    <td class="amount">${{ number_format($total['gst'],4) }}</td>
                    <td class="amount">${{ number_format($total['payback_vendor'],4) }}</td>
                    <td></td>
                </tbody>

            </table>
          </div>
            {{-- @include('partials.paging', $items) --}}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ static_file('js/plugins/bootbox.min.js') }}"></script>
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/3.1.6/footable.min.js"></script>
<script>
    $(document).ready(function() {
        $('.input-daterange').datepicker({
            todayBtn: "linked",
            format: "yyyy-mm-dd"
        });

        if($('.foo_table').length){
            $('.foo_table').footable();
        }
    });
    $('body').on('click', '.post-delete', function(event) {
        event.preventDefault();

        var message = $(this).data('message'),
            url = $(this).attr('href');

        bootbox.dialog({
            message: message,
            buttons: {
                danger: {
                    label: "Yes",
                    //className: "red",
                    callback: function() {
                        $.ajax({
                            url: url,
                            //  type: 'delete',
                            //container: '#pjax-container'
                        }).done(function(data) {
                            //console.log(data);
                            location.reload();
                        });
                    }
                },
                success: {
                    label: "Cancel",
                    //className: "green"
                }
            }
        });
    })

</script>
@stop
