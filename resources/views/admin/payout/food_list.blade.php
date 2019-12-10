@extends('layouts.admin')

@section('styles')
<style>
form{display: inline-block;}
</style>
@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
  <div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="panel cardbox">
            <div class="panel-body card-item panel-refresh">
                <div class="card-details">
                  <div class="col-md-6">Naanstap Total</div>
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
    <!-- <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="panel cardbox">
            <div class="panel-body card-item panel-refresh">
                <div class="card-details">
                  <div class="col-md-6">Flexm Total</div>
                  <div class="col-md-6 text-right timer" data-to="{{ $data['flexm_total'] or 0 }}" data-speed="1500">{{ $data['flexm_total'] or 0 }}</div>

                </div>
            </div>
        </div>
    </div> -->
  </div>

                <div class="x_panel">
                  <div class="x_title">
                    <h2>Transactions</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                      <form action="{{ route('admin.payout.transactions') }}" method="GET" class="form-inline">
                            <input type="hidden" name="payout_id" value="{{ Request::input('payout_id') }}" >
                            <input type="hidden" name="merchant_id" value="{{ Request::input('merchant_id') }}" >
                            <input type="hidden" name="type" value="{{ Request::input('type') }}">
                            <div class="form-group">
                                <input type="text" placeholder="Transaction ID" name="transaction_id" value="{{ Request::input('transaction_id') }}" class="form-control input-small">
                            </div>

                            <button class="btn btn-success" type="submit">Search</button>
                            <a href="{{ route('admin.payout.transactions', ['payout_id' => Request::input('payout_id'),'type' => Request::input('type'),'merchant_id' => Request::input('merchant_id')]) }}" class="btn btn-success">Reset</a>
                      </form>
                      <form action="{{ route('admin.payout.transactions') }}" method="GET" class="form-inline">
                          <input type="hidden" name="payout_id" value="{{ Request::input('payout_id') }}" >
                          <input type="hidden" name="merchant_id" value="{{ Request::input('merchant_id') }}" >
                          <input type="hidden" name="transaction_id" value="{{ Request::input('transaction_id') }}" >
                          <input type="hidden" name="type" value="{{ Request::input('type') }}" >
                          <input type="hidden" name="mid" value="{{ Request::input('mid') }}" >
                          <input type="hidden" name="start" value="{{ Request::input('start') }}" >
                          <input type="hidden" name="end" value="{{ Request::input('end') }}" >
                          <input type="hidden" name="export" value="true" >
                          <!-- <input type="hidden" name="type" value="payout" > -->
                          <button class="btn btn-success" type="submit" style="margin-bottom:24px !important;">Export</button>
                      </form>
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
                          <th>@sortablelink('myma_share', 'WLC Share')</th>
                          <th>@sortablelink('other_share', 'Naanstap Share')</th>
                          <th>@sortablelink('food_share', 'Merchant Share')</th>
                          @if($auth_user->hasRole('admin'))
                          <th class="no-sort">Action</th>
                          @endif
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
                          <td>{{ $item->merchant->merchant_name or '-' }}</td>
                          <td>{{ $item->transaction_amount }}</td>
                          <td>{{ $item->myma_share }}</td>
                          <td>{{ $item->naanstap_pay }}</td>
                          <td>{{ $item->food_share }}</td>
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
<script>
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
