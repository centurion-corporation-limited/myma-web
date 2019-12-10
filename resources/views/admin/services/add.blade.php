@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">
<style>
.btn-group>.btn:last-child:not(:first-child), .btn-group>.dropdown-toggle:not(:first-child){width:64.41px;}
.file .fancy-file {
	display: none;
}
.fancy-file input[type="file"] {
	width: 100% !important;
	display: block !important;
}
</style>
@endsection
@section('content')

  <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Add</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.services.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="language">Language <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="language" class="form-control" name="language">
                      <option data-class="en" value="english" @if(old('language') == 'english') selected @endif>English</option>
                      <option data-class="bn" value="bengali" @if(old('language') == 'bengali') selected @endif>Bengali</option>
                      <option data-class="mn" value="mandarin" @if(old('language') == 'mandarin') selected @endif>Chinese</option>
                      <option data-class="ta" value="tamil" @if(old('language') == 'tamil') selected @endif>Tamil</option>
                      <option data-class="th" value="thai" @if(old('language') == 'thai') selected @endif>Thai</option>
                  </select>
                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="type" class="form-control" name="type">
                      <!-- <option value="mom">MOM</option> -->
                      <option value="event-news" @if(old('type') == 'event-news') selected @endif>Event & attraction</option>
                      <!-- <option value="custom1" @if(old('type') == 'custom1') selected @endif>Custom1</option> -->
                      <!-- <option value="custmo2" @if(old('type') == 'custom2') selected @endif>Custom2</option> -->
                      <option value="custom3" @if(old('type') == 'custom3') selected @endif>Embassy</option>
                  </select>
                </div>
              </div>

              <div class="form-group hide dorm">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Dormitory
                </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('dormitory_id', $dorm, '', ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group en language">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group en language">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="content" name="content" class="editor form-control col-md-7 col-xs-12">{{ old('content') }}</textarea>
                </div>
              </div>

              <div class="form-group mn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_mn">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_mn" name="title_mn" value="{{ old('title_mn') }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group mn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content_mn">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="content_mn" name="content_mn" class="editor form-control col-md-7 col-xs-12">{{ old('content_mn') }}</textarea>
                </div>
              </div>

              <div class="form-group ta language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_ta">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_ta" name="title_ta" value="{{ old('title_ta') }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group ta language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content_ta">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="content_ta" name="content_ta" class="editor form-control col-md-7 col-xs-12">{{ old('content_ta') }}</textarea>
                </div>
              </div>

              <div class="form-group bn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_bn">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_bn" name="title_bn" value="{{ old('title_bn') }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group bn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content_bn">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="content_bn" name="content_bn" class="editor form-control col-md-7 col-xs-12">{{ old('content_bn') }}</textarea>
                </div>
              </div>

              <div class="form-group th language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_th">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_th" name="title_th" value="{{ old('title_th') }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group th language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content_th">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="content_th" name="content_th" class="editor form-control col-md-7 col-xs-12">{{ old('content_th') }}</textarea>
                </div>
              </div>

              <div class="form-group en language">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author">Author <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" value="{{ old('author') }}" name="author">
                    {{-- Form::select('author_id', $users, '', ['class' => 'form-control']) --}}
                </div>
              </div>

              <div class="form-group mn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_mn">Author <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" value="{{ old('author_mn') }}" name="author_mn">
                </div>
              </div>

              <div class="form-group ta language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_ta">Author <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" value="{{ old('author_ta') }}" name="author_ta">
                </div>
              </div>

              <div class="form-group bn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_bn">Author <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" value="{{ old('author_bn') }}" name="author_bn">
                </div>
              </div>

              <div class="form-group th language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_th">Author <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" value="{{ old('author_th') }}" name="author_th">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Author image</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="file" class="fancy_upload" name="author_image">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Image</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <div class="cropme" style="width: 640px; height: 450px;"></div>
                    <input type="hidden" class="form-control col-md-7 col-xs-12" name="path">
                  <!-- <input type="file" class="col-md-7 col-xs-12" name="image"> -->
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Publish <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div id="gender" class="btn-group" data-toggle="buttons">
                    <label class="btn @if(old('publish') == '1') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="publish" @if(old('publish') == "1") checked @endif value="1"> &nbsp; Yes &nbsp;
                    </label>
                    <label class="btn @if(old('publish') == '0') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="publish" @if(old('publish') == "0") checked @endif value="0"> No
                    </label>
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
<script src="{{ static_file('js/plugins/tinymce/tinymce.min.js') }}"></script>
<script>
$('[name=language]').on('change', function(){
    var value = $('[name=language] option:selected').attr('data-class');
    $('.language').addClass('hide');
    $('.'+value).removeClass('hide');

});


$('[name=publish]').on('change', function(){
    var obj = $(this);
    $('[name=publish]').closest('label').addClass('btn-default').removeClass('btn-primary');
    obj.closest('label').addClass('btn-primary').removeClass('btn-default');
});

$('[name=type]').on('change', function(){
    var value = $(this).val();
    $('.dorm').addClass('hide');
    if(value == 'event-news'){
      $('.dorm').removeClass('hide');
    }
});


$(document).on('ready',function(){
    $('.cropme').simpleCropper();
    $('[name=language]').trigger('change');
    $('[name=type]').trigger('change');
});

tinymce.init({
  selector: '.editor',
  height: 300,
  menubar: false,
  plugins: [
    'advlist autolink lists link image charmap print preview anchor textcolor',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime media table contextmenu paste code help wordcount'
  ],
  toolbar: 'code | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
  // content_css: [
  //   '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
  //   '//www.tinymce.com/css/codepen.min.css']
});
</script>
@endsection
