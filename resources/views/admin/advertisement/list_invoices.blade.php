@extends('layouts.admin')

@section('styles')
<style>
.button-tran-abs {
    bottom: 0;
}
</style>

@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Invoices</h2>
                    <!-- <ul class="nav navbar-right panel_toolbox">
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul> -->
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <div class="form-outer">
                    <form action="{{ route('admin.invoice.list') }}" method="GET" class="form-inline">

                          <div class="row flex-row transaction-form">
                              <label class="control-label" for="">User</label>
                              <div class="input-group">
                                  {!!Form::select('user_id', $users, '', ['class' => 'form-control'])!!}
                              </div>
                          </div>

                          <div class="row flex-row transaction-form">
                              <label class="control-label" for="">Status</label>
                              <div class="input-group">
                                  {!!Form::select('status', ['Please select status', 'pending' => 'Pending', 'paid' => 'Paid'], '', ['class' => 'form-control'])!!}
                              </div>
                          </div>

                          <div class="row flex-row transaction-form">
                              <label class="control-label" for="">Type</label>
                              <div class="input-group">
                                  {!!Form::select('type', ['Please select type', 'date' => 'Date', 'impression' => 'Impression'], '', ['class' => 'form-control'])!!}
                              </div>
                          </div>

                          <div class="row flex-row transaction-form">
                              <label class="control-label" for="dormitory_id">Date range </label>
                              <div class="input-group input-daterange">
                                  <input autocomplete="off" type="text" placeholder="From" class="form-control" name="start" value="{{ Request::input('start') }}">
                                  <div class="input-group-addon">to</div>
                                  <input autocomplete="off" type="text" placeholder="To" class="form-control" name="end" value="{{ Request::input('end') }}">
                              </div>
                          </div>

                          <div class="row flex-row  button-tran">
                            <button class="btn btn-success" type="submit">Search</button>
                            <a href="{{ route('admin.invoice.list')}}" class="btn btn-success">Reset</a>
                          </div>
                    </form>
                    <form action="{{ route('admin.invoice.export') }}" method="GET" class="form-inline button-tran-abs">
                        <input type="hidden" name="user_id" value="{{ Request::input('user_id') }}">
                        <input type="hidden" name="type" value="{{ Request::input('type') }}">
                        <input type="hidden" name="status" value="{{ Request::input('status') }}">
                        <input type="hidden" name="start" value="{{ Request::input('start') }}">
                        <input type="hidden" name="end" value="{{ Request::input('end') }}">
                        <!-- <input type="hidden" name="email" value="{{ Request::input('email') }}"> -->
                        <button class="btn btn-success" type="submit">Export</button>
                    </form>
                  </div>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>S/No</th>
                          <th class="no-sort">User</th>
                          <th>@sortablelink('type', 'Ad Type')</th>
                          <th>@sortablelink('price', 'Price')</th>
                          <th>@sortablelink('status', 'Status')</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->user->name or '' }}</td>
                          <td>{{ ucfirst($item->type) }}</td>
                          <td class="amount">${{ number_format($item->price,2) }}</td>
                          <td>{{ $item->status }}</td>
                          <td>
                            @if($item->status == 'pending')
                             <a class="btn btn-success" invoice-id="{{ $item->id }}" data-toggle="modal" data-target="#paidModal" href="{{-- route('admin.invoice.paid', ['id' => $item->id, '_token' => csrf_token()]) --}}" data-message="Are you sure about updating the status?" >Paid?</a>
                            @endif
                            <a href="{{ route('admin.invoice.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
      </table>
      @include('partials.paging', $items)

    </div>
  </div>
</div>

<div id="paidModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Invoice Status</h4>
      </div>
      <form id="demo-form2" action="{{ route('admin.invoice.paid') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
            {{ csrf_field() }}
      <div class="modal-body">
            <input type="hidden" name="invoice_id" value="{{ old('invoice_id') }}" class="form-control col-md-7 col-xs-12">
            <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="customer_no">Customer No <span class="required">*</span></label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  <input type="text" required id="customer_no" name="customer_no" value="{{ old('customer_no') }}" class="form-control col-md-7 col-xs-12">
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="sales_person">Sales person <span class="required"></span></label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  <input type="text" id="sales_person" name="sales_person" value="{{ old('sales_person') }}" class="form-control col-md-7 col-xs-12">
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="payment_mode">Payment Mode <span class="required"></span></label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  <input type="text" id="payment_mode" name="payment_mode" value="{{ old('payment_mode') }}" class="form-control col-md-7 col-xs-12">
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="credit_term">Credit Term <span class="required"></span></label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  <input type="text" id="credit_term" name="credit_term" value="{{ old('credit_term') }}" class="form-control col-md-7 col-xs-12">
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="due_date">Due Date <span class="required"></span></label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  <input type="text" id="due_date" name="due_date" value="{{ old('due_date') }}" autocomplete="off" class="form-control col-md-7 col-xs-12">
                </div>
            </div>
            
            
              
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>

  </div>
</div>

@endsection

@section('scripts')
<script src="{{ static_file('js/plugins/bootbox.min.js') }}"></script>
<script>
$(document).ready(function() {
    
    $('#paidModal').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var invoice_id = button.attr('invoice-id')
        $('[name=invoice_id]').val(invoice_id);
    });
    
    $('#due_date').datepicker({
        format:"yyyy-mm-dd",
        //startDate: new Date()
    });
    
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
