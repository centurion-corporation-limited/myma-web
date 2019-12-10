@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ static_file('js/plugins/jquery.tagsinput/src/jquery.tagsinput.css')}}">
@endsection

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Advertisement</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.advertisement.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Sponsor <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12 add-icon">
                  {!!Form::select('sponsor_id', $sponsor, $item->sponsor_id, ['class' => 'form-control'])!!}
                  <i data-toggle="modal" data-target="#addSponsorModal" class="fa fa-2x fa-plus" style="cursor:pointer;"></i>
                </div>
              </div>


              @if($auth_user->hasRole('food-admin'))
                <input type="hidden" value="food" name="type" >
              @else
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Placeholder <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="role" class="form-control" name="type">
                      <option value="home" @if(old('type', $item->type) == 'home') selected="selected" @endif>Home Slider</option>
                      <option value="landing" @if(old('type', $item->type) == 'landing') selected="selected" @endif>PopUp</option>
                      {{-- <option value="food" @if(old('type', $item->type) == 'food') selected="selected" @endif>Food Dashboard</option> --}}
                  </select>
                </div>
              </div>
              @endif

              <div class="form-group order_div @if($item->type != 'landing') hide @endif">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="order">Slider order </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <?php
                        $order_count[''] = 'Please Select';
                        for($i = 1; $i <= 10; $i++){
                            $order_count[$i] = $i;
                        }
                    ?>
                  {!!Form::select('slider_order', $order_count, $item->slider_order, ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description">Description</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="description" name="description" class="form-control">{{ old('description', $item->description) }}</textarea>
                </div>
              </div>

              @if($auth_user->hasRole('food-admin'))
                <input type="hidden" name="report_whom" value="{{ $item->report_whom }}">
              @else
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Managed By <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('report_whom', $vendors, $item->report_whom, ['class' => 'form-control'])!!}

                  <!-- <input type="text" id="report_whom" name="report_whom" value="{{ old('report_whom', $item->report_whom) }}" class="form-control"> -->
                </div>
              </div>
              @endif

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Advertisement Banner <span class="required">*</span>
                  <br><i class="fa fa-info-circle"></i> Image size must be 640px*410px.</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  <div class="colapsImg">
                    <div class="placehoderImg">
                      <div class="cropme" style="width: 640px; height: 410px;"></div>
                      <input type="hidden" class="form-control" name="path">
                    </div>
                  @if($item->path)
                  <div class="orgImg">
                    <input type="hidden" name="have_image" value="1">
                    <img src= "{{ static_file($item->path) }}" height="410" width="640" />
                  </div>
                  @endif
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Advertisement type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div class="radioBtn">
                    <input type="radio" disabled value="1" name="adv_type" @if(old('adv_type', $item->adv_type) == '1') checked @endif>
                    <label class="control-label">Impression</label>
                  </div>

                  <div class="radioBtn">
                    <input type="radio" disabled value="2" name="adv_type" @if(old('adv_type', $item->adv_type) == '2') checked @endif>
                    <label class="control-label">Duration</label>
                  </div>
                </div>
              </div>

              <div class="impression_div form-group hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="plan_id">Choose Plan <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <select id="plan_id" disabled class="form-control" name="plan_id">
                        @foreach($impressions as $val)
                            <option value="{{ $val->id }}" @if(old('plan_id', $item->plan_id) == $val->id) selected="selected" @endif>{{ $val->impressions }} Impressions - S${{ $val->price }}</option>
                        @endforeach
                    </select>
                </div>
              </div>

              <div class="date_div form-group hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="plan_id">Choose Plan <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <select id="plan_id" class="form-control" name="plan_id" disabled>
                        @foreach($date as $val)
                            <option value="{{ $val->id }}" @if(old('plan_id', $item->plan_id) == $val->id) selected="selected" @endif>A
                              @if($val->impressions == 7 ) Week
                              @elseif($val->impressions == 31 ) Month
                              @elseif($val->impressions == 365 ) Year @endif
                                 - ${{ $val->price }}</option>
                        @endforeach
                    </select>
                </div>
              </div>


              <div class="date_div form-group hide">
                <label for="start_time"  class="control-label col-md-2 col-sm-2 col-xs-12">Start Date <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div class="input-group">
                    <input id="start_time" disabled name="start" value="{{ old('start', $item->start) }}" class="date-picker form-control" type="text">
                    <label class="input-group-addon" for="start">
                       <i class="fa fa-calendar open-datetimepicker"></i>
                    </label>
                  </div>
                </div>
              </div>

              <div class="date_div form-group hide">
                <label for="end_time" class="control-label col-md-2 col-sm-2 col-xs-12">End Date <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div class="input-group">
                    <input id="end_time" disabled name="end" value="{{ old('end', $item->end) }}" class="date-picker form-control" type="text">
                    <label class="input-group-addon" for="end">
                       <i class="fa fa-calendar open-datetimepicker"></i>
                    </label>
                  </div>
                </div>
              </div>

              {{-- <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="tags">Tags</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="tags" name="tags" class="tags form-control">{{ old('tags', $item->tags) }}</textarea>
                </div>
            </div> --}}

                @if($auth_user->hasRole('food-admin'))
                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Food Item <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                      {!!Form::select('food_item', $foods, $item->food_item, ['class' => 'form-control'])!!}
                  </div>
                </div>
                @else
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="link">External Link</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="link" name="link" value="{{ old('link', $item->link) }}" class="form-control">
                </div>
              </div>
              @endif
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







    <!-- Modal -->
    <div id="addSponsorModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Sponsor</h4>
        </div>
        <form id="demo-form2" action="{{ route('admin.sponsor.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
          {{ csrf_field() }}
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-body order-popup">

                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Sponsor</label>
                  <div class="col-md-10 col-sm-10 col-xs-12">
                    <input type="text" id="link" name="name" value="{{ old('name') }}" class="form-control">
                  </div>

                </div>


          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Add</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

          </form>
      </div>
    </div>
@endsection
@section('scripts')
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.Jcrop.js') }}"></script>
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.SimpleCropper.js') }}"></script>
<script src="{{ static_file('js/plugins/jquery.tagsinput/src/jquery.tagsinput.js')}}"></script>
<script>

$('.tags').tagsInput({
  width: '100%',
  height: '50px'
  //tagClass: 'label label-info'
});
$(document).on('ready',function(){
  $('.cropme').simpleCropper();
  // $('.date-picker').datepicker({
  //   format:"dd/mm/yyyy"
  // });

  $('[name=start]').datepicker({
     format:"dd/mm/yyyy",
     startDate: '1'
   }).on('changeDate', function (selected) {
   if(selected.date != undefined)
     var minDate = new Date(selected.date.valueOf());
   else
     var minDate = new Date(this.value.valueOf());
     $('[name=end]').datepicker('setStartDate', minDate);
 });

 $('[name=end]').datepicker({
    format:"dd/mm/yyyy",
    startDate: '1'
  }).on('changeDate', function (selected) {
  if(selected.date != undefined)
    var minDate = new Date(selected.date.valueOf());
  else
    var minDate = new Date(this.value.valueOf());
    $('[name=start]').datepicker('setEndDate', minDate);
 });

  $('[name=adv_type]').on('change',function(){
      if($('[name=adv_type]:checked').val() == 1){
          $('#start').val('');
          $('#end').val('');
          $('.impression_div').removeClass('hide');
          $('.date_div').addClass('hide');
          // $('.impression_div').find('[name=plan_id]').prop('disabled', false);
          // $('.date_div').find('[name=plan_id]').prop('disabled', true);
      }
      else if($('[name=adv_type]:checked').val() == 2){
          $('#impressions').val(0);
          $('.impression_div').addClass('hide');
          $('.date_div').removeClass('hide');
          // $('.impression_div').find('[name=plan_id]').prop('disabled', true);
          // $('.date_div').find('[name=plan_id]').prop('disabled', false);
      }
  });

  $('[name=type]').on('change',function(){
     if($(this).val() == 'landing'){
         $('.order_div').addClass('hide');
     }
     else {
         $('.order_div').removeClass('hide');
     }
  });

  $('[name=type]').trigger('change');
  $('[name=adv_type]').trigger('change');

});

</script>
@endsection
