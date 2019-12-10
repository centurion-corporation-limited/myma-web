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
            <h2>Edit Course Content</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.content.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="language">Language <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="language" class="form-control" name="language">
                      <option @if(old('language', $item->language) == 'english') selected="selected" @endif value="english">English</option>
                      <option @if(old('language', $item->language) == 'bengali') selected="selected" @endif value="bengali">Bengali</option>
                      <option @if(old('language', $item->language) == 'mandarin') selected="selected" @endif value="mandarin">Chinese</option>
                      <option @if(old('language', $item->language) == 'tamil') selected="selected" @endif value="tamil">Tamil</option>
                      <option @if(old('language', $item->language) == 'thai') selected="selected" @endif value="thai">Thai</option>
                  </select>
                </div>
              </div>

              <div class="form-group language english">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ Form::select('course_id', $courses, $item->course_id, array('class' => 'form-control')) }}
                </div>
              </div>

              <div class="form-group language bengali hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ Form::select('course_id_bn', $courses_bn, $item->course_id, array('class' => 'form-control')) }}
                </div>
              </div>

              <div class="form-group language mandarin hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ Form::select('course_id_mn', $courses_mn, $item->course_id, array('class' => 'form-control')) }}
                </div>
              </div>

              <div class="form-group language tamil hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ Form::select('course_id_ta', $courses_ta, $item->course_id, array('class' => 'form-control')) }}
                </div>
              </div>

              <div class="form-group language thai hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ Form::select('course_id_th', $courses_th, $item->course_id, array('class' => 'form-control')) }}
                </div>
              </div>

              <div class="form-group language english">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="title" value="{{ old('title', $item->title) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group language bengali hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_bn">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_bn" name="title_bn" value="{{ old('title_bn', $item->title_bn) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group language mandarin hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_mn">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_mn" name="title_mn" value="{{ old('title_mn', $item->title_mn) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group language tamil hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_ta">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_ta" name="title_ta" value="{{ old('title_ta', $item->title_ta) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group language thai hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_th">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_th" name="title_th" value="{{ old('title_th', $item->title_th) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="role" class="form-control" name="type">
                      <option value="preview" @if(old('type', $item->type) == 'preview') selected="selected" @endif>Free</option>
                      <option value="paid" @if(old('type', $item->type) == 'paid') selected="selected" @endif>Paid</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="order">Order</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="order" name="order" value="{{ old('order', $item->order) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>
              <?php $i = 1; ?>
              
              @foreach($item->files as $file)
              <div class="added_div">
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Type</label>
                <div class="col-md-5 col-sm-9 col-xs-11">
                    <label>
                        <input type="hidden" name="id[{{ $i }}]" value="{{ $file->id }}">
                        <input type="radio" name="file_type[{{ $i }}]" @if($file->file_type == 'url') checked @endif value="url" class="upload_input" checked>Youtube
                    </label>
                    <label>
                        <input type="radio" name="file_type[{{ $i }}]" @if($file->file_type == 'upload') checked @endif value="upload" class="upload_input">Upload
                    </label>
                </div>
                @if($i > 1)
                <div class="col-md-1 col-sm-1 col-xs-1"><a href="javascript:;" class="remove_label" data-id={{ $file->id }}> <i class="fa fa-2x fa-times-circle"></i></a></div>
                @endif
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="path">File</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input placeholder="Youtube video url." type="text" name="path[]" value="@if($file->file_type == 'url'){{ $file->path }}@endif" class="@if($file->file_type != 'url') hide @endif form-control url_input ">
                  @if($file->file_type == 'upload' && $file->path != '')
                    <a href="{{ url($file->path) }}" target="_blank" class="col-md-4 file_input">Click to view file! </a>
                  @endif

                  <input type="file" name="path[]" class="@if($file->file_type != 'upload') hide @endif file_input fancy_upload">
                </div>
              </div>
              <?php $i++; ?>
              </div>
              @endforeach
              
              <div class="content_div"></div>

              <!-- <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">File/Video</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="file" class="form-control col-md-7 col-xs-12" name="image">
                </div>
              </div> -->

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Update</button>
                  <button type="button" data-id="{{ $i }}" class="btn btn-success add_more">Add More Files/link</button>
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
$(document).on('ready',function(){

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
        var id = $(this).data('id');
        $.ajax({
            url:'{{ route("ajax.remove.file") }}',
            type: "POST",
            data: {id : id},
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'))
            },
            success: function(data){
                 // var res = JSON.parse(data);
                 console.log(data);
            },
            error: function(data){
                alert("There was an issue while deleting file.");
                console.log(data);
            }
        });

        $(this).closest('.added_div').remove();
    });

    $(document).on('click', '.add_more', function(){
        var id = parseInt($(this).data('id')) + 1;
        $(this).data('id', id);
        var html = '<div class="added_div"><div class="form-group">'+
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
            '<input placeholder="Youtube video url" type="text" name="path[]" value="" class="form-control col-md-7 col-xs-12 url_input">'+
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

        // var content = <?php print_r(json_encode($lang)); ?>;

        // $('#title').val(content[lang]['title']);
        // $('#path').val(content[lang]['path']);

    });
    $('[name=language]').trigger('change');


});

</script>
@endsection
