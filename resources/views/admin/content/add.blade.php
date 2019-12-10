@extends('layouts.admin')

@section('content')
<style>
.url_input + .fancy-file {
	display: none;
}
.url_input.hide + .fancy-file {
	display: block;
}
</style>

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Add Course Content</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.content.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="language">Language <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="language" class="form-control" name="language">
                      <option @if(old('language') == 'english') selected @endif value="english">English</option>
                      <option @if(old('language') == 'bengali') selected @endif value="bengali">Bengali</option>
                      <option @if(old('language') == 'mandarin') selected @endif value="mandarin">Chinese</option>
                      <option @if(old('language') == 'tamil') selected @endif value="tamil">Tamil</option>
                      <option @if(old('language') == 'thai') selected @endif value="thai">Thai</option>
                  </select>
                </div>
              </div>

              <div class="form-group language english @if($course != '' && $course->language != 'english') hide @endif">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ Form::select('course_id', $courses, '', array('class' => 'form-control')) }}
                </div>
              </div>

              <div class="form-group language bengali @if($course == '' || ($course != '' && $course->language != 'bengali')) hide @endif">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ Form::select('course_id_bn', $courses_bn, '', array('class' => 'form-control')) }}
                </div>
              </div>

              <div class="form-group language mandarin @if($course == '' || ($course != '' && $course->language != 'mandarin')) hide @endif">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ Form::select('course_id_mn', $courses_mn, '', array('class' => 'form-control')) }}
                </div>
              </div>

              <div class="form-group language tamil @if($course == '' || ($course != '' && $course->language != 'tamil')) hide @endif">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ Form::select('course_id_ta', $courses_ta, '', array('class' => 'form-control')) }}
                </div>
              </div>

              <div class="form-group language thai @if($course == '' || ($course != '' && $course->language != 'thai')) hide @endif">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ Form::select('course_id_th', $courses_th, '', array('class' => 'form-control')) }}
                </div>
              </div>

              <div class="form-group language english">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-control">
                </div>
              </div>

              <div class="form-group language bengali hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_bn">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_bn" name="title_bn" value="{{ old('title_bn') }}" class="form-control">
                </div>
              </div>

              <div class="form-group language mandarin hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_mn">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_mn" name="title_mn" value="{{ old('title_mn') }}" class="form-control">
                </div>
              </div>

              <div class="form-group language tamil hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_ta">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_ta" name="title_ta" value="{{ old('title_ta') }}" class="form-control">
                </div>
              </div>

              <div class="form-group language thai hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_th">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_th" name="title_th" value="{{ old('title_th') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="role" class="form-control" name="type">
                      <option value="preview" @if(old('type') == 'preview') selected="selected" @endif>Free</option>
                      <option value="paid" @if(old('type') == 'paid') selected="selected" @endif>Paid</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="order">Order</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="order" name="order" value="{{ old('order') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Type</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <label>
                        <input type="radio" name="file_type[1]" value="url" class="upload_input" checked>Youtube
                    </label>
                    <label>
                        <input type="radio" name="file_type[1]" value="upload" class="upload_input">Upload
                    </label>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="path">File</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input placeholder="Youtube video url" type="text" name="path[]" value="{{ old('path') }}" class="form-control url_input">
                  <input type="file" name="path[]" class="hide file_input fancy_upload">
                </div>
              </div>

              <div class="content_div"></div>
              <!-- <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">File/Video</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="file" class="form-control" name="image">
                </div>
              </div> -->

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Add</button>
                  <button type="button" data-id="1" class="btn btn-success add_more">Add More Files/link</button>

                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
@section('scripts')
<script>

$(document).on('change', '.upload_input', function(){
    var val = $(this).val();
    var obj = $(this).closest('.form-group').next();
    if(val == 'url'){
        obj.find('.url_input').removeClass('hide');
        obj.find('.file_input').addClass('hide');
    }else{
        obj.find('.file_input').removeClass('hide');
        obj.find('.url_input').addClass('hide');
    }
});
$(document).on('click', '.remove_label', function(){
    $(this).closest('.added_div').remove();
});

$(document).on('click', '.add_more', function(){
    var id = parseInt($(this).data('id')) + 1;
    $(this).data('id', id);
    var html = '<div class="added_div"> <div class="form-group">'+
      '<label class="control-label col-md-2 col-sm-2 col-xs-12">Type</label>'+
      '<div class="col-md-5 col-sm-9 col-xs-11">'+
          '<label>'+
              '<input type="radio" name="file_type['+id+']" value="url" class="upload_input" checked>Youtube'+
          '</label>'+
          '<label>'+
            '<input type="radio" name="file_type['+id+']" value="upload" class="upload_input">Upload'+
          '</label>'+
      '</div>'+
      '<div class="col-md-1 col-sm-1 col-xs-1 text-right"><a href="javascript:;" class="remove_label"> <i class="fa fa-2x fa-times-circle"></i></a></div>'+
    '</div>'+

    '<div class="form-group">'+
      '<label class="control-label col-md-2 col-sm-2 col-xs-12" for="path">File</label>'+
      '<div class="col-md-6 col-sm-10 col-xs-12">'+
        '<input placeholder="Youtube video url" type="text" name="path[]" value="" class="form-control url_input">'+
        '<input type="file" name="path[]" class="hide file_input fancy_upload">'+
      '</div>'+
    '</div></div>';
    $('.content_div').append(html);
		$('.fancy_upload').fancyfile({
				text  : '',
				// style : 'btn-info',
				placeholder : 'Browse…'
		});

});


$('[name=language]').on('change', function(){
    var value = $(this).val();
    $('.language').addClass('hide');
    $('.'+value).removeClass('hide');

});
</script>
@endsection
