@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<style>
.button-tran-abs {
    bottom: 0;
}
</style>

@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="panel cardbox">
                <div class="panel-body card-item panel-refresh">
                    <div class="card-details">
                        <div class="col-md-6">Myma Total</div>
                        <div class="col-md-6 text-right timer" data-to="{{ $data['myma_total'] or 0 }}" data-speed="1500">{{ $data['myma_total'] or 0 }}</div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="panel cardbox">
                <div class="panel-body card-item panel-refresh">
                    <div class="card-details">
                        <div class="col-md-6">Merchant Total</div>
                        <div class="col-md-6 text-right timer" data-to="{{ $data['merchant_total'] or 0 }}" data-speed="1500">{{ $data['merchant_total'] or 0 }}</div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="panel cardbox">
                <div class="panel-body card-item panel-refresh">
                    <div class="card-details">
                        <div class="col-md-6">Flexm Total</div>
                        <div class="col-md-6 text-right timer" data-to="{{ $data['flexm_total'] or 0 }}" data-speed="1500">{{ $data['flexm_total'] or 0 }}</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="x_panel">
        <div class="x_title">
            <h2>Transactions</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="form-outer">
                @if($type == 'instore')
                <form action="{{ route('admin.transactions.instore') }}" method="GET" class="form-inline" style="width: calc(100% + 20px);">
                    @else
                    <form action="{{ route('admin.transactions.inapp') }}" method="GET" class="form-inline" style="width: calc(100% + 20px);">
                        @endif
                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="dormitory_id">Date range </label>
                            <div class="input-group input-daterange">
                                <input type="text" placeholder="From" class="form-control" name="start" value="{{ Request::input('start') }}">
                                <div class="input-group-addon">to</div>
                                <input type="text" placeholder="To" class="form-control" name="end" value="{{ Request::input('end') }}">
                            </div>
                        </div>

                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="dormitory_id">Merchant</label>
                            <div class="input-group ">
                                <select class="form-control" name="mid">
                                    <option value="" @if(Request::input('mid')=="" ) selected="selected" @endif>Select a merchant</option>
                                    @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" @if(Request::input('mid')==$merchant->id) selected="selected" @endif>{{ $merchant->user->name or '-' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="">Transaction ID</label>
                            <div class="input-group">
                                <input type="text" placeholder="Transaction ID" name="transaction_id" value="{{ Request::input('transaction_id') }}" class="form-control input-small">
                            </div>
                        </div>

                        <div class="row flex-row  button-tran">
                            <button class="btn btn-success" type="submit">Search</button>
                            @if($type == 'instore')
                            <a href="{{ route('admin.transactions.instore') }}" class="btn btn-success">Reset</a>
                            @else
                            <a href="{{ route('admin.transactions.inapp') }}" class="btn btn-success">Reset</a>
                            @endif
                        </div>
                    </form>
                    <form action="{{ route('admin.transactions.export') }}" method="GET" class="form-inline button-tran-abs">
                        <input type="hidden" name="transaction_id" value="{{ Request::input('transaction_id') }}">
                        <input type="hidden" name="type" value="{{ $type or 'inapp' }}">
                        <input type="hidden" name="mid" value="{{ Request::input('mid') }}">
                        <input type="hidden" name="start" value="{{ Request::input('start') }}">
                        <input type="hidden" name="end" value="{{ Request::input('end') }}">
                        <!-- <input type="hidden" name="email" value="{{ Request::input('email') }}" > -->
                        <button class="btn btn-success" type="submit">Export</button>
                    </form>
            </div>
            <table class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>@sortablelink('created_at', 'Txn Date')</th>
                        <th>@sortablelink('transaction_ref_no', 'Txn ID')</th>
                        <th>@sortablelink('transaction_status', 'Status')</th>
                        <th data-breakpoints="xs sm">@sortablelink('user_id', 'User')</th>
                        <th data-breakpoints="xs sm">@sortablelink('type', 'Merchant')</th>
                        <th data-breakpoints="xs sm">@sortablelink('transaction_amount', 'Txn Amount')</th>
                        <th data-breakpoints="xs sm md">@sortablelink('myma_share', 'WLC Share')</th>
                        <th data-breakpoints="xs sm md">@sortablelink('myma_share', 'Naanstap Share')</th>
                        <th data-breakpoints="xs sm md">@sortablelink('other_share', 'Merchant Share')</th>
                        <th data-breakpoints="xs sm md">@sortablelink('flexm_part', 'Flexm Share')</th>
                        <th data-breakpoints="xs sm md">Delivery Charges</th>
                        <th data-breakpoints="xs sm md">Discount</th>
                        <th class="no-sort">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $key => $item)
                    <tr>
                        <!-- <td>{{ ++$key }}</td> -->
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->transaction_ref_no }}</td>
                        <td>{{ $item->transaction_status }}</td>
                        <td>
                            @if(isset($item->user) && $item->user->name)
                            <a type="button" user_id="{{ $item->user_id }}" data-toggle="modal" data-target="#userModal">
                                {{ $item->user->name or '--'}}
                            </a>
                            @else
                            --
                            @endif
                        </td>
                        <td>{{ $item->merchant_name or '-' }}</td>
                        <td>{{ $item->transaction_amount }}</td>
                        <td>{{ number_format($item->myma_share,4) }}</td>
                        <td>{{ number_format($item->naanstap_pay,4) }}</td>
                        <td>{{ $item->food_share }}</td>
                        <td>{{ $item->flexm_part+$item->myma_part }}</td>
                        <td>{{ $item->order->naanstap or 0 }}</td>
                        <td>{{ $item->order->discount or 0 }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.food.transaction.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            @include('partials.paging', $items)

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ static_file('js/plugins/bootbox.min.js') }}"></script>
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.input-daterange').datepicker({
            todayBtn: "linked",
            format: "yyyy-mm-dd"
        });


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
