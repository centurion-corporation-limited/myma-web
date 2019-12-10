@extends('layouts.admin')

@section('styles')
<link href="{{ static_file('js/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Add Coupon</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.coupon.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="code">Code <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="code" name="code" value="{{ old('code') }}" class="form-control">
                </div>
              </div>

              <!-- <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_bn">Restaurant Type <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                    <select class="form-control" name="restra_type">
                      <option @if(old('restra_type') == "") selected @endif value="">Please select restaurant type</option>
                      <option @if(old('restra_type') == "single") selected @endif value="single">Food Outlet</option>
                      <option @if(old('restra_type') == "package") selected @endif value="package">Catering</option>
                    </select>

                  </div>
              </div> -->

              <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_bn">Merchant <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                    <select class="form-control" name="merchant_id">
                      <option @if(old('merchant_id') == "") selected @endif value="">Select a merchant</option>
                      @foreach($merchants as $id => $name)
                      <option @if(old('merchant_id') == $id) selected @endif value="{{ $id }}">{{ $name }}</option>
                      @endforeach
                    </select>
                  </div>
              </div>

              <div class="form-group hide food_listing">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_bn">Food Items </label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                    <select class="form-control" id="item_ids" name="item_ids[]" multiple>
                      {{-- <option @if(old('merchant_id') == "") selected @endif value="">Select food items</option>
                      @foreach($items as $id => $name)
                      <option value="{{ $id }}">{{ $name }}</option>
                      @endforeach --}}
                    </select>
                  </div>
              </div>

              <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_bn">Type <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                    <select class="form-control" name="type">
                      <option @if(old('type') == "") selected @endif value="">Please select a type</option>
                      <option @if(old('type') == "direct") selected @endif value="direct">Cash Value</option>
                      <option @if(old('type') == "percent") selected @endif value="percent">Percentage</option>
                    </select>

                  </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="value">Discount value <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="number" id="value" name="value" value="{{ old('value') }}" class="form-control">
                </div>
              </div>

              <!-- <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_ta">Start</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="name_ta" name="name_ta" value="{{ old('name_ta') }}" class="form-control">
                </div>
              </div> -->

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="expiry">Expiry <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input autocomplete="off" type="text" id="expiry" name="expiry" value="{{ old('expiry') }}" class="form-control">
                </div>
              </div>

              {{-- <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Approved</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div id="gender" class="btn-group" data-toggle="buttons">
                    <label class="btn @if(old('approved') == '1') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="approved" @if(old('approved') == '1') checked @endif value="1"> &nbsp; Yes &nbsp;
                    </label>
                    <label class="btn @if(old('approved') == '0') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="approved" @if(old('approved') == '0') checked @endif value="0"> No
                    </label>
                  </div>
                </div>
            </div> --}}

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Add</button>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
@section('scripts')
<script src="{{ static_file('js/plugins/select2/js/select2.full.min.js') }}"></script>

<script>
$(document).on('ready',function(){

     $('[name=expiry]').datepicker({
        format:"dd/mm/yyyy",
        startDate: '1'
     });

     $('[name=approved]').on('change', function(){
         var obj = $(this);
         $('[name=approved]').closest('label').addClass('btn-default').removeClass('btn-primary');
         obj.closest('label').addClass('btn-primary').removeClass('btn-default');
     });

     $('[name=merchant_id]').on('change',function(){
         var merchant_id = $(this).val();

         if(merchant_id != ''){
           var formData = new FormData();
           formData.append('merchant_id', merchant_id);

           $.ajax({
               method:'post',
               url: '{{ route('ajax.food.listing') }}',
               data: formData,
               headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
               crossDomain: true,
               cache: false,
               contentType: false,
               processData: false,
               error: function(xhr){
                   console.log("Error");
                   console.log(xhr);
               },
               success: function(data){
                    if (data.status) {
                       $('.food_listing').removeClass('hide');
                       $('#item_ids').html(data.html);
                       $('#item_ids').select2({
                         placeholder: "Select food items",
                       });

                   }else if (data.status == false) {
                       alert(data.message);
                   }
               }
           });
         }else{
           // alert('Merchant Id is required');
         }
     });

     $('[name=merchant_id]').trigger('change');
});
</script>
@endsection
