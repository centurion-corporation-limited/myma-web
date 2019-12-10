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
                  <div class="col-md-6 text-right timer" data-to="{{ $data['myma_total'] or 0 }}" data-speed="1500">${{ number_format($data['myma_total'],4) }}</div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="panel cardbox">
            <div class="panel-body card-item panel-refresh">
                <div class="card-details">
                  <div class="col-md-6">Merchant Total</div>
                  <div class="col-md-6 text-right timer" data-to="{{ $data['merchant_total'] or 0 }}" data-speed="1500">${{ number_format($data['merchant_total'],4) }}</div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="panel cardbox">
            <div class="panel-body card-item panel-refresh">
                <div class="card-details">
                  <div class="col-md-6">Flexm Total</div>
                  <div class="col-md-6 text-right timer" data-to="{{ $data['flexm_total'] or 0 }}" data-speed="1500">${{ number_format($data['flexm_total'],4) }}</div>

                </div>
            </div>
        </div>
    </div>
  </div>

                <div class="x_panel">
                  <div class="x_title">
                    <h2>Transaction-Payout</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="form-outer">
                      <form action="{{ route('admin.payout.transactions') }}" method="GET" class="form-inline" style="width: calc(100% + 20px);">
                            <input type="hidden" name="payout_id" value="{{ Request::input('payout_id') }}" >
                            <input type="hidden" name="merchant_id" value="{{ Request::input('merchant_id') }}" >
                            <input type="hidden" name="type" value="{{ Request::input('type') }}">

                            <!-- <div class="row flex-row transaction-form">
                                <label class="control-label" for="dormitory_id">Date range </label>
                                <div class="input-group input-daterange">
                                    <input autocomplete="off" type="text" placeholder="From" class="form-control" name="start" value="{{ Request::input('start') }}">
                                    <div class="input-group-addon">to</div>
                                    <input autocomplete="off" type="text" placeholder="To" class="form-control" name="end" value="{{ Request::input('end') }}">
                                </div>
                            </div> -->

                            <div class="row flex-row transaction-form">
                                <label class="control-label" for="">Transaction ID</label>
                                <div class="input-group">
                                    <input type="text" placeholder="Transaction ID" name="transaction_id" value="{{ Request::input('transaction_id') }}" class="form-control input-small">
                                </div>
                            </div>

                            <div class="row flex-row transaction-form">
                                <label class="control-label" for="email">Email</label>
                                <div class="input-group">
                                    <input autocomplete="off" type="email" placeholder="Email" name="email" value="{{ Request::input('email') }}" class="form-control input-small">
                                </div>
                            </div>

                            <div class="row flex-row transaction-form">
                                <label class="control-label" for="status">Status</label>
                                <div class="input-group ">
                                    <select class="form-control" name="status">
                                        <option value="" @if(Request::input('status')=="" ) selected="selected" @endif>Select a status</option>
                                        @foreach($statuses as $name)
                                        <option value="{{ $name }}" @if(Request::input('status')==$name) selected="selected" @endif>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row flex-row button-tran">
                              <button class="btn btn-success" type="submit">Search</button>
                              <a href="{{ route('admin.payout.transactions', ['payout_id' => Request::input('payout_id'),'type' => Request::input('type'),'merchant_id' => Request::input('merchant_id')]) }}" class="btn btn-success">Reset</a>
                            </div>
                      </form>
                      <form action="{{ route('admin.payout.transactions') }}" method="GET" class="form-inline button-tran-abs">
                          <input type="hidden" name="payout_id" value="{{ Request::input('payout_id') }}" >
                          <input type="hidden" name="merchant_id" value="{{ Request::input('merchant_id') }}" >
                          <input type="hidden" name="transaction_id" value="{{ Request::input('transaction_id') }}" >
                          <input type="hidden" name="type" value="{{ Request::input('type') }}" >
                          <input type="hidden" name="mid" value="{{ Request::input('mid') }}" >
                          <input type="hidden" name="start" value="{{ Request::input('start') }}" >
                          <input type="hidden" name="end" value="{{ Request::input('end') }}" >
                          <input type="hidden" name="export" value="true" >
                          <input type="hidden" name="email" value="{{ Request::input('email') }}" >
                          <button class="btn btn-success" type="submit">Export</button>
                      </form>
                    </div>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <!-- <th>ID</th> -->
                          <th>@sortablelink('created_at', 'Txn Date')</th>
                          <th>@sortablelink('transaction_ref_no', 'Txn ID')</th>
                          <th>@sortablelink('transaction_status', 'Status')</th>
                          <th>@sortablelink('user_id', 'User')</th>
                          <th>@sortablelink('type', 'Merchant')</th>
                          <th>@sortablelink('transaction_amount', 'Txn Amount')</th>
                          <th>@sortablelink('myma_share', 'Myma Share')</th>
                          <th>@sortablelink('other_share', 'Merchant Share')</th>
                          <th>@sortablelink('flexm_part', 'FlexM Cost')</th>
                          @if($auth_user->hasRole('admin'))
                          <th class="no-sort">Action</th>
                          @endif
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <!-- <td>{{ ++$key }}</td> -->
                          <td>{{ $item->created_at->format('d/m/Y h:i A') }}</td>
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
                          <td>{{ $item->merchant->merchant_name or '-' }}</td>
                          <td class="amount">${{ number_format($item->transaction_amount,4) }}</td>

                          @if(strtolower($item->type) == 'instore')
                              <td class="amount">${{ number_format($item->myma_part+$item->flexm_part,4) }}</td>
                              @if($item->other_share == 0)
                              <td class="amount">${{ number_format( ($item->transaction_amount-$item->myma_part-$item->flexm_part),4) }}</td>
                              @else
                              <td class="amount">${{ $item->other_share }}</td>
                              @endif
                            @else
                            <td class="amount">${{ number_format($item->myma_share+$item->myma_part+$item->flexm_part,4) }}</td>
                            <td class="amount">${{ $item->other_share }}</td>
                          @endif
                          <td class="amount">${{ number_format($item->flexm_part,4) }}</td>
                          @if($auth_user->hasRole('admin'))
                          <td>
                            @if($item->status != 'paid')
                                {{-- <a href="{{ route('admin.transaction.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a> --}}
                            @endif
                            <!-- <a href="{{ route('admin.dormitory.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-delete"><i class="fa fa-2x fa-trash-o"></i></a> -->
                            <a href="{{ route('admin.transaction.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>

                          </td>
                          @endif
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
$('body').on('click', '.post-delete', function (event) {
    event.preventDefault();

    var message = $(this).data('message'),
        url = $(this).attr('href');

    bootbox.dialog({
        message: message,
        buttons: {
            danger: {
                label: "Yes",
                //className: "red",
                callback: function () {
                    $.ajax({
                        url: url,
                      //  type: 'delete',
                        //container: '#pjax-container'
                    }).done(function(data){
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
