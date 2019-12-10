@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">

@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Add Topic</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.topic.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="language">Language <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="language" class="form-control" name="language">
                      <option value="english" data-id="en" @if(old('language') == 'english') selected @endif>English</option>
                      <option value="bengali" data-id="bn" @if(old('language') == 'bengali') selected @endif>Bengali</option>
                      <option value="mandarin" data-id="mn" @if(old('language') == 'mandarin') selected @endif>Chinese</option>
                      <option value="tamil" data-id="ta" @if(old('language') == 'tamil') selected @endif>Tamil</option>
                      <option value="thai" data-id="th" @if(old('language') == 'thai') selected @endif>Thai</option>
                  </select>
                </div>
              </div>

              <div class="form-group en language">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-control">
                </div>
              </div>

              <div class="form-group en language">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description">Description <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="description" name="description" class="form-control">{{ old('description') }}</textarea>
                </div>
              </div>

              <div class="form-group mn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_mn">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_mn" name="title_mn" value="{{ old('title_mn') }}" class="form-control">
                </div>
              </div>

              <div class="form-group mn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description_mn">Description <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="description_mn" name="description_mn" class="form-control">{{ old('description_mn') }}</textarea>
                </div>
              </div>


              <div class="form-group ta language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_ta">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_ta" name="title_ta" value="{{ old('title_ta') }}" class="form-control">
                </div>
              </div>

              <div class="form-group ta language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description_ta">Description <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="description_ta" name="description_ta" class="form-control">{{ old('description_ta') }}</textarea>
                </div>
              </div>


              <div class="form-group bn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_bn">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_bn" name="title_bn" value="{{ old('title_bn') }}" class="form-control">
                </div>
              </div>

              <div class="form-group bn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description_bn">Description <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="description_bn" name="description_bn" class="form-control">{{ old('description_bn') }}</textarea>
                </div>
              </div>

              <div class="form-group th language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_th">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_th" name="title_th" value="{{ old('title_th') }}" class="form-control">
                </div>
              </div>

              <div class="form-group th language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description_th">Description <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="description_th" name="description_th" class="form-control">{{ old('description_th') }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Image <span class="required">*</span></label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                  <div class="colapsImg">
                    <div class="   ">
                      <div class="cropme" style="width: 640px; height: 410px;"></div>
                      <input type="hidden" class="form-control" name="path">
                    </div>
                   </div>
                 </div>
              </div>

              <!-- <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="image">Image <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="file" id="image" name="image" class="fancy_upload">
                </div>
              </div> -->

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
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.Jcrop.js') }}"></script>
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.SimpleCropper.js') }}"></script>
<script>
$('[name=language]').on('change', function(){
    var value = $('[name=language] option:selected').attr('data-id');

    $('.language').addClass('hide');
    $('.'+value).removeClass('hide');

});

$(document).ready(function(){

$('.cropme').simpleCropper();

$('[name=language]').trigger('change');
});
</script>

@endsection
