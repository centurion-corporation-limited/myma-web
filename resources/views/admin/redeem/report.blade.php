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
            <h2>Redeem Report</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="form-outer">

                <form action="{{ route('admin.redeem.report') }}" method="GET" class="form-inline">

						<div class="row flex-row transaction-form">
                            <label class="control-label" for="dormitory_id">Mobile No</label>
                            <div class="input-group">
                            <input type="text" placeholder="Mobile No" name="phone" value="{{ Request::input('phone') }}" class="form-control">
                            </div>
                        </div>
						<div class="row flex-row transaction-form">
                            <label class="control-label" for="dormitory_id">FIN/ID no#</label>
                            <div class="input-group">
                            <input type="text" placeholder="FIN/ID no#" name="fin_no" value="{{ Request::input('fin_no') }}" class="form-control">
                            </div>
                        </div>
                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="dormitory_id">Redeem Clicked Date Range</label>
                            <div class="input-group input-daterange">
                                <input type="text" autocomplete="off" placeholder="From" class="form-control" name="start" value="{{ Request::input('start') }}">
                                <div class="input-group-addon">to</div>
                                <input type="text" autocomplete="off" placeholder="To" class="form-control" name="end" value="{{ Request::input('end') }}">
                            </div>
                        </div>
                       <div class="row flex-row transaction-form">
                            <label class="control-label" for="type">Redeem TOUCH-CoH</label>
                            <div class="input-group">
                                <select class="form-control" name="type">
                                    <option value="" @if(Request::input('type')=="" ) selected="selected" @endif>Select a TOUCH-CoH</option>
                                    <option value="touch" @if(Request::input('type')=='touch') selected="selected" @endif>TOUCH</option>
                                    <option value="spuul" @if(Request::input('type')=='spuul') selected="selected" @endif>Spuul</option>
                                    <option value="mastercard" @if(Request::input('type')=='mastercard') selected="selected" @endif>Mastercard</option>
                                    <option value="starhub" @if(Request::input('type')=='starhub') selected="selected" @endif>Starhub</option>
                                </select>
                            </div> 
                        </div>
                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="dormitory_id">Credited Date Range</label>
                            <div class="input-group input-daterange">
                                <input type="text" autocomplete="off" placeholder="From" class="form-control" name="credited_start" value="{{ Request::input('credited_start') }}">
                                <div class="input-group-addon">to</div>
                                <input type="text" autocomplete="off" placeholder="To" class="form-control" name="credited_end" value="{{ Request::input('credited_end') }}">
                            </div>
                        </div> 

                       <div class="row flex-row transaction-form">
                            <label class="control-label" for="status">Transaction Status</label>
                            <div class="input-group">
                                <select class="form-control" name="status">
                                    <option value="" @if(Request::input('status')=="" ) selected="selected" @endif>Select a Transaction Status</option>
                                    <option value="redeem_successful" @if(Request::input('status')=='redeem_successful') selected="selected" @endif>Redeem Successful</option>
                                    <option value="credit_successful" @if(Request::input('status')=='credit_successful') selected="selected" @endif>Credit Successful</option>
                                </select>
                            </div> 
                        </div> 
                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="status">Dormitory Address</label>
                            <div class="input-group">
                                {!!Form::select('dormitory_id', $dorm, Request::input('dormitory_id'), ['class' => 'form-control'])!!}
                            </div>
                        </div>

                        <div class="row flex-row  button-tran">
                            <button class="btn btn-success" type="submit">Search</button>
                            <a href="{{ route('admin.redeem.report') }}" class="btn btn-success">Reset</a>
                        </div> 

                    </form>
                    <form action="{{ route('admin.redeem.download') }}" method="GET" class="form-inline button-tran-abs">
                        <input type="hidden" name="type" value="{{ Request::input('type') }}">
                        <input type="hidden" name="status" value="{{ Request::input('status') }}">
                        <input type="hidden" name="start" value="{{ Request::input('start') }}">
                        <input type="hidden" name="end" value="{{ Request::input('end') }}">
                        <input type="hidden" name="phone" value="{{ Request::input('phone') }}">
                        <input type="hidden" name="fin_no" value="{{ Request::input('fin_no') }}">
                        <input type="hidden" name="credited_start" value="{{ Request::input('credited_start') }}">
                        <input type="hidden" name="credited_end" value="{{ Request::input('credited_end') }}">
                          <input type="hidden" name="dormitory_id" value="{{ Request::input('dormitory_id') }}" >
                        <button class="btn btn-success" type="submit">Export</button>
                    </form>
            </div>
            <div class="reponsive-table">
            <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <!-- <th>User Id</th> -->
                        <!-- <th>ih User Id</th> -->
                        <th>@sortablelink('name', 'Name')</th>
                        <th>@sortablelink('mobile','Mobile')</th>
                        <th>@sortablelink('fin_no','FIN/ID no#')</th>
                        <th>@sortablelink('type','TOUCH-CoH')</th>
                        <th>@sortablelink('click_redeem','Click Redeem')</th>
                        <th>@sortablelink('click_date','Redeem Clicked Date')</th>
                        <th>@sortablelink('wallet_credited_at','Credited Date')</th>
                        <th>@sortablelink('credit_amount  ','Amount')</th>
                        <th>@sortablelink('status','Transaction Status')</th>
                        <!-- <th>@sortablelink('created_at', 'Redeemed on')</th> -->
                        <!-- <th>@sortablelink('updated_at', 'Updated Date')</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $key => $value)
                    <tr>
                        <td>{{ $value->id }}</td>
                        <!-- <td>{{ $value->ih_user_id }}</td> -->
                        <td>{{ $value->name }}</td>
                        <td>{{ $value->mobile }}</td>
                        <td>{{ $value->fin_no }}</td>
                        <td>{{ $value->type }}</td>
                        <td>{{ $value->click_redeem }}</td>
                        <td>{{ date('d-m-Y H:i:s', strtotime($value->click_date)) }}</td>
                        <td>@if($value->wallet_credited_at){{ date('d-m-Y H:i:s', strtotime($value->wallet_credited_at)) }} @endif</td>
                        <td>{{ $value->credit_amount }}</td>
                        <td>@if($value->status == 'redeem_successful') Redeem Successful @else Credit Successful @endif </td>
                        <!-- <td>{{ date('d-m-Y h:i:s', strtotime($value->created_at)) }}</td> -->
                        <!-- <td>{{ date('d-m-Y', strtotime($value->updated_at)) }}</td> -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
          </div>
          @include('partials.paging', $items)
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
</script>
@stop
