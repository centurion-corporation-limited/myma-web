@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<style>
.type_file{
    margin-top: 15px;margin-bottom: 10px;
}
.fake-file{width: 100%;}
.fancy-file{width: 100%;}
.form-group .fancy-file .btn{margin-bottom: initial;}
</style>
@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Add User</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.user.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="full-name">Role <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {!!Form::select('role', $roles, old('role'), ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group hide dorm">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="full-name">Dormitory
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {!!Form::select('dormitory_id', $dorm, old('dormitory_id'), ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="name">Name <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="email">Email <span class="app_user required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="email" name="email" value="{{ old('email') }}" class="form-control">
                </div>
              </div>

              <div class="form-group driver user_div hide">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="fin_no">Fin No <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="fin_no" name="fin_no" value="{{ old('fin_no') }}" class="form-control">
                </div>
              </div>

              <div class="form-group driver hide">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="type">Type <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="type" class="form-control" name="type">
                      <option value="free" @if(old('type') == 'free') selected="selected" @endif>Food outlet</option>
                      <option value="package" @if(old('type') == 'package') selected="selected" @endif>Catering</option>
                  </select>
                </div>
              </div>

              <div class="form-group driver hide">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="vehicle_no">Vehicle No <span class="driver hide required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="vehicle_no" name="vehicle_no" value="{{ old('vehicle_no') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="password">Password <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="password" id="password" name="password" value="" class="form-control"></br>
                  <i class="fa fa-info-circle"></i>Password should have at least 1 number, 1 lowercase letter, 1 uppercase letter, 1 special character and minimum length of 8.
                </div>
              </div>

              {{-- <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="emp_id">Language</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="language" class="form-control" name="language">
                      <option value="bengali" @if(old('language') == 'bengali') selected="selected" @endif>Bengali</option>
                      <option value="mandarin" @if(old('language') == 'mandarin') selected="selected" @endif>Chinese</option>
                      <option value="english" @if(old('language') == 'english') selected="selected" @endif>English</option>
                      <option value="tamil" @if(old('language') == 'tamil') selected="selected" @endif>Tamil</option>
                      <option value="thai" @if(old('language') == 'thai') selected="selected" @endif>Thai</option>
                  </select>
                </div>
            </div> --}}
            <div class="user_divv">
              <div class="form-group driver user_div hide">
                <label for="contact" class="control-label col-md-2 col-sm-3 col-xs-12">Phone  <span class="driver hide required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="contact" class="form-control" type="phone" name="phone" value="{{ old('phone') }}">
                </div>
              </div>


              <div class="form-group user_div hide">
                <label for="block" class="control-label col-md-2 col-sm-3 col-xs-12">Block</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="block" class="form-control" type="text" name="block" value="{{ old('block') }}">
                </div>
              </div>

              <div class="form-group driver user_div hide">
                <label for="address" class="control-label col-md-2 col-sm-3 col-xs-12">Street Address  <span class="driver hide required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="address" class="form-control" type="text" name="street_address" value="{{ old('street_address') }}">
                </div>
              </div>

              <div class="form-group user_div hide">
                <label for="sub_block" class="control-label col-md-2 col-sm-3 col-xs-12">Sub-Block</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="sub_block" class="form-control" type="text" name="sub_block" value="{{ old('sub_block') }}">
                </div>
              </div>

              <div class="form-group user_div hide">
                <label for="floor_no" class="control-label col-md-2 col-sm-3 col-xs-12">Floor No</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="floor_no" class="form-control" type="text" name="floor_no" value="{{ old('floor_no') }}">
                </div>
              </div>

              <div class="form-group user_div hide">
                <label for="unit_no" class="control-label col-md-2 col-sm-3 col-xs-12">Unit No</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="unit_no" class="form-control" type="text" name="unit_no" value="{{ old('unit_no') }}">
                </div>
              </div>

              <div class="form-group user_div hide">
                <label for="room_no" class="control-label col-md-2 col-sm-3 col-xs-12">Room No</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="room_no" class="form-control" type="text" name="room_no" value="{{ old('room_no') }}">
                </div>
              </div>

              <div class="form-group user_div hide">
                <label for="zip_code" class="control-label col-md-2 col-sm-3 col-xs-12">Postal Code</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="zip_code" class="form-control" type="text" name="zip_code" value="{{ old('zip_code') }}">
                </div>
              </div>

              <div class="form-group user_div hide">
                <label class="control-label col-md-2 col-sm-3 col-xs-12">Work permit <span class="fin_field hide required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                      <label>Front</label>
                      <input type="file" data-text="false" class="type_file fancy_upload" name="work_permit_front">
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                      <label>Back</label>
                      <input type="file" data-text="false" class="type_file fancy_upload" name="work_permit_back">
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group user_div hide">
                <label class="control-label col-md-2 col-sm-3 col-xs-12">WP Expiry</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="wp_expiry" autocomplete="off" name="wp_expiry" class="date-picker form-control" value="{{ old('wp_expiry') }}" type="text">
                </div>
              </div>

              <div class="form-group driver user_div hide">
                <label class="control-label col-md-2 col-sm-3 col-xs-12">Profile Pic  <span class="driver hide required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="file" data-text="false" class="fancy_upload" name="profile_pic">
                </div>
              </div>


              <div class="form-group driver user_div hide">
                <label class="control-label col-md-2 col-sm-3 col-xs-12">Gender <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <input type="hidden" name="gender" value="male">
                  <div id="gender" class="btn-group" data-toggle="buttons">
                    <label class="btn btn-primary active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="gender" selected value="male"> &nbsp; Male &nbsp;
                    </label>
                    <label class="btn btn-default @if(old('gender') == 'female') active @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="gender" @if(old('gender') == 'female') selected @endif value="female"> Female
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group user_div hide">
                <label class="control-label col-md-2 col-sm-3 col-xs-12">Date of Birth <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="dob" name="dob" value="{{ old('dob') }}" autocomplete="off" class="date-picker form-control" type="text">
                </div>
              </div>
          </div>
              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-3 col-xs-12"></div>
                <div class="col-md-6 col-sm-9 col-xs-12">
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
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function(){
    $('[name=fin_no]').on('keyup', function(){
        var val = $(this).val();
        if(val == ''){
            $('.fin_field').addClass('hide')
        }else{
            $('.fin_field').removeClass('hide')
        }
    });
    $('[name=fin_no]').trigger('keyup');
    $('#dob').datepicker({
        format:"yyyy-mm-dd",
        endDate: new Date()
    });
    $('#wp_expiry').datepicker({
        format:"yyyy-mm-dd",
        startDate: new Date()
    });

    $( "#demo-form2" ).validate({
      rules: {
        title: {
            // required: function(element) {
            //   return $("[name=language]").val() == 'english';
            // }
        },
      }
    });
});

$('[name=role]').on('change', function(){
    var value = $(this).val();
    $('.user_div').addClass('hide');
    $('.driver').addClass('hide');
    $('.dorm').addClass('hide');
    $('.app_user').removeClass('hide');
    if(value == 'app-user'){
      $('.user_div').removeClass('hide');
      $('.dorm').removeClass('hide');
      $('.app_user').addClass('hide');
      $('.fancy_upload').fancyfile({
            text  : '',
            // style : 'btn-info',
            placeholder : 'Browse…'
        });
    }else if(value == 'driver'){
      $('.driver').removeClass('hide');
    }else if(value == 'dorm-maintainer'){
      $('.dorm').removeClass('hide');
    }

});

$('[name=gender]').on('change', function(){
    var obj = $(this);
    $('[name=gender]').closest('label').addClass('btn-default').removeClass('btn-primary');
    obj.closest('label').addClass('btn-primary').removeClass('btn-default');
});


$(document).on('ready',function(){
    $('[name=role]').trigger('change');
});

</script>
@endsection
