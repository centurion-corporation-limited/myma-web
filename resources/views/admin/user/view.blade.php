@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('css/lightbox.min.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<style>
table th, table tr{
      text-align: center;
}
</style>
@endsection

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>View Details</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form class="form-horizontal form-label-left colord-form">
              {{-- <div class="user_profile">
                <div class="user-profile-box" style="background-image: url({{ static_file('images/body-bg.jpg') }})">
                    <div class="box-profile text-white">
                        <div class="profile-image">
                          @if(isset($user->profile) && $user->profile->profile_pic)
                          <img class="profile-user-img img-responsive img-circle" src="{{ static_file($user->profile->profile_pic) }}" />
                          @else
                          <img class="profile-user-img img-responsive img-circle" src="{{ static_file('images/img1.jpg') }}">
                          @endif
                        </div>
                        <div class="profile-content">
                          <h3 class="profile-username text-center">{{ ucwords($user->name) }}</h3>
                          <p class="text-center">{{ $user->email }}</p>
                          <p class="text-center">Role : @foreach($user->getRoles() as $key => $role)
                              {{ $roles[$key] }}
                          @endforeach
                          {!!Form::select('role', $roles, current(array_keys($user->getRoles())), ['class' => 'form-control hide'])!!}


                        </div>
                    </div>
                  </div>
              </div> --}}




              <div class="form-group row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="full-name">Role
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    @foreach($user->getRoles() as $key => $role)
                        {{ @$roles[$key] }}
                    @endforeach
                    {!!Form::select('role', $roles, current(array_keys($user->getRoles())), ['class' => 'form-control hide'])!!}

                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ ucwords($user->name) }}
                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">Email
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $user->email }}
                </div>
              </div>

              <!-- <div class="form-group row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="emp_id">Language</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ ucwords($user->language) }}
                </div>
              </div> -->

          <!-- <div class="user_div"> -->
          <div class="form-group driver hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="vehicle_no">Vehicle No  
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                   {{ $user->profile->vehicle_no or '--'  }} 
                </div>
              </div>
              
              <div class="form-group driver user_div row">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Fin No</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $user->profile->fin_no or '--' }}
                </div>
              </div>

              <div class="form-group driver hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="type">Type  
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">@if($user->type) Food Outlet @else Catering @endif
                </div>
              </div>

              <div class="form-group user_div row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Work permit</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  @if(isset($user->profile) && $user->profile->wp_front)
                  <div class="permit">
                    <label> Front
                    @if($auth_user->can('view.user-list'))
                      <a href="{{ route('admin.user.download.permit', ['id' => encrypt($user->id), 'type' => 'front']) }}"><i class="fa fa-2x fa-download"></i></a>
                    @endif
                    </label>
                    <a href="{{ static_file($user->profile->wp_front) }}" data-lightbox="image-1">
                        <img src="{{ static_file($user->profile->wp_front) }}" />
                    </a>    
                  </div>
                  @endif
                  @if(isset($user->profile) && $user->profile->wp_back)
                  <div class="permit">
                    <label>Back
                      @if($auth_user->can('view.user-list'))
                        <a href="{{ route('admin.user.download.permit', ['id' => encrypt($user->id), 'type' => 'back']) }}"><i class="fa fa-2x fa-download"></i></a>
                      @endif
                    </label>
                    <a href="{{ static_file($user->profile->wp_back) }}" data-lightbox="image-1">
                        <img src="{{ static_file($user->profile->wp_back) }}" />
                    </a>
                  </div>
                  @endif
                </div>
              </div>

              <div class="form-group user_div row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">WP Expiry</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ (isset($user->profile) && $user->profile->wp_expiry != '0000-00-00')?$user->profile->wp_expiry:'' }}
                </div>
              </div>

              <div class="form-group user_div driver row">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Phone</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ isset($user->profile)?$user->profile->phone:'' }}
                </div>
              </div>

              <div class="form-group user_div row">
                <label for="block" class="control-label col-md-3 col-sm-3 col-xs-12">Block</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ isset($user->profile)?$user->profile->block:'' }}
                </div>
              </div>

              <div class="form-group user_div row">
                <label for="address" class="control-label col-md-3 col-sm-3 col-xs-12">Street Address</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ isset($user->profile)?(isset($user->profile->dormitory)?$user->profile->dormitory->address:$user->profile->street_address):'' }}
                </div>
              </div>

              <div class="form-group user_div row">
                <label for="sub_block" class="control-label col-md-3 col-sm-3 col-xs-12">Sub-Block</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ isset($user->profile)?$user->profile->sub_block:'' }}
                </div>
              </div>

              <div class="form-group user_div row">
                <label for="floor_no" class="control-label col-md-3 col-sm-3 col-xs-12">Floor No</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ isset($user->profile)?$user->profile->floor_no:'' }}
                </div>
              </div>

              <div class="form-group user_div row">
                <label for="unit_no" class="control-label col-md-3 col-sm-3 col-xs-12">Unit No</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ isset($user->profile)?$user->profile->unit_no:'' }}
                </div>
              </div>

              <div class="form-group user_div row">
                <label for="room_no" class="control-label col-md-3 col-sm-3 col-xs-12">Room No</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ isset($user->profile)?$user->profile->room_no:'' }}
                </div>
              </div>

              <div class="form-group user_div row">
                <label for="zip_code" class="control-label col-md-3 col-sm-3 col-xs-12">Zip Code</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ isset($user->profile)?$user->profile->zip_code:'' }}
                </div>
              </div>

              <div class="form-group user_div row">
                <label for="zip_code" class="control-label col-md-3 col-sm-3 col-xs-12">Dormitory</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ isset($user->profile)?($user->profile->dormitory?$user->profile->dormitory->name:''):'' }}
                </div>
              </div>

              <div class="form-group user_div driver row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Profile Pic</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  @if(isset($user->profile) && $user->profile->profile_pic)
                  <div class="profleImg">
                        <a href="{{ static_file($user->profile->profile_pic) }}" data-lightbox="image-1">
                            <img src="{{ static_file($user->profile->profile_pic) }}" />
                        </a>
                  </div>
                  @endif
                </div>
              </div>

              <div class="form-group user_div driver row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Gender</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ isset($user->profile)?$user->profile->gender:'' }}
                </div>
              </div>
              <div class="form-group user_div row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Date of Birth
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {{ isset($user->profile)?$user->profile->dob:'' }}
                </div>
              </div>
          <!-- </div> -->
              <div class="form-group row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="full-name">Status</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  @if($user->blocked == '0')Unblocked @else Blocked @endif
                </div>
              </div>
              <div class="form-group user_div row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Good for wallet
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    @if($user->good_for_wallet == 'Y')
                    Yes
                    @elseif($user->good_for_wallet == 'N')
                    No
                    @elseif($user->good_for_wallet == 'C')
                    Corrected
                    @elseif($user->good_for_wallet == 'D')
                    Done
                    @endif
                </div>
              </div>

              <div class="form-group user_div row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Good for wallet updated at
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {{ $user->good_date }}
                </div>
              </div>
              
              <div class="form-group user_div row">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Flexm account registered using
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    @if($user->flexm_account == 1)
                        @if($user->flexm_direct == 1)
                            MyMA App
                        @else
                            @if($user->flexm_cron == 1)
                                Cron
                            @elseif($user->flexm_cron == '')    
                                MyMA App
                            @endif
                        @endif    
                    @endif
                    
                </div>
              </div>
              <div class="ln_solid"></div>
              <div class="form-group row">
                <div class="col-md-3 col-sm-3 col-xs-12"></div>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  @if($auth_user->can('update.user-edit'))

                  <a class="btn btn-success" href="{{ route('admin.user.edit', encrypt($user->id)) }}">Update</a>
                  @endif
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
@section('scripts')
<script src="{{  static_file('js/lightbox.min.js') }}"></script>
<script>
$(document).on('ready',function(){
  $('.user_div').addClass('hide');
  $('.driver').addClass('hide');
    // console.log($('#role').val());
  if($('[name=role]').val() == '3'){
    $('.user_div').removeClass('hide');
  }else if($('[name=role]').val() == '6'){
    $('.driver').removeClass('hide');
  }
});

</script>
@endsection
