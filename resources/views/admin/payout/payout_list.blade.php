@extends('layouts.admin')
@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/3.1.6/footable.bootstrap.min.css" />
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<style>
.button-tran-abs {
    bottom: 0;
}
th.amount {
    text-align: right;
}
</style>
@endsection
@section('content')

<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>List Payouts</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="form-outer">
                      <form action="{{ route('admin.payout.list') }}" method="GET" class="form-inline">
                            <input type="hidden" name="type" value="{{ Request::input('type') }}" >
                            <input type="hidden" name="merchant_id" value="{{ Request::input('merchant_id') }}" >
                            <div class="row flex-row transaction-form">
                                <label class="control-label" for="dormitory_id">Date range </label>
                                <div class="input-group input-daterange">
                                    <input autocomplete="off" type="text" placeholder="From" class="form-control" name="start" value="{{ Request::input('start') }}">
                                    <div class="input-group-addon">to</div>
                                    <input autocomplete="off" type="text" placeholder="To" class="form-control" name="end" value="{{ Request::input('end') }}">
                                </div>
                            </div>

                            <div class="row flex-row transaction-form">
                                <label class="control-label" for="">Transaction ID</label>
                                <div class="input-group">
                                    <input type="text" placeholder="Transaction ID" name="transaction_id" value="{{ Request::input('transaction_id') }}" class="form-control input-small">
                                </div>
                            </div>

                            <div class="row flex-row button-tran">
                              <button class="btn btn-success" type="submit">Search</button>
                              <a href="{{ route('admin.payout.list', ['payout_id' => Request::input('payout_id'), 'merchant_id' => Request::input('merchant_id'), 'type' => Request::input('type')]) }}" class="btn btn-success">Reset</a>
                            </div>
                      </form>
                    </div>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>@sortablelink('id')</th>
                          <th>@sortablelink('payout_date', 'Payout Date')</th>
                          <th class="amount">@sortablelink('amount')</th>
                          <th data-breakpoints="xs sm" class="no-sort">Merchant Name</th>
                          <th class="no-sort">Transaction ID</th>
                          <th data-breakpoints="xs sm md">@sortablelink('value_date', 'Value Date')</th>
                          <th data-breakpoints="xs sm md" class="no-sort">Remarks</th>
                          <th data-breakpoints="xs sm" class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->payout_date }}</td>
                          <td class="amount">${{ number_format($item->net_payable,4) }}</td>
                          <td>{{ $item->merchant->merchant_name or '--' }}</td>
                          <td>{{ $item->transaction_id }}</td>
                          <td>{{ $item->value_date }}</td>
                          <td>{{ $item->remarks }}</td>

                          <td>
                            @if($item->status != 'paid')
                                {{-- <a href="{{ route('admin.transaction.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a> --}}
                            @endif
                            <!-- <a href="{{ route('admin.dormitory.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-delete"><i class="fa fa-2x fa-trash-o"></i></a> -->
                            <a href="{{ route('admin.payout.transactions', ['payout_id' => encrypt($item->id)]) }}"><i class="fa fa-2x fa-eye"></i></a>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/3.1.6/footable.min.js"></script>
<script src="{{ static_file('js/plugins/bootbox.min.js') }}"></script>
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.input-daterange').datepicker({
            todayBtn: "linked",
            format: "yyyy-mm-dd"
        });


    });
$(document).ready(function() {
    if($('.foo_table').length){
        $('.foo_table').footable();
    }
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
