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
            <h2>Add</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.jtc.category.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Language <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('language', $languages, '', ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Main Category <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('center_id', $centers, request('center_id'), ['class' => 'form-control'])!!}
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
                  <!-- <input type="file" id="image" name="image" value="{{ old('image') }}" class="form-control"> -->
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-3 col-sm-3 col-xs-12"></div>
                <div class="col-md-6 col-sm-9 col-xs-12">
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
$(document).on('change', '[name=language]', function(){
    var val = $(this).val();
    $('.language').addClass('hide');
    $('.'+val).removeClass('hide');
});

$('[name=language]').trigger('change');
// $( "#demo-form2" ).validate({
//   rules: {
//     title: {
//         required: function(element) {
//           return $("[name=language]").val() == 'english';
//         }
//     },
//     title_bn: {
//       required: function(element) {
//         return $("[name=language]").val() == 'bengali';
//       }
//     }
//   }
// });
$(document).on('ready',function(){
    $('.cropme').simpleCropper();
});
</script>
@endsection
