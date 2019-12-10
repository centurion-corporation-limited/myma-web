@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<style>
table th, table tr{
      text-align: center;
}
#good_for_wallet.btn-group > .btn:first-child{
  margin-left: 0px;
}
</style>
@endsection

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Details</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.user.update', encrypt($user->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="full-name">Role <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="hidden" name="page" value="{{ old('page', request('page')) }}">
                  {!!Form::select('role', $roles, current(array_keys($user->getRoles())), ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group hide dorm">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="full-name">Dormitory
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {!!Form::select('dormitory_id', $dorm, $user->dormitory_id, ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">Email <span class="app_user required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
                </div>
              </div>

              <div class="form-group driver user_div hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fin_no">Fin No
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="fin_no" name="fin_no" value="{{ old('fin_no', isset($user->profile)?$user->profile->fin_no:'') }}" class="form-control">
                </div>
              </div>

              <div class="form-group driver hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="type">Type <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="type" class="form-control" name="type">
                      <option value="free" @if(old('type', $user->type) == 'free') selected="selected" @endif>Food outlet</option>
                      <option value="package" @if(old('type', $user->type) == 'package') selected="selected" @endif>Catering</option>
                  </select>
                </div>
              </div>

              <div class="form-group driver hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="vehicle_no">Vehicle No <span class="required driver hide">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="vehicle_no" name="vehicle_no" value="{{ old('vehicle_no', isset($user->profile)?$user->profile->vehicle_no:'') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password">Password
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="password" id="password" name="password" value="" class="form-control"></br>
                  <i class="fa fa-info-circle"></i>Password should have at least 1 number, 1 lowercase letter, 1 uppercase letter, 1 special character and minimum length of 8.

                </div>
              </div>

              {{-- <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="emp_id">Language</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="language" class="form-control" name="language">
                      <option value="bengali" @if(old('language', $user->language) == 'bengali') selected="selected" @endif>Bengali</option>
                      <option value="mandarin" @if(old('language', $user->language) == 'mandarin') selected="selected" @endif>Chinese</option>
                      <option value="english" @if(old('language', $user->language) == 'english') selected="selected" @endif>English</option>
                      <option value="tamil" @if(old('language', $user->language) == 'tamil') selected="selected" @endif>Tamil</option>
                      <option value="thai" @if(old('language', $user->language) == 'thai') selected="selected" @endif>Thai</option>
                  </select>
                </div>
            </div> --}}

            <div class="user_divv">
              <div class="form-group driver user_div hide">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Phone <span class="required driver hide">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="contact" class="form-control" type="phone" name="phone" value="{{ old('phone', isset($user->profile)?$user->profile->phone:'') }}">
                </div>
              </div>


              <div class="form-group user_div hide">
                <label for="block" class="control-label col-md-3 col-sm-3 col-xs-12">Block</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="block" class="form-control" type="text" name="block" value="{{ old('block', isset($user->profile)?$user->profile->block:'') }}">
                </div>
              </div>

              <div class="form-group driver user_div hide">
                <label for="address" class="control-label col-md-3 col-sm-3 col-xs-12">Street Address <span class="required driver hide">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="address" class="form-control" type="text" name="street_address" value="{{ old('street_address', isset($user->profile)?(isset($user->profile->dormitory)?$user->profile->dormitory->address:$user->profile->street_address):'') }}">
                </div>
              </div>

              <div class="form-group user_div hide">
                <label for="sub_block" class="control-label col-md-3 col-sm-3 col-xs-12">Sub-Block</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="sub_block" class="form-control" type="text" name="sub_block" value="{{ old('sub_block', isset($user->profile)?$user->profile->sub_block:'') }}">
                </div>
              </div>

              <div class="form-group user_div">
                <label for="floor_no" class="control-label col-md-3 col-sm-3 col-xs-12">Floor No</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="floor_no" class="form-control" type="text" name="floor_no" value="{{ old('floor_no', isset($user->profile)?$user->profile->floor_no:'') }}">
                </div>
              </div>

              <div class="form-group user_div hide">
                <label for="unit_no" class="control-label col-md-3 col-sm-3 col-xs-12">Unit No</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="unit_no" class="form-control" type="text" name="unit_no" value="{{ old('unit_no', isset($user->profile)?$user->profile->unit_no:'') }}">
                </div>
              </div>

              <div class="form-group user_div hide">
                <label for="room_no" class="control-label col-md-3 col-sm-3 col-xs-12">Room No</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="room_no" class="form-control" type="text" name="room_no" value="{{ old('room_no', isset($user->profile)?$user->profile->room_no:'') }}">
                </div>
              </div>

              <div class="form-group user_div hide">
                <label for="zip_code" class="control-label col-md-3 col-sm-3 col-xs-12">Postal Code</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="zip_code" class="form-control" type="text" name="zip_code" value="{{ old('zip_code', isset($user->profile)?$user->profile->zip_code:'') }}">
                </div>
              </div>

              <div class="form-group user_div">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Work permit
                    <span class="fin_field @if($user->profile && $user->profile->fin_no == '') hide @endif required">*</span></label>

                <div class="col-md-6 col-sm-9 col-xs-12">
                  <div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                      <label> Front </label>
                      <input type="file" class="type_file fancy_upload" name="wp_front">
                      @if(isset($user->profile) && $user->profile->wp_front)<img src="{{ static_file($user->profile->wp_front) }}" />@endif
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                      <label>Back</label>
                      <input type="file" class="type_file fancy_upload" name="wp_back">
                      @if(isset($user->profile) && $user->profile->wp_back)<img src="{{ static_file($user->profile->wp_back) }}" />@endif
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group user_div hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">WP Expiry</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="wp_expiry" autocomplete="off" name="wp_expiry" class="date-picker form-control" value="{{ old('wp_expiry', @$user->profile->wp_expiry) }}" type="text">
                </div>
              </div>

              <div class="form-group driver user_div hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Profile Pic <span class="required driver hide">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="file" class="type_file fancy_upload" name="profile_pic">
                  @if(isset($user->profile) && $user->profile->profile_pic)<img src="{{ static_file($user->profile->profile_pic) }}"  height="100" width="100" />@endif
                </div>
              </div>


              <div class="form-group driver user_div hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Gender <span class="required">*</span></label>
                <input type="hidden" name="gender" value="{{ $user->profile->gender or 'male'}}">

                <div class="col-md-6 col-sm-9 col-xs-12">
                  <div id="gender" class="btn-group" data-toggle="buttons">
                    <label class="btn @if(old('gender', isset($user->profile)?$user->profile->gender:'' ) == 'male') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="gender" @if(old('gender', isset($user->profile)?$user->profile->gender:'' ) == 'male') selected @endif value="male"> &nbsp; Male &nbsp;
                    </label>
                    <label class="btn @if(old('gender', isset($user->profile)?$user->profile->gender:'' ) == 'female') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="gender" @if(old('gender', isset($user->profile)?$user->profile->gender:'' ) == 'female') selected @endif value="female"> Female
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group user_div hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Date of Birth <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input id="dob" autocomplete="off" name="dob" value="{{ old('dob', isset($user->profile)?$user->profile->dob:'' )}}" class="date-picker form-control" type="text">
                </div>
              </div>
          </div>
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="full-name">Status</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="blocked" class="form-control" name="blocked" >
                      <option value="">--Select--</option>
                      <option value="0" @if($user->blocked == '0') selected="selected" @endif>Unblocked</option>
                      <option value="1" @if($user->blocked == '1') selected="selected" @endif>Blocked</option>
                  </select>
                </div>
              </div>
              <div class="form-group user_div hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Good for wallet <span class="required">*</span></label>
                <input type="hidden" name="good_for_wallet" value="{{ $user->good_for_wallet or 'N'}}">

                <div class="col-md-6 col-sm-9 col-xs-12">
                  <div id="good_for_wallet" class="btn-group" data-toggle="buttons">
                    @if($user->good_for_wallet  != 'D')
                        @if($auth_user->hasRole('app-user-manager'))
                            <label class="btn @if(old('good_for_wallet', $user->good_for_wallet ) == 'Y') btn-primary active @else btn-default @endif" @if($auth_user->hasRole('app-user-manager')) data-toggle-class="btn-primary" data-toggle-passive-class="btn-default" @endif>
                              <input @if(!$auth_user->hasRole('app-user-manager')) disabled @endif type="radio" name="good_for_wallet" @if(old('good_for_wallet', $user->good_for_wallet ) == 'Y') selected @endif value="Y"> &nbsp; Yes &nbsp;
                            </label>
                        @endif
                    <label class="btn @if(old('good_for_wallet', $user->good_for_wallet ) == 'N') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="good_for_wallet" @if(old('good_for_wallet', $user->good_for_wallet ) == 'N') selected @endif value="N"> No
                    </label>
                    <label class="btn @if(old('good_for_wallet', $user->good_for_wallet ) == 'C') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="good_for_wallet" @if(old('good_for_wallet', $user->good_for_wallet ) == 'C') selected @endif value="C"> &nbsp; Corrected &nbsp;
                    </label>
                    @endif
                    <label class="btn @if(old('good_for_wallet', $user->good_for_wallet ) == 'D') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="good_for_wallet" @if(old('good_for_wallet', $user->good_for_wallet ) == 'D') selected @endif value="D"> &nbsp; Done &nbsp;
                    </label>
                  </div>
                </div>
              </div>
              
              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-3 col-sm-3 col-xs-12"></div>
                <div class="col-md-9 col-sm-9 col-xs-12">
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
});
$('[name=role]').on('change', function(){
    var value = $(this).val();
    $('.user_div').addClass('hide');
    $('.driver').addClass('hide');
    $('.dorm').addClass('hide');
    $('.app_user').removeClass('hide');
    if(value == '3'){
      $('.user_div').removeClass('hide');
      $('.app_user').addClass('hide');
      $('.dorm').removeClass('hide');
    }else if($('[name=role]').val() == '6'){
      $('.driver').removeClass('hide');
    }else if($('[name=role]').val() == '10'){
      $('.dorm').removeClass('hide');
    }
});
$(document).on('ready',function(){
    // console.log($('#role').val());
    $('[name=role]').trigger('change');

    $('[name=gender]').on('change', function(){
        var obj = $(this);
        $('[name=gender]').closest('label').addClass('btn-default').removeClass('btn-primary');
        obj.closest('label').addClass('btn-primary').removeClass('btn-default');
    });
    
    $('[name=good_for_wallet]').on('change', function(){
        var obj = $(this);
        $('[name=good_for_wallet]').closest('label').addClass('btn-default').removeClass('btn-primary');
        obj.closest('label').addClass('btn-primary').removeClass('btn-default');
    });


});


</script>
@endsection
