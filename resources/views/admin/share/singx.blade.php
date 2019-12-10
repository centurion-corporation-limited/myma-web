@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Singx Share</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.share.flexm') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}
              <input type="hidden" name="merchant_type" value="singx">

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Profit share (%) <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input required type="text" id="title" name="options[singx_charges]" value="{{ old('options.singx_charges', getOption('singx_charges')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="frequency">Frequency  </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <select name="options[singx_frequency]"   class="form-control">
                      <option value="" @if(old('options[singx_frequency]', getOption('singx_frequency')) == '') selected @endif>Select an option</option>
                      <option value="1" @if(old('options[singx_frequency]', getOption('singx_frequency')) == '1') selected @endif>T+1</option>
                      <option value="2" @if(old('options[singx_frequency]', getOption('singx_frequency')) == '2') selected @endif>T+2</option>
                      <option value="3" @if(old('options[singx_frequency]', getOption('singx_frequency')) == '3') selected @endif>T+3</option>
                      <option value="4" @if(old('options[singx_frequency]', getOption('singx_frequency')) == '4') selected @endif>T+4</option>
                      <option value="5" @if(old('options[singx_frequency]', getOption('singx_frequency')) == '5') selected @endif>T+5</option>
                      <option value="6" @if(old('options[singx_frequency]', getOption('singx_frequency')) == '6') selected @endif>T+6</option>
                      <option value="7" @if(old('options[singx_frequency]', getOption('singx_frequency')) == '7') selected @endif>T+7</option>
                      <option value="15" @if(old('options[singx_frequency]', getOption('singx_frequency')) == '15') selected @endif>T+15</option>
                      <option value="30" @if(old('options[singx_frequency]', getOption('singx_frequency')) == '30') selected @endif>T+30</option>
                      <option value="60" @if(old('options[singx_frequency]', getOption('singx_frequency')) == '60') selected @endif>T+60</option>
                      <option value="90" @if(old('options[singx_frequency]', getOption('singx_frequency')) == '90') selected @endif>T+90</option>
                    </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="singx_product_type">Product Type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input required type="text" id="singx_product_type" name="options[singx_product_type]" value="{{ old('options.singx_product_type', getOption('singx_product_type')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Revenue Model <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input required type="text" id="title" name="options[singx_revenue_model]" value="{{ old('options.singx_revenue_model', getOption('singx_revenue_model')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Signup fee after 10k <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input required type="text" id="title" name="options[singx_signup_fee]" value="{{ old('options.singx_signup_fee', getOption('singx_signup_fee')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Report from vendor</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  -
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Report from system</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  -
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Tracking transaction system</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  -
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_address_1">Merchant Address1</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="merchant_address_1" name="options[singx_address_1]" value="{{ old('options.merchant_address_1', getOption('singx_address_1')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_address_2">Merchant Address 2</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="merchant_address_2" name="options[singx_address_2]" value="{{ old('options.merchant_address_2', getOption('singx_address_2')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_address_3">Merchant Address 3</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="merchant_address_3" name="options[singx_address_3]" value="{{ old('options.merchant_address_3', getOption('singx_address_3')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="bank_name">Bank Name</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="bank_name" name="options[singx_bank_name]" value="{{ old('options.singx_bank_name', getOption('singx_bank_name')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="bank_address">Bank Address </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="bank_address" name="options[singx_bank_address]" value="{{ old('options.singx_bank_address', getOption('singx_bank_address')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="bank_country">Bank Country </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="bank_country" name="options[singx_bank_country]" minlength="2" maxlength="2" value="{{ old('options.singx_bank_country', getOption('singx_bank_country')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="account_number">Bank Account Number </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="account_number" name="options[singx_account_number]" value="{{ old('options.singx_account_number', getOption('singx_account_number')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="swift_code">Bank Swift BIC</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="swift_code" name="options[singx_swift_code]" value="{{ old('options.singx_swift_code', getOption('singx_swift_code')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="routing_code">Rounting code</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="routing_code" name="options[singx_routing_code]" value="{{ old('options.singx_routing_code', getOption('singx_routing_code')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="singx_gst">Have GST no? <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <label><input required type="radio" @if(old("options.singx_gst", getOption('singx_gst')) == "1") checked @endif name="options[singx_gst]" value="1" class="singx_gst">Yes</label>
                    <label><input required type="radio" @if(old("options.singx_gst", getOption('singx_gst')) == "0") checked @endif name="options[singx_gst]" value="0" class="singx_gst">No</label>
                </div>
              </div>

              <div class="form-group gst_div @if(getOption('singx_gst') == 0) hide @endif">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="gst_number">GST Number <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="gst_number" name="options[singx_gst_number]" value="{{ old('options.singx_gst_number', getOption('singx_gst_number')) }}" class="form-control">
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Update</button>
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

    $('.singx_gst').on('change', function(){
      var val = $(this).val();

      if(val == 1){
        $('.gst_div').removeClass('hide');
        $('#gst_number').attr('required', true);

      }else{
        $('.gst_div').addClass('hide');
        $('#gst_number').val('');
        $('#gst_number').attr('required', false);
      }

    });

    //$('[name=gst]').trigger('change');
});
</script>
@endsection
