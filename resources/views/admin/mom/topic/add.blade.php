@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">
@endsection

@section('content')

<style>
.file .fancy-file {
	display: none;
}
.fancy-file input[type="file"] {
	width: 100% !important;
	display: block !important;
}
</style>

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
            <form id="demo-form2" action="{{ route('admin.mom.topic.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Category <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('category_id', $cats, '', ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Language <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('language', $languages, '', ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="title" name="title" value="{{ old('title') }}" class="english language form-control">
                    <input type="text" id="title" name="title_bn" value="{{ old('title_bn') }}" class="bengali hide language form-control">
                    <input type="text" id="title" name="title_mn" value="{{ old('title_mn') }}" class="mandarin hide language form-control">
                    <input type="text" id="title" name="title_th" value="{{ old('title_th') }}" class="thai hide language form-control">
                    <input type="text" id="title" name="title_ta" value="{{ old('title_ta') }}" class="tamil hide language form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="image">Image <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <div class="cropme" style="width: 640px; height: 410px;"></div>
                    <input type="hidden" class="form-control" name="path">
                  <!-- <input type="file" id="image" name="image" class="form-control"> -->
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Content Type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <label for="text"><input type="radio" id="text" name="type" value="text" @if(old('type') == 'text') checked @endif>Text</label>
                    <label for="file"><input type="radio" id="file" name="type" value="file" @if(old('type') == 'file') checked @endif>File</label>
                    <label for="youtube"><input type="radio" id="youtube" name="type" value="youtube" @if(old('type') == 'youtube') checked @endif>Youtube</label>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <div class="language english">
                        <input type="file" id="content" name="content" class="content_file hide fancy_upload">
                        <textarea id="content" name="content" class="content_text form-control">{{ old('content') }}</textarea>
                    </div>
                    <div class="language bengali hide">
                        <input type="file" id="content" name="content_bn" class="content_file hide fancy_upload">
                        <textarea id="content" name="content_bn" class="content_text form-control">{{ old('content_bn') }}</textarea>
                    </div>
                    <div class="language mandarin hide">
                        <input type="file" id="content" name="content_mn" class="content_file hide fancy_upload">
                        <textarea id="content" name="content_mn" class="content_text form-control">{{ old('content_mn') }}</textarea>
                    </div>
                    <div class="language thai hide">
                        <input type="file" id="content" name="content_th" class="content_file hide fancy_upload">
                        <textarea id="content" name="content_th" class="content_text form-control">{{ old('content_th') }}</textarea>
                    </div>
                    <div class="language tamil hide">
                        <input type="file" id="content" name="content_ta" class="content_file hide fancy_upload">
                        <textarea id="content" name="content_ta" class="content_text form-control">{{ old('content_ta') }}</textarea>
                    </div>
                </div>
              </div>

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
$(document).on('ready',function(){
    $('.cropme').simpleCropper();

    $(document).on('change', '[name=language]', function(){
        var val = $(this).val();
        $('.language').addClass('hide');
        $('.language').removeClass('file');
        $('.'+val).removeClass('hide');
        $('.'+val).addClass('file');
    });
    $('[name=language]').trigger('change');
    $(document).on('change', '[name=type]', function(){
        var val = $(this).val();
        $('.content_text').val('');

        if(val == 'file'){
            $('.content_file').removeClass('hide');
            $('.content_text').addClass('hide');
            $('.content_text').prev().removeClass('hide');
            $('.content_text').closest('.language').removeClass('file');
        }else{
            $('.content_file').addClass('hide');
            $('.content_text').removeClass('hide');
            $('.content_text').prev().addClass('hide');

        }
    });
});
</script>
@endsection
