@extends('layouts.admin')
@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/3.1.6/footable.bootstrap.min.css" />

@endsection
@section('content')


<style>
.row.flex-row.button-tran {
	padding-right: calc(100% - 50%);
	width: 100%;
}
.button-tran-abs {
	width: calc(100% - 75.7%);
	right: 1%;
}
.button-tran-abs + .button-tran-abs {
	right: 26.1%;
}

@media only screen and (max-width: 991px){
  .button-tran-abs {
  	bottom: 0px;
  }
}
</style>

<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Payment Report</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="form-outer">
                      <form action="{{ route('admin.payment.report') }}" method="GET" class="form-inline">
                            <div class="row flex-row transaction-form">
                                <label class="control-label" for="dormitory_id">Date range </label>
                                <div class="input-group input-daterange">
                                    <input type="text" autocomplete="off" placeholder="From" class="form-control" name="start" value="{{ Request::input('start') }}">
                                    <div class="input-group-addon">to</div>
                                    <input type="text" autocomplete="off" placeholder="To" class="form-control" name="end" value="{{ Request::input('end') }}">
                                </div>
                            </div>

                            <div class="row flex-row transaction-form">
                                <label class="control-label" for="mid">Merchant</label>
                                <div class="input-group">
                                    <select class="form-control" name="merchant_id">
                                        <option value="" @if(Request::input('merchant_id') == "") selected="selected" @endif>Select a merchant</option>
                                        @foreach($merchants as $mid => $name)
                                        <option value="{{ $mid }}" @if(Request::input('merchant_id') == $mid) selected="selected" @endif>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row flex-row transaction-form">
                                <label class="control-label chk-box" for="checked">
                                  <input type="checkbox" name="show_checked" @if(Request::input('show_checked') == 1) checked @endif value="1">
                                  View only checked values
                                </label>
																<label class="control-label chk-box" for="mid" style="margin-left:20px;">
                                  <input type="checkbox" name="show_non_checked" @if(Request::input('show_non_checked') == 1) checked @endif value="1">
                                  View only non-checked values
                                </label>
                            </div>

														<!-- <div class="row flex-row transaction-form">

                            </div> -->

                            <div class="row flex-row transaction-form">
                              <label style="height: 38px;">

                              </label>
                            </div>

                            <div class="row flex-row  button-tran" style="float:left;">
                                <button class="btn btn-success" type="submit">Search</button>
                                <a href="{{ route('admin.payment.report', ['payout_id' => Request::input('payout_id'), 'type' => Request::input('type')]) }}" class="btn btn-success">Reset</a>
                            </div>
                      </form>

                      <form action="{{ route('admin.payment.export') }}" method="GET" class="form-inline button-tran-abs">
                          <input type="hidden" name="merchant_id" value="{{ Request::input('merchant_id') }}">
                          <input type="hidden" name="start" value="{{ Request::input('start') }}">
                          <input type="hidden" name="end" value="{{ Request::input('end') }}">
													<input type="hidden" name="show_checked" value="{{ Request::input('show_checked') }}">
                          <input type="hidden" name="show_non_checked" value="{{ Request::input('show_non_checked') }}">
                          <button class="btn btn-success" type="submit">Export</button>
                      </form>
                      <form action="{{ route('admin.payment.download') }}" method="GET" class="form-inline button-tran-abs">
                          <input type="hidden" name="merchant_id" value="{{ Request::input('merchant_id') }}">
                          <input type="hidden" name="start" value="{{ Request::input('start') }}">
													<input type="hidden" name="end" value="{{ Request::input('end') }}">
													<input type="hidden" name="show_checked" value="{{ Request::input('show_checked') }}">
                          <input type="hidden" name="show_non_checked" value="{{ Request::input('show_non_checked') }}">
                          <button class="btn btn-success" type="submit">Generate DBS CSV</button>
                      </form>
										</div>
										<div class="col-md-3" style="padding: 0 5px;">
											<form action="{{ route('admin.payment.paid') }}" method="GET" class="form-inline">
                          <input type="hidden" name="merchant_id" value="{{ Request::input('merchant_id') }}">
                          <input type="hidden" name="start" value="{{ Request::input('start') }}">
													<input type="hidden" name="end" value="{{ Request::input('end') }}">
													<button class="btn btn-success" type="submit" style="width:100%;padding:10px;margin:0;">View Paid Reports</button>
                      </form>
										</div>
                    <div class="reponsive-table">
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>Vendor name</th>
                          <th>@sortablelink('payout_date', 'Payment due date')</th>
                          <th>Vendor transacted date</th>
                          <th>Vendor product type</th>
                          <th>@sortablelink('quantity', 'Vendor sales quantity')</th>
                          <th>Vendor Sales Amount</th>
                          <th>Wallet received amount</th>
                          <th>Myma Comms share</th>
                          <th>Myma Wallet Txn fee earned</th>
                          <th>Flexm cost</th>
                          <th>GST</th>
                          <th>Net payable to vendor</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ $item->merchant->merchant_name or '-' }}</td>
                          <td>{{ $item->payout_date }}</td>
                          <td>{{ $item->start_date }}</td>
                          <td>{{ $item->merchant->product_type or '-'}}</td>
                          <td>{{ $item->quantity }}</td>
                          <td class="amount">${{ number_format($item->amount,4) }}</td>
                          <td class="amount">${{ number_format($item->wallet_received_amount,4) }}</td>
                          <td class="amount">${{ number_format($item->revenue_deducted,4) }}</td>
                          <td class="amount">${{ number_format($item->txn_fee,4) }}</td>
                          <td class="amount">${{ number_format($item->cost_charged,4) }}</td>
                          <td class="amount">${{ number_format($item->gst,4) }}</td>
                          <td class="amount">${{ number_format($item->net_payable,4) }}</td>
                          <td>
                            @if($item->status != 'paid')
                                {{-- <a href="{{ route('admin.transaction.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a> --}}
                            @endif
                            {{-- <a href="{{ route('admin.payout.transactions', ['payout_id' => encrypt($item->id)]) }}"><i class="fa fa-2x fa-eye"></i></a> --}}
                            <input type="checkbox" class="verify_click" @if($item->verified) checked @endif data-id="{{ encrypt($item->id) }}">
                          </td>
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
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/3.1.6/footable.min.js"></script>
<script src="{{ static_file('js/plugins/bootbox.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('[name=show_checked]').on('change', function(){
			$('[name=show_non_checked]').prop('checked', false);
			$(this).closest('form').submit();
    });

		$('[name=show_non_checked]').on('change', function(){
			$('[name=show_checked]').prop('checked', false);
			$(this).closest('form').submit();
    });
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
});
$(document).on('click', '.verify_click', function (event) {
        var item_id = $(this).attr('data-id');
        var formData = new FormData();
				var ref = $(this);
 				formData.append('item_id', item_id);
        // formData.append('csrf_token', '{{ csrf_token() }}');
				// ref.attr('disabled', true);
        $.ajax({
            method:'post',
            url: '{{ route('ajax.payout.verify') }}',
            data: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            crossDomain: true,
            cache: false,
            contentType: false,
            processData: false,
            //beforeSend: function () {
                //    $(objct).val('Connecting...');
            //},
            error: function(xhr){
                console.log("Error");
                console.log(xhr);
            },
            success: function(data){
                console.log(data);
                var obj = data;
                if (obj.status) {
								// 		ref.attr('disabled', true);
                    // location.reload();
                }else if (obj.status == false) {
								// 		ref.attr('disabled', true);
										alert(obj.message);
                }
            }
        });
});
</script>
@endsection
