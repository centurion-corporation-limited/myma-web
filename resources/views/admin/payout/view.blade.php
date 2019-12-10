@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">

                                  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                      <div class="panel cardbox">
                                          <div class="panel-body card-item panel-refresh">
                                              <div class="card-details">
                                                <h4>Total Payout Paid</h4>
                                                <span>{{-- @$data['total_users_text'] --}}</span>
                                              </div>
                                              <div class="timer" data-to="{{ $data['total'] or 0 }}" data-speed="1500">${{ number_format($data['total'],4) }}</div>
                                              <div class="pull-right"><a href="{{ route('admin.payout.list', ['type' => 'all', 'merchant_id' => $merchant_id?$merchant_id:Request::input('merchant_id')]) }}">View all</a></div>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                      <div class="panel cardbox">
                                          <div class="panel-body card-item panel-refresh">
                                              <div class="card-details">
                                                @if(@$passed)
                                                  <h4>Payout Date Was</h4>
                                                @else
                                                  <h4>Next Payout Date</h4>
                                                @endif
                                                <span>{{-- @$data['total_users_text'] --}}</span>
                                              </div>
                                              <div class="timer" data-to="{{ $data['next_payout_date'] or '-' }}" data-speed="1500">{{ $data['next_payout_date'] or '-' }}</div>
                                              @if($auth_user->hasRole('food-admin'))
                                              <div class="pull-right"><a href="{{ route('admin.payout.transactions.food', ['type' => 'current', 'merchant_id' => $merchant_id?$merchant_id:Request::input('merchant_id')]) }}">View transactions</a></div>
                                              @else
                                              <div class="pull-right"><a href="{{ route('admin.payout.transactions', ['type' => 'current', 'merchant_id' => $merchant_id?$merchant_id:Request::input('merchant_id')]) }}">View transactions</a></div>
                                              @endif
                                          </div>
                                      </div>
                                  </div>

                                  <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                      <div class="panel cardbox">
                                          <div class="panel-body card-item panel-refresh">
                                              <div class="card-details">
                                                <h4>Pending Payout Amount</h4>
                                                <span>{{-- @$data['total_users_text'] --}}</span>
                                              </div>
                                              <div class="timer" data-to="{{ $data['last_amount'] or 0 }}" data-speed="1500">${{ number_format($data['last_amount'],4) }}</div>
                                               <div class="pull-right"><a href="{{ route('admin.payout.list', ['type' => 'pending', 'merchant_id' => $merchant_id?$merchant_id:Request::input('merchant_id')]) }}">View all</a></div>
                                          </div>
                                      </div>
                                  </div>
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Payout</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.payout.save') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left"class="colored-form">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Payout Amount</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    ${{ number_format($data['amount'],4) }}
                </div>
              </div>

              @if($data['last_payout_pending'])

                @foreach($data['last_payout_list'] as $row)
                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Previous Payout date was</label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $row->payout_date or '-' }}
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Previous Payout amount</label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                    ${{ number_format($row->net_payable,4) }}
                  </div>
                </div>
                @endforeach

                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Total Payout amount</label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                    ${{ number_format($total, 4) }}
                  </div>
                </div>
              @endif

              @if($auth_user->hasRole('admin') || $auth_user->hasRole('food-admin'))
              <div class="form-group en language row">
                <input type="hidden" name="merchant_id" value="{{ $merchant_id }}" class="form-control">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="txns_id">Transaction ID <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input @if(!$today) title="Can udpate only if its payout date or after." disabled @endif type="text" id="txns_id" name="transaction_id" value="{{ old('transaction_id') }}" class="form-control">
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="value_date">Value Date <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input @if(!$today) title="Can udpate only if its payout date or after." disabled @endif type="text" id="value_date" name="value_date" value="{{ old('value_date') }}" class="form-control">
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="remarks">Remarks <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input @if(!$today) disabled title="Can udpate only if its payout date or after." @endif type="text" id="remarks" name="remarks" value="{{ old('remarks') }}" class="form-control">
                </div>
              </div>
              @endif
              <div class="ln_solid"></div>
              <div class="form-group">
                @if($auth_user->hasRole('admin') || $auth_user->hasRole('food-admin'))
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <button @if(!$today) disabled title="Can udpate only if its payout date or after." @endif type="submit" class="btn btn-success">Submit</button>
                </div>
                @endif
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection

@section('scripts')
<script>
$(document).on('ready',function(){
    $('[name=value_date]').datepicker({
      format:"yyyy-mm-dd",
      startDate: '0'
    });
  });
</script>
@endsection
