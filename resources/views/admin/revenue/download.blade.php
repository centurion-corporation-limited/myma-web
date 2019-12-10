@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>DBS download report</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.payment.download') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}
              <input type="hidden" name="merchant_id" value="{{ Request::input('merchant_id') }}">
              <input type="hidden" name="start" value="{{ Request::input('start') }}">
              <input type="hidden" name="end" value="{{ Request::input('end') }}">
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="organization_id">Organization ID(Assigned by DBS)<span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="organization_id" name="organization_id" value="{{ old('organization_id', getOption('organization_id')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="organization_name">Organization Name <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="organization_name" name="organization_name" value="{{ old('organization_name', getOption('organization_name')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="product_type">Product type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('product_type', $product_type, '', ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="originating_account_no">Originating account no <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="originating_account_no" name="originating_account_no" value="{{ old('originating_account_no', getOption('originating_account_no')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="bank_charges">Bank Charges <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('bank_charges', $bank_charges, '', ['class' => 'form-control'])!!}
                </div>
              </div>
                
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="payment_purpose">Payment Purpose <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="payment_purpose" name="payment_purpose" value="{{ old('payment_purpose', getOption('payment_purpose')) }}" class="form-control" required="" maxlength=12>
                </div>
              </div>
              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Download</button>
                </div>
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
    $('[name=start_date]').datepicker({
       format:"yyyy-mm-dd",
       startDate: '1'
    });
});
</script>
@endsection
