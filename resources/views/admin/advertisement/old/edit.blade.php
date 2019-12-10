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
            <form id="demo-form2" action="{{ route('admin.advertisement.edit', $item->id) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="text" id="title" name="title" value="{{ old('title', $item->title) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="full-name">Placeholder</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <select id="role" class="form-control" name="type">
                      <option value="home" @if(old('type', $item->type) == 'home') selected="selected" @endif>Home</option>
                      <option value="landing" @if(old('type', $item->type) == 'landing') selected="selected" @endif>Landing</option>
                  </select>
                </div>
              </div>

              <div class="form-group order_div @if($item->type != 'landing') hide @endif">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="order">Slider order</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description">Description</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <textarea id="description" name="description" class="form-control col-md-7 col-xs-12">{{ old('description', $item->description) }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Report to whom</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="text" id="report_whom" name="report_whom" value="{{ old('report_whom', $item->report_whom) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Image</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="cropme" style="width: 200px; height: 405px;"></div>
                  <input type="hidden" class="form-control col-md-7 col-xs-12" name="path">
                  <img src= "{{ static_file($item->path) }}" height="405" width="200" />
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Advertisement type</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <label class="col-md-6 col-xs-12">Impression Based
                      <input type="radio" value="1" name="adv_type" @if(old('adv_type', $item->adv_type) == '1') checked @endif>
                  </label>

                  <label class="col-md-6 col-xs-12">Date Based
                      <input type="radio" value="2" name="adv_type" @if(old('adv_type', $item->adv_type) == '2') checked @endif>
                  </label>
                </div>
              </div>

              <div class="impression_div form-group hide">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Number of impression</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                      <input type="number" id="impression" name="impression" value="{{ old('impressions', $item->impressions) }}" class="form-control col-md-7 col-xs-12">
                  </div>
              </div>

              <div class="date_div form-group hide">
                <label for="start_time" class="control-label col-md-3 col-sm-3 col-xs-12">Start Time</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input id="start_time" name="start" value="{{ old('start', $item->start) }}" class="date-picker form-control col-md-7 col-xs-12" type="text">
                </div>
              </div>

              <div class="date_div form-group hide">
                <label for="end_time" class="control-label col-md-3 col-sm-3 col-xs-12">End Time</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input id="end_time" name="end" value="{{ old('end', $item->end) }}" class="date-picker form-control col-md-7 col-xs-12" type="text">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tags">Tags</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <textarea id="tags" name="tags" class="tags form-control col-md-7 col-xs-12">{{ old('tags', $item->tags) }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="link">Link</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="text" id="link" name="link" value="{{ old('link', $item->link) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
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

  $('[name=type]').trigger('change');
  $('[name=adv_type]').trigger('change');

});

</script>
@endsection
