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
            <h2>Add Advertisement</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.advertisement.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="role">Placeholder</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <select id="role" class="form-control" name="type">
                      <option value="landing" @if(old('type') == 'landing') selected="selected" @endif>Popup</option>
                      <option value="home" @if(old('type') == 'home') selected="selected" @endif>Home Slider</option>
                  </select>
                </div>
              </div>

              <div class="form-group order_div hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="order">Slider order</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description">Description</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <textarea id="description" name="description" class="form-control col-md-7 col-xs-12">{{ old('description') }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Report to whom</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="text" id="report_whom" name="report_whom" value="{{ old('report_whom') }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Image</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                      <div class="cropme" style="width: 200px; height: 405px;"></div>
                      <input type="hidden" class="form-control col-md-7 col-xs-12" name="path">
                  </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Advertisement type</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <label class="col-md-6 col-xs-12">Impression Based
                      <input type="radio" value="1" name="adv_type">
                  </label>

                  <label class="col-md-6 col-xs-12">Date Based
                      <input type="radio" value="2" name="adv_type">
                  </label>
                </div>
              </div>

              <div class="impression_div form-group hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Number of impression</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="number" id="impression" name="impressions" value="{{ old('impressions') }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>


              <div class="date_div form-group hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Start Time</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input id="birthday" name="start" value="{{ old('start') }}" class="date-picker form-control col-md-7 col-xs-12" type="text">
                </div>
              </div>

              <div class="date_div form-group hide">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">End Time</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input id="birthday" name="end"  value="{{ old('end') }}" class="date-picker form-control col-md-7 col-xs-12" type="text">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tags">Tags</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <textarea id="tags" name="tags" class="tags form-control col-md-7 col-xs-12">{{ old('tags') }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="link">Link</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="text" id="link" name="link" value="{{ old('link') }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>


              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
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

  $('.date-picker').datepicker({
    format:"yyyy-mm-dd"
  });

  $('[name=adv_type]').on('change',function(){
     if($('[name=adv_type]:checked').val() == 1){
         $('.impression_div').removeClass('hide');
         $('.date_div').addClass('hide');
     }
     else if($('[name=adv_type]:checked').val() == 2){
         $('.impression_div').addClass('hide');
         $('.date_div').removeClass('hide');
     }
  });

  $('[name=type]').on('change',function(){
     if($(this).val() == 'landing'){
         $('.order_div').removeClass('hide');
     }
     else {
         $('.order_div').addClass('hide');
     }
  });

});

</script>
@endsection
