@extends('layouts.admin')

@section('styles')
@endsection

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Share</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.share.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_name">Merchant <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" disabled id="merchant_name" placeholder="merchant_name" value="{{ old('merchant_name', $item->user?$item->user->name:'') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Merchant Category<span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('merchant_category_code', $category_code, $item->merchant_category_code, ['disabled' => 'disabled', 'class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_share">WLC Profit Share (%)<span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="merchant_share" name="merchant_share" value="{{ old('merchant_share', $item->merchant_share) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="myma_transaction_share">Myma Transaction charges(%)<span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="myma_transaction_share" name="myma_transaction_share" value="{{ old('myma_transaction_share', $item->myma_transaction_share) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="frequency">Frequency <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <select name="frequency" required class="form-control">
                      <option value="" @if(old('frequency', $item->frequency) == '') selected @endif>Select an option</option>
                      <option value="1" @if(old('frequency', $item->frequency) == '1') selected @endif>T+1</option>
                      <option value="2" @if(old('frequency', $item->frequency) == '2') selected @endif>T+2</option>
                      <option value="3" @if(old('frequency', $item->frequency) == '3') selected @endif>T+3</option>
                      <option value="4" @if(old('frequency', $item->frequency) == '4') selected @endif>T+4</option>
                      <option value="5" @if(old('frequency', $item->frequency) == '5') selected @endif>T+5</option>
                      <option value="6" @if(old('frequency', $item->frequency) == '6') selected @endif>T+6</option>
                      <option value="7" @if(old('frequency', $item->frequency) == '7') selected @endif>T+7</option>
                      <option value="15" @if(old('frequency', $item->frequency) == '15') selected @endif>T+15</option>
                      <option value="30" @if(old('frequency', $item->frequency) == '30') selected @endif>T+30</option>
                      <option value="60" @if(old('frequency', $item->frequency) == '60') selected @endif>T+60</option>
                      <option value="90" @if(old('frequency', $item->frequency) == '90') selected @endif>T+90</option>
                    </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="product_type">Product Type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="product_type" name="product_type" value="{{ old('product_type', $item->product_type) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Revenue Model <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="revenue_model" name="revenue_model" value="{{ old('revenue_model', $item->revenue_model) }}" class="form-control">
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
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_address_1">Merchant Address1 <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="merchant_address_1" name="merchant_address_1" value="{{ old('merchant_address_1', $item->merchant_address_1) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_address_2">Merchant Address 2</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="merchant_address_2" name="merchant_address_2" value="{{ old('merchant_address_2', $item->merchant_address_2) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_address_3">Merchant Address 3</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="merchant_address_3" name="merchant_address_3" value="{{ old('merchant_address_3', $item->merchant_address_3) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="bank_name">Bank Name <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $item->bank_name) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="bank_address">Bank Address <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="bank_address" name="bank_address" value="{{ old('bank_address', $item->bank_address) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="bank_country">Bank Country <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="bank_country" name="bank_country" minlength="2" maxlength="2" value="{{ old('bank_country', $item->bank_country) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="account_number">Bank Account Number <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="account_number" name="account_number" value="{{ old('account_number', $item->account_number) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="swift_code">Bank Swift BIC</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="swift_code" name="swift_code" value="{{ old('swift_code', $item->swift_code) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="routing_code">Rounting code</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="routing_code" name="routing_code" value="{{ old('routing_code', $item->routing_code) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="routing_code">Have GST no? <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <label><input type="radio" @if(old("gst", $item->gst) == "1") checked @endif name="gst" value="1" class=" ">Yes</label>
                    <label><input type="radio" @if(old("gst", $item->gst) == "0") checked @endif name="gst" value="0" class=" ">No</label>
                </div>
              </div>

              <div class="form-group gst_div @if($item->gst == 0) hide @endif">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="gst_number">GST Number <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="gst_number" name="gst_number" value="{{ old('gst_number', $item->gst_number) }}" class="form-control">
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

    $('[name=gst]').on('change', function(){
      var val = $(this).val();

      if(val == 1){
        $('.gst_div').removeClass('hide');
      }else{
        $('.gst_div').addClass('hide');
        $('[name=gst_number]').val('');
      }

    });

    //$('[name=gst]').trigger('change');
});
</script>
@endsection
