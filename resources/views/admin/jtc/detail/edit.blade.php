@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">
<style>
.btn-group>.btn:last-child:not(:first-child), .btn-group>.dropdown-toggle:not(:first-child){width:64.41px;}
</style>
@endsection

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.jtc.detail.edit', encrypt($item->id) ) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="language">Language <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="language" class="form-control" name="language">
                      <option data-class="en" value="english" @if(old('language', $item->language) == 'english') selected @endif>English</option>
                      <option data-class="bn" value="bengali" @if(old('language', $item->language) == 'bengali') selected @endif>Bengali</option>
                      <option data-class="mn" value="mandarin" @if(old('language', $item->language) == 'mandarin') selected @endif>Chinese</option>
                      <option data-class="ta" value="tamil" @if(old('language', $item->language) == 'tamil') selected @endif>Tamil</option>
                      <option data-class="th" value="thai" @if(old('language', $item->language) == 'thai') selected @endif>Thai</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Sub Category (2) <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('event_id', $events, $item->event_id, ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group en language">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="title" value="{{ old('title', $item->lang_en ? $item->lang_en->title : '') }}" class="form-control">
                </div>
              </div>

              <div class="form-group en language">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="content" name="content" class="editor form-control">{{ old('content', $item->lang_en ? $item->lang_en->content : '') }}</textarea>
                </div>
              </div>

              <div class="form-group mn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_mn">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_mn" name="title_mn" value="{{ old('title_mn', $item->lang_mn ? $item->lang_mn->title : '') }}" class="form-control">
                </div>
              </div>

              <div class="form-group mn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content_mn">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="content_mn" name="content_mn" class="editor form-control">{{ old('content_mn', $item->lang_mn ? $item->lang_mn->content : '') }}</textarea>
                </div>
              </div>

              <div class="form-group ta language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_ta">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_ta" name="title_ta" value="{{ old('title_ta', $item->lang_ta ? $item->lang_ta->title : '') }}" class="form-control">
                </div>
              </div>

              <div class="form-group ta language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content_ta">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="content_ta" name="content_ta" class="editor form-control">{{ old('content_ta', $item->lang_ta ? $item->lang_ta->content : '') }}</textarea>
                </div>
              </div>

              <div class="form-group bn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_bn">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_bn" name="title_bn" value="{{ old('title_bn', $item->lang_bn ? $item->lang_bn->title : '') }}" class="form-control">
                </div>
              </div>

              <div class="form-group bn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content_bn">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="content_bn" name="content_bn" class="editor form-control">{{ old('content_bn', $item->lang_bn ? $item->lang_bn->content : '') }}</textarea>
                </div>
              </div>

              <div class="form-group th language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_th">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_th" name="title_th" value="{{ old('title_th', $item->lang_th ? $item->lang_th->title : '') }}" class="form-control">
                </div>
              </div>

              <div class="form-group th language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content_th">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="content_th" name="content_th" class="editor form-control">{{ old('content_th', $item->lang_th ? $item->lang_th->content : '') }}</textarea>
                </div>
              </div>


              <div class="form-group en language">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author">Author <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" class="form-control" value="{{ old('author', $item->lang_en ? $item->lang_en->author : '') }}" name="author">
                    {{-- Form::select('author_id', $users, '', ['class' => 'form-control']) --}}
                </div>
              </div>

              <div class="form-group mn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_mn">Author <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" class="form-control" value="{{ old('author_mn', $item->lang_mn ? $item->lang_mn->author : '') }}" name="author_mn">
                </div>
              </div>

              <div class="form-group ta language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_ta">Author <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" class="form-control" value="{{ old('author_ta', $item->lang_ta ? $item->lang_ta->author : '') }}" name="author_ta">
                </div>
              </div>

              <div class="form-group bn language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_bn">Author <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" class="form-control" value="{{ old('author_bn', $item->lang_bn ? $item->lang_bn->author : '') }}" name="author_bn">
                </div>
              </div>

              <div class="form-group th language hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_th">Author <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" class="form-control" value="{{ old('author_th', $item->lang_th ? $item->lang_th->author : '') }}" name="author_th">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Author image</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="file" class="fancy_upload" name="author_image">
                  @if($item->author_image)
                  <img src="{{ static_file($item->author_image) }}"  />
                  @endif
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Image</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div class="colapsImg">
                    <div class="placehoderImg">
                      <div class="cropme" style="width: 640px; height: 410px;"></div>
                      <input type="hidden" class="form-control" name="path">
                    </div>

                    <div class="orgImg">
                      @if($item->image)
                      <img src= "{{ static_file($item->image) }}" height="410" width="640" style="max-width: inherit;" />
                      @else
                      <div class="cropme" style="width: 640px; height: 410px;"></div>
                      @endif
                    </div>

                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Publish <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div id="gender" class="btn-group" data-toggle="buttons">
                    <label class="btn @if(old('publish', $item->publish ) == '1') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="publish" @if(old('publish', $item->publish ) == '1') checked @endif value="1"> &nbsp; Yes &nbsp;
                    </label>
                    <label class="btn @if(old('publish', $item->publish ) == '0') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="publish" @if(old('publish', $item->publish ) == '0') checked @endif value="0"> No
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
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.Jcrop.js') }}"></script>
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.SimpleCropper.js') }}"></script>

<script src="{{ static_file('js/plugins/tinymce/tinymce.min.js') }}"></script>
<script>
$('[name=publish]').on('change', function(){
    var obj = $(this);
    $('[name=publish]').closest('label').addClass('btn-default').removeClass('btn-primary');
    obj.closest('label').addClass('btn-primary').removeClass('btn-default');
});

$('[name=language]').on('change', function(){
    var value = $('[name=language] option:selected').attr('data-class');

    $('.language').addClass('hide');
    $('.'+value).removeClass('hide');

});
$('[name=language]').trigger('change');

$('[name=type]').on('change', function(){
    var value = $(this).val();
    $('.dorm').addClass('hide');
    if(value == 'event-news'){
      $('.dorm').removeClass('hide');
    }
});


$(document).on('ready',function(){
    $('.cropme').simpleCropper();
    $('[name=type]').trigger('change');
});


tinymce.init({
  selector: '.editor',
  height: 300,
  menubar: "tools",
  forced_root_block : false,
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
