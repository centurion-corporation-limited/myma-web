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
            <form id="demo-form2" action="{{ route('admin.share.food.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_name">Merchant <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" disabled id="merchant_name" placeholder="merchant_name" value="{{ old('merchant_name', $item->user?$item->user->name:'') }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="naanstap_share">Naanstap Share (@if($catering)$@else%@endif)<span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="naanstap_share" name="naanstap_share" value="{{ old('naanstap_share', $item->naanstap_share) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              @if($catering)
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="sub_limit">Subscription limit after which subscription will be chargeable<span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="hidden" name="catering" value="1" >
                    <input type="text" id="sub_limit" name="sub_limit" value="{{ old('sub_limit', $item->sub_limit) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="per_sub_price">Per subscription price after limit<span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="per_sub_price" name="per_sub_price" value="{{ old('per_sub_price', $item->per_sub_price) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              @endif
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="bank_name">Bank Name </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $item->bank_name) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="account_number">Account Number </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="account_number" name="account_number" value="{{ old('account_number', $item->account_number) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="start_date">Start date<span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input autocomplete="off" type="text" id="start_date" @if($item->start_date != '') readonly @endif name="start_date" value="{{ old('start_date', $item->start_date) }}" class="form-control">
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

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-3 col-sm-3 col-xs-3 col-md-offset-2">
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
    $('[name=start_date]').datepicker({
       format:"yyyy-mm-dd",
       startDate: '1'
    });
});
</script>
@endsection
