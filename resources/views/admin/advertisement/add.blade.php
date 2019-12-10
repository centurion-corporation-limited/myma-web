@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ static_file('js/plugins/jquery.tagsinput/src/jquery.tagsinput.css')}}">
<style>
.modal-footer.text-center{
  text-align: center !important;
}
</style>
@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Add Advertisement</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.advertisement.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Sponsor <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12 add-icon">
                  {!!Form::select('sponsor_id', $sponsor, '', ['class' => 'form-control'])!!}
                  <i data-toggle="modal" data-target="#addSponsorModal" class="fa fa-2x fa-plus" style="cursor:pointer;"></i>
                </div>
              </div>

              @if($auth_user->hasRole('food-admin'))
                <input type="hidden" value="food" name="type" >
              @else
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="role">Placeholder <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="role" class="form-control" name="type">
                      <option value="home" @if(old('type') == 'home') selected="selected" @endif>Home Slider</option>
                      <option value="landing" @if(old('type') == 'landing') selected="selected" @endif>Popup</option>

                      {{-- <option value="food" @if(old('type') == 'food') selected="selected" @endif>Food Dashboard</option> --}}
                  </select>
                </div>
              </div>
              @endif

              <div class="form-group order_div">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="order">Slider order <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <?php
                        $order_count[''] = 'Please Select';
                        for($i = 1; $i <= 10; $i++){
                            $order_count[$i] = $i;
                        }

                    ?>
                  {!!Form::select('slider_order', $order_count, '', ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description">Description</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="description" name="description" class="form-control col-md-7 col-xs-12">{{ old('description') }}</textarea>
                </div>
              </div>

              @if($auth_user->hasRole('food-admin'))
                <input type="hidden" name="report_whom" value="{{ $auth_user->id }}">
              @else
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Managed By <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('report_whom', $vendors, '', ['class' => 'form-control'])!!}
                  <!-- <input type="text" id="report_whom" name="report_whom" value="{{ old('report_whom') }}" class="form-control col-md-7 col-xs-12"> -->
                </div>
              </div>
              @endif

              <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12">Advertisement Banner <span class="required">*</span>
                    <br><i class="fa fa-info-circle"></i> Image size must be 640px*410px.</label>
                  <div class="col-md-10 col-sm-10 col-xs-12">
                      <div class="cropme" style="width: 640px; height: 410px;"></div>
                      <input type="hidden" class="form-control col-md-7 col-xs-12" name="path">
                  </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Advertisement type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div class="radioBtn">
                    <input type="radio" value="1" name="adv_type" @if(old('adv_type') == 1) checked @endif>
                    <label class="control-label">Impression</label>
                  </div>
                  <div class="radioBtn">
                    <input type="radio" value="2" name="adv_type" @if(old('adv_type') == 2) checked @endif>
                    <label class="control-label">Duration</label>
                  </div>
                </div>
              </div>

              <div class="impression_div form-group hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="plan_id">Choose Plan <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <select id="plan_id" class="form-control" name="plan_id">
                        @foreach($impressions as $val)
                            <option value="{{ $val->id }}" @if(old('plan_id') == $val->id) selected="selected" @endif>{{ $val->impressions }} Impressions - S${{ $val->price }}</option>
                        @endforeach
                    </select>
                </div>
              </div>

              <div class="date_div form-group hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="plan_id">Choose Plan <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <select id="plan_id" class="form-control" name="plan_id" disabled>
                        @foreach($date as $val)
                            <option value="{{ $val->id }}" data-val="{{ $val->impressions }}" @if(old('plan_id') == $val->id) selected="selected" @endif>A
                                @if($val->impressions == 7 ) Week
                                @elseif($val->impressions == 31 ) Month
                                @elseif($val->impressions == 365 ) Year @endif
                                 - ${{ $val->price }}</option>
                        @endforeach
                    </select>
                </div>
              </div>

              <div class="date_div form-group hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Start Date <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div class="input-group">
                    <input id="start" autocomplete="off" name="start" value="{{ old('start') }}" class="date-picker form-control col-md-7 col-xs-12" type="text">
                    <label class="input-group-addon" for="start">
                       <i class="fa fa-calendar open-datetimepicker"></i>
                    </label>
                  </div>
                </div>
              </div>

              <div class="date_div form-group hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">End Date <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div class="input-group">
                    <input id="end" name="end" readonly autocomplete="off"  value="{{ old('end') }}" class="date-picker form-control col-md-7 col-xs-12" type="text">
                    <label class="input-group-addon" for="date">
                       <i class="fa fa-calendar"></i>
                    </label>
                  </div>
                </div>
              </div>

              {{-- <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="tags">Tags</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="tags" name="tags" class="tags form-control col-md-7 col-xs-12">{{ old('tags') }}</textarea>
                </div>
            </div> --}}

            @if($auth_user->hasRole('food-admin'))
            <div class="form-group">
              <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Food Item <span class="required">*</span></label>
              <div class="col-md-6 col-sm-10 col-xs-12">
                  {!!Form::select('food_item', $foods, '', ['class' => 'form-control'])!!}
              </div>
            </div>
            @else

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="link">External Link</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="link" name="link" value="{{ old('link') }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>
              @endif

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

    <!-- Modal -->
    <div id="addSponsorModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4 class="modal-title text-center" id="myModalLabel">Add Sponsor</h4>
        </div>
            <form id="sponsorAdd" action="{{ route('admin.sponsor.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              <div class="modal-body order-popup">
                <div class="error form-group">
                </div>
                {{ csrf_field() }}
                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="order">Name <span class="required">*</span></label>
                  <div class="col-md-10 col-sm-10 col-xs-12">
                    <input type="text" id="name" required name="name" value="{{ old('name') }}" class="form-control">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="email">Email <span class="required">*</span></label>
                  <div class="col-md-10 col-sm-10 col-xs-12">
                    <input type="email" id="email" required name="email" value="{{ old('email') }}" class="form-control">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="phone">Phone No <span class="required">*</span></label>
                  <div class="col-md-10 col-sm-10 col-xs-12">
                    <input type="text" id="phone" required name="phone" value="{{ old('phone') }}" class="form-control">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="address">Address</label>
                  <div class="col-md-10 col-sm-10 col-xs-12">
                    <textarea id="address" name="address" class="form-control">{{ old('address') }}</textarea>
                  </div>
                </div>
              </div>


              <div class="modal-footer text-center">
                <button type="submit" class="btn btn-success">Add</button>
              </div>
            </form>
            <!-- <a class="icon-close" data-dismiss="modal" href="javascript:;"><img src="{{ static_file('merchant/images/icon-close.png') }}" alt=""></a> </div> -->

      </div>
    </div>
@endsection
@section('scripts')
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
    $("#sponsorAdd").submit(function(event) {
                /* stop form from submitting normally */
                event.preventDefault();
                /* get some values from elements on the page: */
                var $form = $(this),
                    data = $form.serialize(),
                    url = $form.attr('action');

                /* Send the data using post */
                var posting = $.post(url, data);

                /* Put the results in a div */
                posting.done(function(data) {
                    location.reload();
                });
                posting.error(function(data) {
                    var error = JSON.parse(data.responseText);
                    if(error.name != 'undefined'){
                      $('.error').append('<div>'+error.name+'</div>');
                    }
                });
    });

    $('.cropme').simpleCropper();


  // $('.date-picker').datepicker({
  //   format:"dd/mm/yyyy"
  // });

 $('[name=start]').datepicker({
    format:"dd/mm/yyyy",
    startDate: '1'
  });


$('[name=start]').on('change', function () {
    var endDate;
    var val = $('.date_div [name=plan_id] option:selected').attr('data-val');
    endDate = $('[name=start]').datepicker('getDate');
    if(endDate != ''){

      endDate = moment(endDate);

      if(val == 7){
        endDate.add(6, 'd');
      }else if(val == 31){
        endDate.add(1, 'month');
        endDate.subtract(1, 'd');
      }else if(val == 365){
        endDate.add(1, 'year');
        endDate.subtract(1, 'd');
      }else{
        endDate.add(1, 'd');
      }

      $('[name=end]').val(endDate.format('DD/MM/YYYY'));
    }
    // $('[name=end]').datepicker('setDate', endDate.getDate() + '/' + (endDate.getMonth() + 1) + '/' + endDate.getFullYear()format('DD/MM/YYYY'));
});

// $('[name=end]').datepicker({
//    format:"dd/mm/yyyy",
//    startDate: '1'
//  }).on('changeDate', function (selected) {
//  if(selected.date != undefined)
//    var minDate = new Date(selected.date.valueOf());
//  else
//    var minDate = new Date(this.value.valueOf());
//    $('[name=start]').datepicker('setEndDate', minDate);
// });

  $('[name=adv_type]').on('change',function(){
     if($('[name=adv_type]:checked').val() == 1){
         $('#start').val('');
         $('#end').val('');
         $('.impression_div').removeClass('hide');
         $('.date_div').addClass('hide');
         $('.impression_div').find('[name=plan_id]').prop('disabled', false);
         $('.date_div').find('[name=plan_id]').prop('disabled', true);
     }
     else if($('[name=adv_type]:checked').val() == 2){
         $('#impressions').val(0);
         $('.impression_div').addClass('hide');
         $('.date_div').removeClass('hide');
         $('.impression_div').find('[name=plan_id]').prop('disabled', true);
         $('.date_div').find('[name=plan_id]').prop('disabled', false);
     }
  });

  $('[name=plan_id]').on('change',function(){
      $('[name=start]').trigger('change');
  });

  $('[name=adv_type]').trigger('change');
  $('[name=type]').on('change',function(){
     if($(this).val() == 'landing'){
         $('.order_div').addClass('hide');
     }
     else {
         $('.order_div').removeClass('hide');
     }
  });

});

</script>
@endsection
