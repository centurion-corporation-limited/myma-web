@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/datetimepicker/build/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">

@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Restaurant</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.restaurant.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_id">Role <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('role_id', $roles, $item->role_id, ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Name <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="name" name="user_name" value="{{ old('user_name', $item->merchant?$item->merchant->name:'') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Restaurant Name <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="email">Email <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="email" disabled name="email" value="{{ old('email', $item->merchant?$item->merchant->email:'') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="password">Password</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="password" id="password" name="password" value="" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="gst_no">GST No <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="gst_no" name="gst_no" value="{{ old('gst_no', $item->gst_no) }}" class="form-control">
                </div>
              </div>
            
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nea_number">Nea License <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="nea_number" name="nea_number" value="{{ old('nea_number', $item->nea_number) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="bank_name">Bank Name <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $item->bank_name) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="bank_number">Bank Account Number <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="bank_number" name="bank_number" value="{{ old('bank_number', $item->bank_number) }}" class="form-control">
                </div>
              </div>
              
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="phone_no">Phone No <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="phone_no" name="phone_no" value="{{ old('phone_no', $item->phone_no) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="address">Address <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="address" name="address" class="form-control">{{ old('address', $item->address) }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="latitude">Latitude <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $item->latitude) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="latitude">Longitude <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $item->longitude) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="open_at">Opens At <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="open_at" name="open_at" value="{{ old('open_at', $item->open_at) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="closes_at">Closes At <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="closes_at" name="closes_at" value="{{ old('closes_at', $item->closes_at) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_id">Blocked <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <select class="form-control" name="blocked">
                      <option @if(old('blocked', $item->blocked) == 1) selected @endif value="1">Yes</option>
                      <option @if(old('blocked', $item->blocked) == 0) selected @endif value="0">No</option>
                    </select>
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
<script src="{{  static_file('js/plugins/datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>

<script>
$(document).on('ready',function(){

    $('#open_at').datetimepicker({
        format: 'LT'
    });
    $('#closes_at').datetimepicker({
        format: 'LT'
    });


});

</script>
@endsection
