@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/3.1.6/footable.bootstrap.min.css" />
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
                  <div class="col-md-6">Transaction Total</div>
                  <div class="col-md-6 text-right timer" data-to="{{ $data['total'] or 0 }}" data-speed="1500">${{ number_format($data['total'],4) }}</div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="panel cardbox">
            <div class="panel-body card-item panel-refresh">
                <div class="card-details">
                  <div class="col-md-6">Total Success</div>
                  <div class="col-md-6 text-right timer" data-to="{{ $data['total_success'] or 0 }}" data-speed="1500">{{  $data['total_success']  }}</div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="panel cardbox">
            <div class="panel-body card-item panel-refresh">
                <div class="card-details">
                  <div class="col-md-6">Total Not confirmed</div>
                  <div class="col-md-6 text-right timer" data-to="{{ $data['total_not_confirmed'] or 0 }}" data-speed="1500">{{  $data['total_not_confirmed']  }}</div>

                </div>
            </div>
        </div>
    </div>
  </div>

                <div class="x_panel">
                  <div class="x_title">
                    <h2>Transactions-List Remittance</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="form-outer">
                      <form action="{{ route('admin.transactions.remit') }}" method="GET" class="form-inline">
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

                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="status">Status</label>
                            <div class="input-group ">
                                <select class="form-control" name="status">
                                    <option value="" @if(Request::input('status')=="" ) selected="selected" @endif>Select a status</option>
                                    @foreach($statuses as $id => $name)
                                    <option value="{{ $id }}" @if(Request::input('status') == $id) selected="selected" @endif>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="email">Email</label>
                            <div class="input-group">
                                <input autocomplete="off" type="email" placeholder="Email" name="email" value="{{ Request::input('email') }}" class="form-control input-small">
                            </div>
                        </div>

                        <div class="row flex-row button-tran">
                            <button class="btn btn-success" type="submit">Search</button>
                            <a href="{{ route('admin.transactions.remit') }}" class="btn btn-success">Reset</a>
                        </div>
                      </form>
                      <form action="{{ route('admin.transactions.export') }}" method="GET" class="form-inline button-tran-abs">
                          <input type="hidden" name="transaction_id" value="{{ Request::input('transaction_id') }}" >
                          <input type="hidden" name="type" value="{{ $type or 'inapp' }}" >
                          <input type="hidden" name="start" value="{{ Request::input('start') }}">
                          <input type="hidden" name="end" value="{{ Request::input('end') }}">
                          <input type="hidden" name="status" value="{{ Request::input('status') }}">
                          <input type="hidden" name="email" value="{{ Request::input('email') }}" >
                          <button class="btn btn-success" type="submit">Export</button>
                      </form>
                    </div>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="no-sort">ID</th>
                          <th>@sortablelink('user_id', 'User')</th>
                          <th>@sortablelink('hash_id', 'Transaction ID')</th>
                          <th>@sortablelink('total_transaction_amount', 'Transaction Amount')</th>
                          <th data-breakpoints="xs sm md">@sortablelink('created_at', 'Transaction Time')</th>
                          <th>@sortablelink('status', 'Status')</th>
                          <th data-breakpoints="xs sm" class="no-sort">MyMA Txn Cost</th>
                          <th data-breakpoints="xs sm" class="no-sort">FlexM Cost</th>
                          <th data-breakpoints="xs sm" class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>
                            @if(isset($item->user) && $item->user->name)
                            <a type="button" user_id="{{ $item->user_id }}" data-toggle="modal" data-target="#userModal">
                              {{ $item->user->name or '--'}}
                            </a>
                            @else
                            --
                            @endif
                          </td>
                          <td>{{ $item->hash_id }}</td>
                          <td class="amount">${{ number_format($item->total_transaction_amount,4) }}</td>
                          <td>{{ $item->created_at->format('d/m/Y h:i A') }}</td>
                          <td>
                            @if($item->status == 'require')
                            Not confirmed
                            @else
                            {{ ucfirst($item->status) }}
                            @endif
                          </td>
                          <td class="amount">${{ number_format($item->myma_share,4) }}</td>
                          <td class="amount">${{ number_format($item->flexm_part,4) }}</td>

                          <td>
                            @if($item->status != 'paid')
                                {{-- <a href="{{ route('admin.transaction.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a> --}}
                            @endif
                            <!-- <a href="{{ route('admin.dormitory.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-delete"><i class="fa fa-2x fa-trash-o"></i></a> -->
                            <a href="{{ route('admin.remit.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>

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
